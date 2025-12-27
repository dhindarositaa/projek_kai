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
        $data = [
            'title'      => 'Input Manual',
            'page_title' => 'Input Manual',
            'show_stats' => true,
            'stats' => $this->getStats(),
        ];

        return view('dashboard/input-manual', $data);
    }
    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            // Wajib
            'no_rab'            => 'required',
            'no_npd'            => 'required',
            'tanggal_pengadaan' => 'required|valid_date',
            'jenis_perangkat'   => 'required',
            'merk_tipe'         => 'required',
            'serial_number'     => 'required',
            'no_inventaris'     => 'required',
            'unit'              => 'required',
            'condition'         => 'required|in_list[baik,rusak,dipinjam,disposal,diganti]',

            // Opsional
            'no_bast_bmc'       => 'permit_empty',
            'no_wo_bast'        => 'permit_empty',
            'link_bast'         => 'permit_empty|valid_url',
            'spesifikasi'       => 'permit_empty',
            'link_dokumen'      => 'permit_empty|valid_url',
            'nama_pengguna'     => 'permit_empty',
            'nipp'              => 'permit_empty',
            'keterangan'        => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }
        $exists = $this->db->table('assets')
            ->where('asset_code', $input['no_inventaris'])
            ->countAllResults();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', [
                'no_inventaris' => 'No Inventaris sudah dipakai.'
            ]);
        }

        $this->db->transStart();

        try {
            $procData = [
                'no_rab'           => $input['no_rab'],
                'no_npd'           => $input['no_npd'],
                'procurement_date' => $input['tanggal_pengadaan'],
                'notes'            => $input['keterangan'] ?? null,
                'created_at'       => date('Y-m-d H:i:s'),
            ];
            $this->db->table('procurements')->insert($procData);
            $procurementId = (int) $this->db->insertID();
            $assetModelsTable = $this->db->table('asset_models');
            $assetModelRow = $assetModelsTable
                ->where('brand', $input['jenis_perangkat'])
                ->where('model', $input['merk_tipe'])
                ->get()
                ->getRowArray();

            if ($assetModelRow) {
                $assetModelId = (int) $assetModelRow['id'];
            } else {
                $am = [
                    'brand'      => $input['jenis_perangkat'],
                    'model'      => $input['merk_tipe'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->table('asset_models')->insert($am);
                $assetModelId = (int) $this->db->insertID();
            }
            $unitRow = $this->db->table('units')
                ->where('name', $input['unit'])
                ->get()
                ->getRowArray();

            if ($unitRow) {
                $unitId = (int) $unitRow['id'];
            } else {
                $u = [
                    'name'       => $input['unit'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->table('units')->insert($u);
                $unitId = (int) $this->db->insertID();
            }
            $employeeId = null;
            if (!empty($input['nipp'])) {
                $empRow = $this->db->table('employees')
                    ->where('nipp', $input['nipp'])
                    ->get()
                    ->getRowArray();

                if ($empRow) {
                    $employeeId = (int) $empRow['id'];
                    if (!empty($input['nama_pengguna']) && $empRow['name'] !== $input['nama_pengguna']) {
                        $this->db->table('employees')
                            ->where('id', $employeeId)
                            ->update([
                                'name' => $input['nama_pengguna'],
                            ]);
                    }
                } else {
                    $emp = [
                        'nipp'       => $input['nipp'],
                        'name'       => $input['nama_pengguna'] ?? '',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->table('employees')->insert($emp);
                    $employeeId = (int) $this->db->insertID();
                }
            }
            $assetPayload = [
                'asset_code'     => $input['no_inventaris'],
                'procurement_id' => $procurementId,
                'asset_model_id' => $assetModelId,
                'serial_number'  => $input['serial_number'],
                'purchase_date'  => $input['tanggal_pengadaan'],
                'unit_id'        => $unitId,
                'employee_id'    => $employeeId,
                'specification'  => $input['spesifikasi'] ?? null,
                'label_attached' => 'Belum', 
                'condition'      => $input['condition'],
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            $this->db->table('assets')->insert($assetPayload);
            $assetId = (int) $this->db->insertID();
            $docNumberParts = [];
            if (!empty($input['no_bast_bmc'])) {
                $docNumberParts[] = 'BAST: '.$input['no_bast_bmc'];
            }
            if (!empty($input['no_wo_bast'])) {
                $docNumberParts[] = 'WO: '.$input['no_wo_bast'];
            }
            $docNumber = $docNumberParts ? implode(' | ', $docNumberParts) : null;
            $docLink = null;
            if (!empty($input['link_bast'])) {
                $docLink = $input['link_bast'];
            } elseif (!empty($input['link_dokumen'])) {
                $docLink = $input['link_dokumen'];
            }

            if ($docNumber || $docLink) {
                $docPayload = [
                    'asset_id'       => $assetId,
                    'procurement_id' => $procurementId,
                    'doc_type'       => 'BAST/WO/FILE',
                    'doc_number'     => $docNumber,
                    'doc_link'       => $docLink,
                    'uploaded_at'    => date('Y-m-d H:i:s'),
                    'created_at'     => date('Y-m-d H:i:s'),
                ];
                $this->db->table('documents')->insert($docPayload);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $dbError = $this->db->error();
                return redirect()->back()->withInput()->with(
                    'error',
                    'Gagal menyimpan data (transaksi). DB error: ' . ($dbError['message'] ?? 'unknown')
                );
            }

            return redirect()->to(site_url('input'))
                ->with('success', 'Data berhasil ditambahkan melalui input manual.');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', '[InputController::store] Exception: ' . $e->getMessage());
            $dbError = $this->db->error();
            $msg     = $e->getMessage();
            if (!empty($dbError['message'])) {
                $msg .= ' | DB: ' . $dbError['message'];
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $msg);
        }
    }
}
