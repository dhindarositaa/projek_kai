<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpExcelDate;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ImportExcel extends Controller
{
    protected $db;
    protected $request;
    protected $validation;

    // required normalized field names
    protected $required = [
        'no_rab','no_npd','tgl_pengadaan',
        'jenis_perangkat','merk_dan_tipe',
        'serial_number','no_inventaris','unit'
    ];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->request = Services::request();
        $this->validation = Services::validation();
        helper(['filesystem','text','url','array']);
    }

    /**
     * Main import endpoint used by your frontend form.
     * POST /import/process (multipart form-data file field named "file")
     */
    public function process()
    {
        // quick check: ensure PhpSpreadsheet class available; try to require composer autoload if not
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            $vendorAutoload = ROOTPATH . 'vendor/autoload.php';
            if (file_exists($vendorAutoload)) {
                @require_once $vendorAutoload;
            }
        }

        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            log_message('error', 'PhpSpreadsheet not found. Please run: composer require phpoffice/phpspreadsheet');
            return $this->respondJSON([
                'status' => 'error',
                'message' => 'Library PhpSpreadsheet tidak ditemukan. Pastikan dependency sudah terpasang (composer require phpoffice/phpspreadsheet).'
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->respondJSON(['status'=>'error','message'=>'File tidak di-upload atau tidak valid.'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Accept only xls/xlsx
        if (!preg_match('/\.(xls|xlsx)$/i', $file->getClientName())) {
            return $this->respondJSON(['status'=>'error','message'=>'Hanya menerima file .xls atau .xlsx'], ResponseInterface::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        // Save temp
        $tmpPath = WRITEPATH . 'uploads/';
        if (!is_dir($tmpPath)) @mkdir($tmpPath, 0755, true);
        $tempName = $file->getRandomName();
        $file->move($tmpPath, $tempName);
        $fullPath = $tmpPath . $tempName;

        // Read spreadsheet
        try {
            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (Throwable $e) {
            log_message('error', 'Gagal membaca file Excel: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            @unlink($fullPath);
            $resp = ['status'=>'error','message'=>'Gagal membaca file Excel: '.$e->getMessage()];
            if (ENVIRONMENT !== 'production') {
                $resp['exception'] = [
                    'type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ];
            }
            return $this->respondJSON($resp, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Detect header
        $headerRowIndex = $this->detectHeaderRow($rows);
        if ($headerRowIndex === null) {
            @unlink($fullPath);
            return $this->respondJSON(['status'=>'error','message'=>'Tidak dapat mendeteksi baris header. Pastikan file memiliki header.'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Normalize headers
        $headers = [];
        foreach ($rows[$headerRowIndex] as $col => $value) {
            $normalized = $this->normalizeHeader((string)$value);
            if ($normalized === '') $normalized = 'col_'.strtolower($col);
            $headers[$col] = $normalized;
        }

        // log any unknown headers
        $logTable = $this->db->table('import_logs');
        $knownKeys = array_values($this->headerSynonymMap());
        foreach ($headers as $col => $name) {
            if (strpos($name, 'col_') === 0) continue;
            if (!in_array($name, $knownKeys, true)) {
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $headerRowIndex,
                    'status' => 'unknown_header',
                    'message' => 'Unmapped header detected: '.$rows[$headerRowIndex][$col]
                ]);
            }
        }

        // counters and log table
        $inserted = $updated = $failed = $conflicts = 0;
        $failedRows = []; // collect failed row details

        // transaction
        $this->db->transStart();

        $maxRow = max(array_keys($rows));
        for ($r = $headerRowIndex + 1; $r <= $maxRow; $r++) {
            $rawRow = $rows[$r] ?? [];
            $mapped = [];
            $allEmpty = true;
            $synMap = $this->headerSynonymMap();
            foreach ($headers as $col => $name) {
                $val = isset($rawRow[$col]) ? trim((string)$rawRow[$col]) : '';
                if ($val !== '') $allEmpty = false;
                $canonical = isset($synMap[$name]) ? $synMap[$name] : $name;
                $mapped[$canonical] = $val;
            }
            if ($allEmpty) continue;

            // validate required
            $missing = [];
            foreach ($this->required as $req) {
                if (empty($mapped[$req])) $missing[] = $req;
            }
            if (!empty($missing)) {
                $failed++;
                $msg = 'Missing required fields: '.implode(', ', $missing);
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => $msg
                ]);
                $failedRows[] = [
                    'row' => $r,
                    'data' => $mapped,
                    'errors' => [$msg],
                ];
                continue;
            }

            // parse date
            $tgl_pengadaan = $this->parseDate($mapped['tgl_pengadaan']);
            if ($tgl_pengadaan === false) {
                $failed++;
                $msg = 'Invalid date format for tgl_pengadaan: ' . ($mapped['tgl_pengadaan'] ?? '');
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => $msg
                ]);
                $failedRows[] = [
                    'row' => $r,
                    'data' => $mapped,
                    'errors' => [$msg],
                ];
                continue;
            }

            // parse brand/model
            $brand_type = $mapped['merk_dan_tipe'];
            $brand = $brand_type;
            $model = $brand_type;
            if (strpos($brand_type, ' - ') !== false) {
                [$brand, $model] = array_map('trim', explode(' - ', $brand_type, 2));
            } elseif (strpos($brand_type, '/') !== false) {
                [$brand, $model] = array_map('trim', explode('/', $brand_type, 2));
            } else {
                $parts = preg_split('/\s+/', $brand_type, 2);
                $brand = $parts[0];
                $model = $parts[1] ?? $parts[0];
            }

            $no_rab = $mapped['no_rab'];
            $no_npd = $mapped['no_npd'];
            $jenis_perangkat = $mapped['jenis_perangkat'];
            $serial = $mapped['serial_number'];
            $asset_code = $mapped['no_inventaris'] ?? ($mapped['no_inventaris_bmn'] ?? null);
            $unit_name = $mapped['unit'];
            $nama = $mapped['nama'] ?? ($mapped['nama_pj'] ?? null);
            $nipp = $mapped['nipp'] ?? null;
            $specs = $mapped['spesifikasi'] ?? null;
            $label = $mapped['sudah_ditempel'] ?? null;
            $no_bast = $mapped['no_bast_bmc'] ?? ($mapped['no_bast'] ?? null);
            $no_wo = $mapped['no_wo_bast'] ?? ($mapped['no_wo'] ?? null);
            $link_file = $mapped['link_file'] ?? null;

            // upsert foreigns
            $procurementId = $this->upsertProcurement($no_rab, $no_npd, $tgl_pengadaan);
            $modelId = $this->upsertAssetModel($brand, $model, $specs);
            $unitId = $this->upsertUnit($unit_name);

            if (empty($procurementId) || empty($modelId)) {
                $failed++;
                $msg = 'Missing procurementId or modelId after upsert (procurementId=' . (int)$procurementId . ', modelId=' . (int)$modelId . ')';
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => $msg
                ]);
                $failedRows[] = [
                    'row' => $r,
                    'data' => $mapped,
                    'errors' => [$msg],
                ];
                continue;
            }

            $employeeId = null;
            if (!empty($nipp) || !empty($nama)) {
                $employeeId = $this->upsertEmployee($nipp, $nama);
            }

            $assetTable = $this->db->table('assets');
            $existingBySerial = $serial ? $assetTable->where('serial_number', $serial)->get()->getRow() : null;
            $existingByCode = $asset_code ? $assetTable->where('asset_code', $asset_code)->get()->getRow() : null;

            if ($existingBySerial && $existingByCode && $existingBySerial->id !== $existingByCode->id) {
                $conflicts++;
                $msg = 'Conflict between existing serial and asset_code';
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'conflict',
                    'message' => $msg
                ]);
                $failedRows[] = [
                    'row' => $r,
                    'data' => $mapped,
                    'errors' => [$msg],
                ];
                continue;
            }

            try {
                if ($existingBySerial) {
                    $assetTable->update([
                        'asset_code' => $asset_code,
                        'procurement_id' => $procurementId,
                        'asset_model_id' => $modelId,
                        'purchase_date' => $tgl_pengadaan,
                        'unit_id' => $unitId,
                        'employee_id' => $employeeId,
                        'specification' => $specs,
                        'label_attached' => $label
                    ], ['id' => $existingBySerial->id]);

                    $updated++;
                    $assetIdToUse = $existingBySerial->id;
                    $logTable->insert([
                        'imported_at'=>date('Y-m-d H:i:s'),
                        'source_file'=>$file->getClientName(),
                        'row_number'=>$r,
                        'status'=>'updated',
                        'message'=>'Updated asset id='.$existingBySerial->id
                    ]);
                } elseif ($existingByCode) {
                    $assetTable->update([
                        'serial_number' => $serial,
                        'procurement_id' => $procurementId,
                        'asset_model_id' => $modelId,
                        'purchase_date' => $tgl_pengadaan,
                        'unit_id' => $unitId,
                        'employee_id' => $employeeId,
                        'specification' => $specs,
                        'label_attached' => $label
                    ], ['id' => $existingByCode->id]);

                    $updated++;
                    $assetIdToUse = $existingByCode->id;
                    $logTable->insert([
                        'imported_at'=>date('Y-m-d H:i:s'),
                        'source_file'=>$file->getClientName(),
                        'row_number'=>$r,
                        'status'=>'updated',
                        'message'=>'Updated asset id='.$existingByCode->id
                    ]);
                } else {
                    $assetTable->insert([
                        'asset_code' => $asset_code,
                        'procurement_id' => $procurementId,
                        'asset_model_id' => $modelId,
                        'serial_number' => $serial,
                        'purchase_date' => $tgl_pengadaan,
                        'unit_id' => $unitId,
                        'employee_id' => $employeeId,
                        'specification' => $specs,
                        'label_attached' => $label
                    ]);
                    $assetIdToUse = $this->db->insertID();
                    $inserted++;
                    $logTable->insert([
                        'imported_at'=>date('Y-m-d H:i:s'),
                        'source_file'=>$file->getClientName(),
                        'row_number'=>$r,
                        'status'=>'inserted',
                        'message'=>'Inserted asset id='.$assetIdToUse
                    ]);
                }
            } catch (Throwable $e) {
                $failed++;
                $msg = 'DB error while inserting/updating: '. $e->getMessage();
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => $msg
                ]);
                $failedRows[] = [
                    'row' => $r,
                    'data' => $mapped,
                    'errors' => [$msg],
                ];
                continue;
            }

            // documents
            if (!empty($no_bast) || !empty($no_wo) || !empty($link_file)) {
                $docTable = $this->db->table('documents');
                $docTable->insert([
                    'asset_id' => $assetIdToUse,
                    'procurement_id' => $procurementId,
                    'doc_type' => 'BAST/WO/FILE',
                    'doc_number' => ($no_bast ?? $no_wo),
                    'doc_link' => $link_file,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);
            }
        } // end loop rows

        $this->db->transComplete();

        // remove temp file
        @unlink($fullPath);

        $summary = [
            'inserted' => $inserted,
            'updated' => $updated,
            'failed' => $failed,
            'conflicts' => $conflicts,
        ];

        // include failed row details to help debugging
        return $this->respondJSON(['status'=>'success','summary'=>$summary,'failed_rows'=>$failedRows]);
    }

    /**
     * Export import_logs CSV
     */
    public function downloadLogs()
    {
        $builder = $this->db->table('import_logs');
        $rows = $builder->orderBy('imported_at','DESC')->get()->getResultArray();

        $filename = 'import_logs_'.date('Ymd_His').'.csv';
        $fh = fopen('php://memory','w');
        fputcsv($fh, ['id','imported_at','source_file','row_number','status','message']);
        foreach ($rows as $r) {
            fputcsv($fh, [$r['id'],$r['imported_at'],$r['source_file'],$r['row_number'],$r['status'],$r['message']]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response->setHeader('Content-Type','text/csv')
                              ->setHeader('Content-Disposition','attachment; filename="'.$filename.'"')
                              ->setBody($csv);
    }

    // ---------------- Helper Methods ----------------

    /**
     * Heuristik mencari header row dari array hasil toArray()
     * Mengembalikan numeric index baris (sesuai key $rows) atau null kalau tidak ketemu
     */
    protected function detectHeaderRow(array $rows, int $maxCheck = 10)
    {
        $keys = array_keys($rows);
        $maxToCheck = min($maxCheck, count($keys));
        for ($i = 0; $i < $maxToCheck; $i++) {
            $rowIndex = $keys[$i];
            $row = $rows[$rowIndex];
            if ($this->rowLooksLikeHeader($row)) {
                return $rowIndex;
            }
        }
        // fallback: pertama baris non-empty
        foreach ($rows as $idx => $r) {
            if (array_filter($r, function($v){ return trim((string)$v) !== ''; })) {
                return $idx; // fallback
            }
        }
        return null;
    }

    /**
     * Menilai apakah satu baris tampak seperti header (mengandung banyak token teks)
     */
    protected function rowLooksLikeHeader($row): bool
    {
        $nonEmpty = 0;
        $textTokens = 0;
        foreach ($row as $cell) {
            $val = trim((string)$cell);
            if ($val === '') continue;
            $nonEmpty++;
            if (preg_match('/[A-Za-z\p{L}]/u', $val)) $textTokens++;
            if (strlen($val) <= 40 && preg_match('/^[\p{L}\p{N}_\s\-\/]+$/u', $val)) $textTokens++;
        }
        if ($nonEmpty === 0) return false;
        return ($textTokens >= max(1, intval($nonEmpty * 0.4)));
    }

    /**
     * Map semua kemungkinan header ke canonical key yang dipakai di proses
     * key = normalized header (hasil normalizeHeader), value = canonical field name
     */
    protected function headerSynonymMap(): array
    {
        return [
            'no_rab' => 'no_rab',
            'no rab' => 'no_rab',
            'norab' => 'no_rab',

            'no_npd' => 'no_npd',
            'no npd' => 'no_npd',
            'nonpd' => 'no_npd',

            'tgl_pengadaan' => 'tgl_pengadaan',
            'tanggal_pengadaan' => 'tgl_pengadaan',
            'tgl pembelian' => 'tgl_pengadaan',

            'jenis_perangkat' => 'jenis_perangkat',
            'jenis perangkat' => 'jenis_perangkat',
            'type' => 'jenis_perangkat',

            'merk_dan_tipe' => 'merk_dan_tipe',
            'merk & tipe' => 'merk_dan_tipe',
            'merk tipe' => 'merk_dan_tipe',
            'merk' => 'merk_dan_tipe',

            'serial_number' => 'serial_number',
            'serial number' => 'serial_number',
            'sn' => 'serial_number',

            'no_inventaris' => 'no_inventaris',
            'no inventaris' => 'no_inventaris',
            'kode_barang' => 'no_inventaris',
            'asset_code' => 'no_inventaris',

            'unit' => 'unit',
            'unit kerja' => 'unit',

            'nama' => 'nama',
            'nama_pj' => 'nama_pj',
            'nama penanggung jawab' => 'nama_pj',

            'nipp' => 'nipp',
            'nip' => 'nipp',

            'spesifikasi' => 'spesifikasi',
            'spec' => 'spesifikasi',
            'specs' => 'spesifikasi',

            'sudah_ditempel' => 'sudah_ditempel',
            'label' => 'sudah_ditempel',

            'no_bast' => 'no_bast_bmc',
            'no_bast_bmc' => 'no_bast_bmc',

            'no_wo' => 'no_wo_bast',
            'no_wo_bast' => 'no_wo_bast',

            'link_file' => 'link_file',
        ];
    }

    /**
     * Normalize header: lower, remove diacritics, replace non-alnum dengan space, compress spaces
     * Return bentuk kunci yang mudah dibandingkan
     */
    protected function normalizeHeader(string $header): string
    {
        $h = trim(mb_strtolower($header));
        $h = preg_replace('/[\x00-\x1F\x7F]/u', '', $h);
        $h = preg_replace('/[^\p{L}\p{Nd}_]+/u', ' ', $h);
        $h = preg_replace('/\s+/', ' ', $h);
        $h = trim($h);
        $map = $this->headerSynonymMap();
        if (isset($map[$h])) return $map[$h];
        $noSpace = str_replace(' ', '', $h);
        if (isset($map[$noSpace])) return $map[$noSpace];
        $under = str_replace(' ', '_', $h);
        if (isset($map[$under])) return $map[$under];
        return $under;
    }

    /**
     * Parse tanggal yang mungkin berupa:
     * - Excel serial number (numeric)
     * - dd/mm/yyyy atau dd-mm-yyyy
     * - yyyy-mm-dd
     * Return yyyy-mm-dd string or false jika gagal
     */
    protected function parseDate($value)
    {
        $v = trim((string)$value);
        if ($v === '') return false;

        // numeric Excel serial
        if (is_numeric($v)) {
            try {
                $dt = PhpExcelDate::excelToDateTimeObject((float)$v);
                return $dt->format('Y-m-d');
            } catch (Throwable $e) {
                // fallback
            }
        }

        // try several formats
        $formats = ['d/m/Y','d-m-Y','Y-m-d','Y/m/d','d M Y','j M Y'];
        foreach ($formats as $f) {
            $d = \DateTime::createFromFormat($f, $v);
            if ($d && $d->format($f) === $v) {
                return $d->format('Y-m-d');
            }
        }

        // try strtotime
        $ts = strtotime($v);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return false;
    }

    /**
     * Upsert procurement: cari berdasarkan no_rab & no_npd, jika ada return id, jika tidak insert
     */
    protected function upsertProcurement($no_rab, $no_npd, $tgl)
    {
        $tbl = $this->db->table('procurements');
        $row = $tbl->where('no_rab', $no_rab)->where('no_npd', $no_npd)->get()->getRow();
        if ($row) return $row->id;
        $tbl->insert([
            'no_rab' => $no_rab,
            'no_npd' => $no_npd,
            'procurement_date' => $tgl,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return $this->db->insertID();
    }

    /**
     * Upsert asset model
     */
    protected function upsertAssetModel($brand, $model, $specs = null)
    {
        $tbl = $this->db->table('asset_models');
        $row = $tbl->where('brand', $brand)->where('model', $model)->get()->getRow();
        if ($row) return $row->id;

        $data = [
            'brand' => $brand,
            'model' => $model,
        ];

        if ($specs !== null && $this->db->fieldExists('specs', 'asset_models')) {
            $data['specs'] = $specs;
        }

        if ($this->db->fieldExists('created_at', 'asset_models')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $tbl->insert($data);
        return $this->db->insertID();
    }

    /**
     * Upsert unit
     */
    protected function upsertUnit($name)
    {
        $name = trim((string)$name);
        if ($name === '') return null;
        $tbl = $this->db->table('units');
        $row = $tbl->where('name', $name)->get()->getRow();
        if ($row) return $row->id;
        $tbl->insert(['name' => $name, 'created_at' => date('Y-m-d H:i:s')]);
        return $this->db->insertID();
    }

    /**
     * Upsert employee by nipp or name (simple)
     */
    protected function upsertEmployee($nipp = null, $name = null)
    {
        $nipp = trim((string)$nipp);
        $name = trim((string)$name);
        $tbl = $this->db->table('employees');
        if ($nipp !== '') {
            $row = $tbl->where('nipp', $nipp)->get()->getRow();
            if ($row) return $row->id;
        }
        if ($name !== '') {
            $row = $tbl->where('name', $name)->get()->getRow();
            if ($row) return $row->id;
        }
        // insert
        $data = ['name' => $name ?: null, 'nipp' => $nipp ?: null, 'created_at' => date('Y-m-d H:i:s')];
        $tbl->insert($data);
        return $this->db->insertID();
    }

    protected function respondJSON($data, int $code = ResponseInterface::HTTP_OK)
    {
        return $this->response->setStatusCode($code)
                              ->setHeader('Content-Type','application/json')
                              ->setBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}