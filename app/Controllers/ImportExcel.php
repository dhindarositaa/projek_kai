<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;
<<<<<<< HEAD
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpExcelDate;
use CodeIgniter\HTTP\ResponseInterface;
=======
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpExcelDate;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8

class ImportExcel extends Controller
{
    protected $db;
    protected $request;
    protected $validation;

<<<<<<< HEAD
    // required normalized field names
=======
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
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

<<<<<<< HEAD
    /**
     * Main import endpoint used by your frontend form.
     * POST /import/process (multipart form-data file field named "file")
     */
    public function process()
    {
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
        if (!is_dir($tmpPath)) mkdir($tmpPath, 0755, true);
        $tempName = $file->getRandomName();
        $file->move($tmpPath, $tempName);
        $fullPath = $tmpPath . $tempName;

        // Read spreadsheet
        try {
            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            @unlink($fullPath);
            return $this->respondJSON(['status'=>'error','message'=>'Gagal membaca file Excel: '.$e->getMessage()], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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

        // counters and log table
        $inserted = $updated = $failed = $conflicts = 0;
        $logTable = $this->db->table('import_logs');

        // transaction
        $this->db->transStart();

        $maxRow = max(array_keys($rows));
        for ($r = $headerRowIndex + 1; $r <= $maxRow; $r++) {
            $rawRow = $rows[$r] ?? [];
            $mapped = [];
            $allEmpty = true;
            foreach ($headers as $col => $name) {
                $val = isset($rawRow[$col]) ? trim((string)$rawRow[$col]) : '';
                if ($val !== '') $allEmpty = false;
                $mapped[$name] = $val;
            }
            if ($allEmpty) continue;

            // validate required
            $missing = [];
            foreach ($this->required as $req) {
                if (empty($mapped[$req])) $missing[] = $req;
            }
            if (!empty($missing)) {
                $failed++;
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => 'Missing required fields: '.implode(', ', $missing)
                ]);
                continue;
            }

            // parse fields
            $no_rab = $mapped['no_rab'];
            $no_npd = $mapped['no_npd'];
            $tgl_pengadaan = $this->parseDate($mapped['tgl_pengadaan']);
            if (!$tgl_pengadaan) {
                $failed++;
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'failed',
                    'message' => 'Invalid date format for tgl_pengadaan'
                ]);
                continue;
            }

            $jenis_perangkat = $mapped['jenis_perangkat'];
            $brand_type = $mapped['merk_dan_tipe'];
            // split brand & model
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

            $serial = $mapped['serial_number'];
            $asset_code = $mapped['no_inventaris'];
            $unit_name = $mapped['unit'];
            $nama = $mapped['nama'] ?? null;
            $nipp = $mapped['nipp'] ?? null;
            $specs = $mapped['spesifikasi'] ?? null;
            $label = $mapped['sudah_ditempel'] ?? null;
            $no_bast = $mapped['no_bast_bmc'] ?? ($mapped['no_bast'] ?? null);
            $no_wo = $mapped['no_wo_bast'] ?? ($mapped['no_wo'] ?? null);
            $link_file = $mapped['link_file'] ?? null;

            // upsert procurement
            $procurementId = $this->upsertProcurement($no_rab, $no_npd, $tgl_pengadaan);

            // upsert model
            $modelId = $this->upsertAssetModel($brand, $model, $specs);

            // upsert unit
            $unitId = $this->upsertUnit($unit_name);

            // upsert employee optional
            $employeeId = null;
            if (!empty($nipp) || !empty($nama)) {
                $employeeId = $this->upsertEmployee($nipp, $nama);
            }

            // check existing assets
            $assetTable = $this->db->table('assets');
            $existingBySerial = $assetTable->where('serial_number', $serial)->get()->getRow();
            $existingByCode = $assetTable->where('asset_code', $asset_code)->get()->getRow();

            if ($existingBySerial && $existingByCode && $existingBySerial->id !== $existingByCode->id) {
                $conflicts++;
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'conflict',
                    'message' => 'Conflict between existing serial and asset_code'
                ]);
                continue;
            }

            // upsert asset
