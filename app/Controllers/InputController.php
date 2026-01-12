<?php

namespace App\Controllers;

class InputController extends BaseController
{
    protected $db;
    protected $helpers = ['form', 'url', 'text'];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('dashboard/input-manual', [
            'title'      => 'Input Manual',
            'page_title' => 'Input Manual',
        ]);
    }

    public function store()
    {
        $input = $this->request->getPost();

        // Trim hanya string
        foreach ($input as $k => $v) {
            if (is_string($v)) $input[$k] = trim($v);
        }

        // ================= VALIDATION =================
        $rules = [
            'no_rab'            => 'required',
            'no_npd'            => 'required',
            'tanggal_pengadaan' => 'required',
            'jenis_perangkat'   => 'required',
            'merk_tipe'         => 'required',
            'serial_number'     => 'required',
            'no_inventaris'     => 'required',
            'unit'              => 'required',
            'label_attached'    => 'required|in_list[Sudah,Belum]',
            'condition'         => 'required|in_list[baik,rusak,dipinjam,disposal,diganti]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // ================= TRANSAKSI DATA UTAMA =================
        $this->db->transBegin();

        try {

            // Lock inventaris
            $exist = $this->db
                ->query("SELECT id FROM assets WHERE asset_code = ? FOR UPDATE", [$input['no_inventaris']])
                ->getRow();

            if ($exist) throw new \Exception("No inventaris sudah digunakan");

            // PROCUREMENT
            $this->db->table('procurements')->insert([
                'no_rab'            => $input['no_rab'],
                'no_npd'            => $input['no_npd'],
                'procurement_date' => $input['tanggal_pengadaan'],
                'notes'            => $input['keterangan'] ?? null,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
            $procurementId = $this->db->insertID();
            if (!$procurementId) throw new \Exception("Gagal simpan procurement");

            // ASSET MODEL
            $model = $this->db->table('asset_models')
                ->where('brand', $input['jenis_perangkat'])
                ->where('model', $input['merk_tipe'])
                ->get()->getRowArray();

            if (!$model) {
                $this->db->table('asset_models')->insert([
                    'brand' => $input['jenis_perangkat'],
                    'model' => $input['merk_tipe'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $assetModelId = $this->db->insertID();
                if (!$assetModelId) throw new \Exception("Gagal simpan model");
            } else {
                $assetModelId = $model['id'];
            }

            // UNIT
            $unit = $this->db->table('units')->where('name', $input['unit'])->get()->getRowArray();
            if (!$unit) {
                $this->db->table('units')->insert([
                    'name' => $input['unit'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $unitId = $this->db->insertID();
                if (!$unitId) throw new \Exception("Gagal simpan unit");
            } else {
                $unitId = $unit['id'];
            }

            // EMPLOYEE
            $employeeId = null;
            if (!empty($input['nipp'])) {
                $emp = $this->db->table('employees')->where('nipp', $input['nipp'])->get()->getRowArray();
                if (!$emp) {
                    $this->db->table('employees')->insert([
                        'nipp' => $input['nipp'],
                        'name' => $input['nama_pengguna'] ?? '',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $employeeId = $this->db->insertID();
                    if (!$employeeId) throw new \Exception("Gagal simpan employee");
                } else {
                    $employeeId = $emp['id'];
                }
            }

            // ASSET
            $this->db->table('assets')->insert([
                'asset_code'      => $input['no_inventaris'],
                'procurement_id' => $procurementId,
                'asset_model_id' => $assetModelId,
                'serial_number'  => $input['serial_number'],
                'purchase_date' => $input['tanggal_pengadaan'],
                'unit_id'        => $unitId,
                'note'           => $input['keterangan'] ?? null,
                'employee_id'    => $employeeId,
                'specification'  => $input['spesifikasi'] ?? null,
                'label_attached' => $input['label_attached'],
                'condition'      => $input['condition'],
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $assetId = $this->db->insertID();
            if (!$assetId) throw new \Exception("Gagal simpan asset");

            $this->db->transCommit();

        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', '[CORE STORE ERROR] '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        // ================= SIMPAN DOKUMEN (STRUKTUR BARU) =================
        try {
            $noBast = isset($input['no_bast_bmc']) ? trim($input['no_bast_bmc']) : null;
            $noWo   = isset($input['no_wo_bast']) ? trim($input['no_wo_bast']) : null;

            $docLink = null;
            if (!empty($input['link_bast']) && filter_var($input['link_bast'], FILTER_VALIDATE_URL)) {
                $docLink = trim($input['link_bast']);
            }

            if (!empty($noBast) || !empty($noWo) || !empty($docLink)) {
                $this->db->table('documents')->insert([
                    'asset_id'       => $assetId,
                    'procurement_id' => $procurementId,
                    'doc_type'       => 'BAST',
                    'doc_number'     => $noBast,
                    'no_wo_bast'     => $noWo,
                    'doc_link'       => $docLink,
                    'uploaded_at'    => date('Y-m-d H:i:s'),
                ]);
            }

        } catch (\Throwable $e) {
            log_message('error', '[DOCUMENT ERROR] '.$e->getMessage());
        }

        return redirect()->to(site_url('input'))->with('success', 'Data berhasil disimpan');
    }
}
