<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpExcelDate;
use CodeIgniter\HTTP\ResponseInterface;

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
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'updated',
                    'message' => 'Updated asset id='.$existingBySerial->id
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
                    'imported_at' => date('Y-m-d H:i:s'),
                    'source_file' => $file->getClientName(),
                    'row_number' => $r,
                    'status' => 'updated',
                    'message' => 'Updated asset id='.$existingByCode->id
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
        return $this->db->insertID();
    }

    protected function respondJSON($data, int $code = ResponseInterface::HTTP_OK)
    {
        return $this->response->setStatusCode($code)
                              ->setHeader('Content-Type','application/json')
                              ->setBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