=======
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

    if (!preg_match('/\.(xls|xlsx)$/i', $file->getClientName())) {
        return $this->respondJSON(['status'=>'error','message'=>'Hanya menerima file .xls atau .xlsx'], ResponseInterface::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    $tmpPath = WRITEPATH . 'uploads/';
    if (!is_dir($tmpPath)) @mkdir($tmpPath, 0755, true);
    $tempName = $file->getRandomName();
    $file->move($tmpPath, $tempName);
    $fullPath = $tmpPath . $tempName;

    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
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

    $headerRowIndex = $this->detectHeaderRow($rows);
    if ($headerRowIndex === null) {
        @unlink($fullPath);
        return $this->respondJSON(['status'=>'error','message'=>'Tidak dapat mendeteksi baris header. Pastikan file memiliki header.'], ResponseInterface::HTTP_BAD_REQUEST);
    }

    $headers = [];
    foreach ($rows[$headerRowIndex] as $col => $value) {
        $normalized = $this->normalizeHeader((string)$value);
        if ($normalized === '') $normalized = 'col_'.strtolower($col);
        $headers[$col] = $normalized;
    }

    $logTable = $this->db->table('import_logs');
    $knownKeys = array_keys($this->headerSynonymMap());
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

    $inserted = $updated = $failed = $conflicts = 0;
    $failedRows = []; // <-- kumpulkan detail baris gagal

    $this->db->transStart();

    $maxRow = max(array_keys($rows));
    for ($r = $headerRowIndex + 1; $r <= $maxRow; $r++) {
        $rawRow = $rows[$r] ?? [];
        $mapped = [];
        $allEmpty = true;
        foreach ($headers as $col => $name) {
            $val = isset($rawRow[$col]) ? trim((string)$rawRow[$col]) : '';
            if ($val !== '') $allEmpty = false;
            $synMap = $this->headerSynonymMap();
            $canonical = isset($synMap[$name]) ? $synMap[$name] : $name;
            $mapped[$canonical] = $val;
        }
        if ($allEmpty) continue;

        // REQUIRED check
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

            // simpan detail kegagalan
            $failedRows[] = [
                'row' => $r,
                'data' => $mapped,
                'errors' => [$msg],
            ];
            continue;
        }

        // parse date
        $tgl_pengadaan = $this->parseDate($mapped['tgl_pengadaan']);
        if (!$tgl_pengadaan) {
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

        // parse brand/model/specs
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
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
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
<<<<<<< HEAD

                $updated++;
                $assetIdToUse = $existingBySerial->id;
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'updated',
                    'message' => 'Updated asset id='.$existingBySerial->id
                ]);
=======
                $updated++;
                $assetIdToUse = $existingBySerial->id;
                $logTable->insert(['imported_at'=>date('Y-m-d H:i:s'),'source_file'=>$file->getClientName(),'row_number'=>$r,'status'=>'updated','message'=>'Updated asset id='.$existingBySerial->id]);
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
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
<<<<<<< HEAD

                $updated++;
                $assetIdToUse = $existingByCode->id;
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'updated',
                    'message' => 'Updated asset id='.$existingByCode->id
                ]);
=======
                $updated++;
                $assetIdToUse = $existingByCode->id;
                $logTable->insert(['imported_at'=>date('Y-m-d H:i:s'),'source_file'=>$file->getClientName(),'row_number'=>$r,'status'=>'updated','message'=>'Updated asset id='.$existingByCode->id]);
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
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
<<<<<<< HEAD
                $logTable->insert([
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'inserted',
                    'message' => 'Inserted asset id='.$assetIdToUse
                ]);
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
        return $this->respondJSON(['status'=>'success','summary'=>$summary]);
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

    protected function detectHeaderRow(array $rows)
    {
        foreach ($rows as $idx => $row) {
            $nonEmpty = 0;
            $content = '';
            foreach ($row as $cell) {
                if ($cell !== null && trim((string)$cell) !== '') $nonEmpty++;
                $content .= ' '.(string)$cell;
            }
            if ($nonEmpty >= 3 && $this->rowLooksLikeHeader($content)) {
                return $idx;
            }
        }
        return array_key_first($rows);
    }

    protected function rowLooksLikeHeader($text)
    {
        $keywords = ['no rab','no npd','tgl','merk','serial','inventaris','unit'];
        $lower = strtolower($text);
        $count = 0;
        foreach ($keywords as $k) { if (strpos($lower,$k) !== false) $count++; }
        return $count >= 2;
    }

    protected function normalizeHeader(string $h)
    {
        $h = trim($h);
        $h = strtolower($h);
        $h = preg_replace('/[^\p{L}\p{Nd}]+/u', '_', $h);
        $h = preg_replace('/_+/', '_', $h);
        $h = trim($h, '_');

        $map = [
            'no_rab' => ['no rab','no_rab','rab'],
            'no_npd' => ['no npd','no_npd','npd'],
            'tgl_pengadaan' => ['tgl pengadaan','tanggal pengadaan','tgl_pengadaan','tanggal'],
            'jenis_perangkat' => ['jenis perangkat','perangkat','type','jenis'],
            'merk_dan_tipe' => ['merk dan tipe','brand series','merk_tipe','merk'],
            'serial_number' => ['serial number','serial_number','serial'],
            'no_inventaris' => ['no inventaris','no_inventaris','inventaris','asset_code'],
            'unit' => ['unit','lokasi','unit_kerja'],
            'nama' => ['nama','penanggung jawab','petugas'],
            'nipp' => ['nipp','nip'],
            'spesifikasi' => ['spesifikasi','specs','spec'],
            'sudah_ditempel' => ['sudah ditempel','label'],
            'no_bast_bmc' => ['no bast bmc','no_bast_bmc','no_bast'],
            'no_wo_bast' => ['no wo bast','no_wo_bast','no_wo'],
            'link_file' => ['link file','link_file','file']
        ];

        foreach ($map as $key=>$syns) {
            foreach ($syns as $s) {
                $skey = preg_replace('/[^a-z0-9_]+/','',$s);
                if ($h === $skey) return $key;
            }
        }

        foreach ($map as $key=>$syns) {
            foreach ($syns as $s) {
                if (strpos($h, str_replace(' ','_', $s)) !== false) return $key;
            }
        }

        return $h;
    }

    protected function parseDate($value)
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) {
            try {
                $dt = PhpExcelDate::excelToDateTimeObject($value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        $d = date_create($value);
        if ($d === false) return null;
        return $d->format('Y-m-d');
    }

    // ---------------- Upsert helpers ----------------

    protected function upsertProcurement($no_rab, $no_npd, $date)
    {
        $t = $this->db->table('procurements');
        $row = $t->where('no_rab', $no_rab)->where('no_npd', $no_npd)->get()->getRow();
        if ($row) return $row->id;
        $t->insert(['no_rab'=>$no_rab,'no_npd'=>$no_npd,'procurement_date'=>$date]);
        return $this->db->insertID();
    }

    protected function upsertAssetModel($brand, $model, $specs=null)
    {
        $t = $this->db->table('asset_models');
        $row = $t->where('brand', $brand)->where('model', $model)->get()->getRow();
        if ($row) return $row->id;
        $t->insert(['brand'=>$brand,'model'=>$model,'specs'=>$specs]);
        return $this->db->insertID();
    }

    protected function upsertUnit($name)
    {
        $t = $this->db->table('units');
        $row = $t->where('name', $name)->get()->getRow();
        if ($row) return $row->id;
        $t->insert(['name'=>$name]);
        return $this->db->insertID();
    }

    protected function upsertEmployee($nipp, $name)
    {
        $t = $this->db->table('employees');
        if (!empty($nipp)) {
            $row = $t->where('nipp',$nipp)->get()->getRow();
            if ($row) return $row->id;
        } else {
            $row = $t->where('name',$name)->get()->getRow();
            if ($row) return $row->id;
        }
        $t->insert(['nipp'=>$nipp,'name'=>$name]);
=======
                $logTable->insert(['imported_at'=>date('Y-m-d H:i:s'),'source_file'=>$file->getClientName(),'row_number'=>$r,'status'=>'inserted','message'=>'Inserted asset id='.$assetIdToUse]);
            }
        } catch (\Throwable $e) {
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
    }

    $this->db->transComplete();

    @unlink($fullPath);

    $summary = [
        'inserted' => $inserted,
        'updated' => $updated,
        'failed' => $failed,
        'conflicts' => $conflicts,
    ];

    // sertakan detail baris yang gagal
    return $this->respondJSON(['status'=>'success','summary'=>$summary,'failed_rows'=>$failedRows]);
}


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
        // juga coba cek baris pertama non-empty kalau heuristik gagal
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
    protected function rowLooksLikeHeader(array $row): bool
    {
        $nonEmpty = 0;
        $textTokens = 0;
        foreach ($row as $cell) {
            $val = trim((string)$cell);
            if ($val === '') continue;
            $nonEmpty++;
            // jika cell mengandung huruf (label biasanya mengandung huruf)
            if (preg_match('/[A-Za-z\p{L}]/u', $val)) $textTokens++;
            // jika ada spasi dan panjang pendek, anggap label
            if (strlen($val) <= 40 && preg_match('/^[\p{L}\p{N}_\s\-\/]+$/u', $val)) $textTokens++;
        }
        if ($nonEmpty === 0) return false;
        // heuristik: setidaknya 40% sel non-empty terlihat seperti label teks
        return ($textTokens >= max(1, intval($nonEmpty * 0.4)));
    }

    /**
     * Map semua kemungkinan header ke canonical key yang dipakai di proses
     * key = normalized header (hasil normalizeHeader), value = canonical field name
     */
    protected function headerSynonymMap(): array
    {
        return [
            // canonical => canonical (so keys searchable)
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
        // remove BOM and weird chars
        $h = preg_replace('/[\x00-\x1F\x7F]/u', '', $h);
        // replace non-alnum (except underscore) with space
        $h = preg_replace('/[^\p{L}\p{Nd}_]+/u', ' ', $h);
        $h = preg_replace('/\s+/', ' ', $h);
        $h = trim($h);
        // map via synonym map keys: find best match
        $map = $this->headerSynonymMap();
        // try direct match
        if (isset($map[$h])) return $map[$h];
        // try removing spaces
        $noSpace = str_replace(' ', '', $h);
        if (isset($map[$noSpace])) return $map[$noSpace];
        // try with underscores
        $under = str_replace(' ', '_', $h);
        if (isset($map[$under])) return $map[$under];
        // otherwise return as-is slug
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
            } catch (\Exception $e) {
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
     * Updated: sesuai migration kamu, kolom specs digunakan (bukan 'specification')
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

        // migration kamu menggunakan 'specs' sebagai nama kolom
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
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
        return $this->db->insertID();
    }

    protected function respondJSON($data, int $code = ResponseInterface::HTTP_OK)
    {
<<<<<<< HEAD
=======
        // ensure JSON_UNESCAPED_UNICODE for readability
>>>>>>> e35b68859d7702e40b03f6df3be5d28334f0b1f8
        return $this->response->setStatusCode($code)
                              ->setHeader('Content-Type','application/json')
                              ->setBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
