<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetsModel;
use App\Models\DocumentsModel;
use App\Models\ProcurementsModel;
use App\Models\EmployeesModel;
use CodeIgniter\I18n\Time;

class Assets extends BaseController
{
    protected $assetsModel;
    protected $db;
    protected $helpers = ['form', 'url', 'text'];

    public function __construct()
    {
        $this->assetsModel = new AssetsModel();
        $this->db          = \Config\Database::connect();
    }
    public function index()
    {
        $perPage = (int)($this->request->getGet('perPage') ?? 10);
        if (! in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $page = (int)($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $offset    = ($page - 1) * $perPage;
        $search    = $this->request->getGet('q');
        $condition = $this->request->getGet('condition');
        $assets = $this->assetsModel->getAssetsWithRelations(
            $perPage,
            $offset,
            $search,
            $condition
        );
        $allFiltered = $this->assetsModel->getAssetsWithRelations(
            null,
            null,
            $search,
            $condition
        );
        $total = count($allFiltered);

        $no = $offset + 1;

        $pager = [
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];

        return view('dashboard/barang', [
            'title'  => 'Daftar Semua Barang',
            'assets' => $assets,
            'pager'  => $pager,
            'no'     => $no,
        ]);
    }

    public function create()
    {
        $data = [
            'title'        => 'Tambah Barang',
            'units'        => $this->db->table('units')->orderBy('name')->get()->getResultArray(),
            'asset_models' => $this->db->table('asset_models')->orderBy('brand')->get()->getResultArray(),
            'procurements' => $this->db->table('procurements')->orderBy('id', 'DESC')->get()->getResultArray(),
            'employees'    => $this->db->table('employees')->orderBy('name')->get()->getResultArray(),
        ];

        return view('dashboard/barang_create', $data);
    }

    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'proc_no_rab'       => 'required',
            'proc_no_npd'       => 'required',
            'procurement_date'  => 'required|valid_date',
            'asset_brand'       => 'required',
            'asset_model_name'  => 'required',
            'serial_number'     => 'required',
            'asset_code'        => 'required',
            'unit_name'         => 'required',
            'condition'         => 'required|in_list[baik,rusak,dipinjam,disposal]',
            'specification'     => 'permit_empty',
            'employee_name'     => 'permit_empty',
            'employee_nipp'     => 'permit_empty',
            'no_bast_bmc'       => 'permit_empty',
            'no_wo_bast'        => 'permit_empty',
            'link_bast'         => 'permit_empty|valid_url',
            'doc_link'          => 'permit_empty|valid_url',
            'purchase_date'     => 'permit_empty|valid_date',
            'label_attached'    => 'permit_empty|in_list[Sudah,Belum]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $exists = $this->db->table('assets')->where('asset_code', $input['asset_code'])->countAllResults();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', ['asset_code' => 'Kode inventaris sudah dipakai.']);
        }

        $this->db->transStart();

        try {
            $procData = [
                'no_rab'           => $input['proc_no_rab'],
                'no_npd'           => $input['proc_no_npd'],
                'procurement_date' => $input['procurement_date'],
                'notes'            => $input['proc_notes'] ?? null,
                'created_at'       => date('Y-m-d H:i:s'),
            ];
            $this->db->table('procurements')->insert($procData);
            $procurementId = (int) $this->db->insertID();

            $assetModelsTable = $this->db->table('asset_models');
            $assetModelRow = $assetModelsTable
                ->where('brand', $input['asset_brand'])
                ->where('model', $input['asset_model_name'])
                ->get()
                ->getRowArray();

            if ($assetModelRow) {
                $assetModelId = (int) $assetModelRow['id'];
            } else {
                $am = [
                    'brand'      => $input['asset_brand'],
                    'model'      => $input['asset_model_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->table('asset_models')->insert($am);
                $assetModelId = (int) $this->db->insertID();
            }
            $unitRow = $this->db->table('units')->where('name', $input['unit_name'])->get()->getRowArray();
            if ($unitRow) {
                $unitId = (int) $unitRow['id'];
            } else {
                $u = [
                    'name'       => $input['unit_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->table('units')->insert($u);
                $unitId = (int) $this->db->insertID();
            }
            $employeeId = null;
            if (!empty($input['employee_nipp'])) {
                $empRow = $this->db->table('employees')->where('nipp', $input['employee_nipp'])->get()->getRowArray();
                if ($empRow) {
                    $employeeId = (int) $empRow['id'];
                    if (!empty($input['employee_name']) && $empRow['name'] !== $input['employee_name']) {
                        $this->db->table('employees')->where('id', $employeeId)->update([
                            'name' => $input['employee_name'],
                        ]);
                    }
                } else {
                    $emp = [
                        'nipp'       => $input['employee_nipp'],
                        'name'       => $input['employee_name'] ?? '',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->table('employees')->insert($emp);
                    $employeeId = (int) $this->db->insertID();
                }
            }

            $assetPayload = [
                'asset_code'     => $input['asset_code'],
                'procurement_id' => $procurementId,
                'asset_model_id' => $assetModelId,
                'serial_number'  => $input['serial_number'],
                'purchase_date'  => $input['purchase_date'] ?? $input['procurement_date'] ?? null,
                'unit_id'        => $unitId,
                'employee_id'    => $employeeId,
                'specification'  => $input['specification'] ?? null,
                'label_attached' => $input['label_attached'] ?? 'Belum',
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
            } elseif (!empty($input['doc_link'])) {
                $docLink = $input['doc_link'];
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

            return redirect()->to(site_url('assets'))->with('success', 'Data berhasil ditambahkan dan relasi dibuat.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[Assets::store] Exception: ' . $e->getMessage());
            $dbError = $this->db->error();
            $msg     = $e->getMessage();
            if (!empty($dbError['message'])) {
                $msg .= ' | DB: ' . $dbError['message'];
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $msg);
        }
    }

    public function show($id)
    {
        $asset = $this->assetsModel->findWithRelations($id);
        if (! $asset) {
            return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');
        }

        $procurementsModel = new ProcurementsModel();
        $documentsModel    = new DocumentsModel();
        $employeesModel    = new EmployeesModel();

        $no_rab = '-';
        if (!empty($asset['procurement_id'])) {
            $proc = $procurementsModel->find($asset['procurement_id']);
            if ($proc) {
                $no_rab = $proc['no_rab'] ?? '-';
            }
        }

        $no_bast_bmc = '-';
        $link_bast   = null;
        $doc = $documentsModel->findBastByProcurementOrAsset(
            $asset['procurement_id'] ?? null,
            $asset['id'] ?? null
        );
        if ($doc) {
            $no_bast_bmc = $doc['doc_number'] ?? '-';
            $link_bast   = $doc['doc_link'] ?? null;
        }
        $employee_name = $asset['employee_name'] ?? '-';
        $nipp          = $asset['employee_nipp'] ?? '-';
        if (!empty($asset['employee_id'])) {
            $emp = $employeesModel->find($asset['employee_id']);
            if ($emp) {
                $employee_name = $emp['name'] ?? $employee_name;
                $nipp          = $emp['nipp'] ?? $nipp;
            }
        }

        $asset['no_rab']        = $no_rab;
        $asset['no_bast_bmc']   = $no_bast_bmc;
        $asset['link_bast']     = $link_bast;
        $asset['employee_name'] = $employee_name;
        $asset['nipp']          = $nipp;

        return view('dashboard/barang_detail', [
            'title' => 'Detail Barang',
            'asset' => $asset,
        ]);
    }

public function edit($id)
{
    $asset = $this->assetsModel->findWithRelations($id);
    if (! $asset) {
        return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');
    }

    $documentsModel = new DocumentsModel();
    $doc = $documentsModel->findBastByProcurementOrAsset(
        $asset['procurement_id'] ?? null,
        $asset['id'] ?? null
    );
    $asset['no_bast_bmc'] = null;
    $asset['no_wo_bast']  = null;
    $asset['link_bast']   = null;

    if ($doc && !empty($doc['doc_number'])) {
        if (preg_match('/BAST:\s*([^|]+)/', $doc['doc_number'], $m)) {
            $asset['no_bast_bmc'] = trim($m[1]);
        }

        if (preg_match('/WO:\s*([^|]+)/', $doc['doc_number'], $m)) {
            $asset['no_wo_bast'] = trim($m[1]);
        }

        $asset['link_bast'] = $doc['doc_link'] ?? null;
    }

    $data = [
        'title'        => 'Edit Barang',
        'asset'        => $asset,
        'units'        => $this->db->table('units')->orderBy('name')->get()->getResultArray(),
        'asset_models' => $this->db->table('asset_models')->orderBy('brand')->get()->getResultArray(),
        'procurements' => $this->db->table('procurements')->orderBy('id', 'DESC')->get()->getResultArray(),
        'employees'    => $this->db->table('employees')->orderBy('name')->get()->getResultArray(),
    ];

    return view('dashboard/barang_create', $data);
}

    public function update($id)
{
    $asset = $this->assetsModel->findWithRelations($id);
    if (! $asset) {
        return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');
    }

    $input = $this->request->getPost();

    $rules = [
        'proc_no_rab'       => 'required',
        'proc_no_npd'       => 'required',
        'procurement_date' => 'required|valid_date',
        'asset_code'        => 'required',
        'asset_brand'       => 'required',
        'asset_model_name'  => 'required',
        'serial_number'     => 'required',
        'unit_name'         => 'required',
        'condition'         => 'required|in_list[baik,rusak,dipinjam,disposal]',
        'purchase_date'     => 'permit_empty|valid_date',
        'specification'     => 'permit_empty',
        'label_attached'    => 'permit_empty|in_list[Sudah,Belum]',
        'employee_name'     => 'permit_empty',
        'employee_nipp'     => 'permit_empty',
        'no_bast_bmc'       => 'permit_empty',
        'no_wo_bast'        => 'permit_empty',
        'link_bast'         => 'permit_empty|valid_url',
        'doc_link'          => 'permit_empty|valid_url',
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
    }

    if ($input['asset_code'] !== $asset['asset_code']) {
        $exists = $this->db->table('assets')
            ->where('asset_code', $input['asset_code'])
            ->where('id !=', $id)
            ->countAllResults();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', ['asset_code' => 'Kode inventaris sudah dipakai.']);
        }
    }

    $this->db->transStart();

    try {

        $this->db->table('procurements')
            ->where('id', $asset['procurement_id'])
            ->update([
                'no_rab'           => $input['proc_no_rab'],
                'no_npd'           => $input['proc_no_npd'],
                'procurement_date' => $input['procurement_date'],
                'notes'            => $input['proc_notes'] ?? null,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

        $assetModelId = $asset['asset_model_id'];

        if (
            $input['asset_brand'] !== ($asset['brand'] ?? '') ||
            $input['asset_model_name'] !== ($asset['model_name'] ?? '')
        ) {
            $modelRow = $this->db->table('asset_models')
                ->where('brand', $input['asset_brand'])
                ->where('model', $input['asset_model_name'])
                ->get()
                ->getRowArray();

            if ($modelRow) {
                $assetModelId = $modelRow['id'];
            } else {
                $this->db->table('asset_models')->insert([
                    'brand'      => $input['asset_brand'],
                    'model'      => $input['asset_model_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $assetModelId = $this->db->insertID();
            }
        }

        $unitId = $asset['unit_id'];
        if ($input['unit_name'] !== ($asset['unit_name'] ?? '')) {
            $unitRow = $this->db->table('units')
                ->where('name', $input['unit_name'])
                ->get()
                ->getRowArray();

            if ($unitRow) {
                $unitId = $unitRow['id'];
            } else {
                $this->db->table('units')->insert([
                    'name'       => $input['unit_name'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $unitId = $this->db->insertID();
            }
        }

        $employeeId = $asset['employee_id'];

        if (!empty($input['employee_nipp']) || !empty($input['employee_name'])) {
            $empRow = $this->db->table('employees')
                ->where('nipp', $input['employee_nipp'])
                ->get()
                ->getRowArray();

            if ($empRow) {
                $employeeId = $empRow['id'];
                if (!empty($input['employee_name']) && $empRow['name'] !== $input['employee_name']) {
                    $this->db->table('employees')
                        ->where('id', $employeeId)
                        ->update(['name' => $input['employee_name']]);
                }
            } else {
                $this->db->table('employees')->insert([
                    'nipp'       => $input['employee_nipp'] ?? '',
                    'name'       => $input['employee_name'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $employeeId = $this->db->insertID();
            }
        }
        $this->db->table('assets')
            ->where('id', $id)
            ->update([
                'asset_code'     => $input['asset_code'],
                'asset_model_id' => $assetModelId,
                'serial_number'  => $input['serial_number'],
                'purchase_date'  => $input['purchase_date'] ?? $asset['purchase_date'],
                'unit_id'        => $unitId,
                'employee_id'    => $employeeId,
                'specification'  => $input['specification'] ?? $asset['specification'],
                'label_attached' => $input['label_attached'] ?? ($asset['label_attached'] ?? 'Belum'),
                'condition'      => $input['condition'],
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

        $documentsModel = new DocumentsModel();
        $doc = $documentsModel->findBastByProcurementOrAsset(
            $asset['procurement_id'],
            $id
        );

        $docParts = [];
        if (!empty($input['no_bast_bmc'])) {
            $docParts[] = 'BAST: ' . $input['no_bast_bmc'];
        }
        if (!empty($input['no_wo_bast'])) {
            $docParts[] = 'WO: ' . $input['no_wo_bast'];
        }

        $docNumber = $docParts ? implode(' | ', $docParts) : null;

        if ($docNumber || !empty($input['link_bast']) || !empty($input['doc_link'])) {

            $docLink = $input['link_bast']
                ?? $input['doc_link']
                ?? ($doc['doc_link'] ?? null);

            $docData = [
                'asset_id'       => $id,
                'procurement_id' => $asset['procurement_id'],
                'doc_type'       => 'BAST/WO/FILE',
                'doc_number'     => $docNumber ?? ($doc['doc_number'] ?? null),
                'doc_link'       => $docLink,
            ];

            if ($doc) {
                $this->db->table('documents')
                    ->where('id', $doc['id'])
                    ->update($docData);
            } else {
                $docData['created_at']  = date('Y-m-d H:i:s');
                $docData['uploaded_at'] = date('Y-m-d H:i:s');
                $this->db->table('documents')->insert($docData);
            }
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new \RuntimeException('Transaksi gagal.');
        }

        return redirect()->to('assets')->with('success', 'Data berhasil diperbarui.');

    } catch (\Throwable $e) {

        $this->db->transRollback();
        log_message('error', '[UPDATE ERROR] ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal mengubah data: ' . $e->getMessage());
    }
}

    public function delete($id)
    {
        if (! $this->request->is('post')) {
            return redirect()->to('assets')->with('error', 'Metode tidak diizinkan.');
        }

        $asset = $this->assetsModel->find($id);
        if (! $asset) {
            return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');
        }

        try {
            $this->assetsModel->delete($id);
            return redirect()->to('assets')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('assets')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function apiList()
    {
        $assets = $this->assetsModel->getAssetsWithRelations();
        return $this->response->setJSON([
            'status' => 'ok',
            'data'   => $assets,
        ]);
    }
    public function monitoring()
    {
        $assetsModel = new AssetsModel();
        $kategori    = $this->request->getGet('kategori');

        $allAssets   = $assetsModel->getAssetsWithRelations();
        $now         = Time::now('Asia/Jakarta', 'en_US');
        $masaManfaat = 5;

        $filteredAssets = [];

        foreach ($allAssets as $asset) {

            if (($asset['condition'] ?? '') === 'diganti') {
                continue;
            }
            $baseDate = $asset['purchase_date']
                ?? $asset['procurement_date']
                ?? null;

            if (!$baseDate) continue;

            try {
                $base = Time::parse($baseDate, 'Asia/Jakarta');
            } catch (\Exception $e) {
                continue;
            }

            $tahunKe = ($now->getYear() - $base->getYear()) + 1;
            $tahunKe = max(1, min($masaManfaat, $tahunKe));

            if ($tahunKe >= 4) {
                $kategoriUmur = 'merah';
            } elseif ($tahunKe == 3) {
                $kategoriUmur = 'kuning';
            } else {
                $kategoriUmur = 'hijau';
            }

            if ($kategori && $kategori !== $kategoriUmur) {
                continue;
            }

            $asset['tahun_ke']      = $tahunKe;
            $asset['kategori_umur'] = $kategoriUmur;

            $asset['asset_name'] =
                trim(($asset['brand'] ?? '').' '.($asset['model_name'] ?? ''))
                ?: ($asset['asset_code'] ?? '-');

            $filteredAssets[] = $asset;
        }

        return view('dashboard/monitoring', [
            'title'      => 'Monitoring Aset',
            'page_title' => 'Daftar Aset',
            'assets'     => $filteredAssets,
            'kategori'   => $kategori,
        ]);
    }

    public function monitoringStatus()
{
    $mode     = $this->request->getPost('mode');
    $assetIds = $this->request->getPost('asset_ids');

    if ($mode !== 'selected') {
        return redirect()->back()->with('error', 'Mode tidak valid.');
    }

    if (empty($assetIds) || !is_array($assetIds)) {
        return redirect()->back()->with('error', 'Tidak ada aset yang dipilih.');
    }

    $this->db->table('assets')
        ->whereIn('id', $assetIds)
        ->where('condition !=', 'diganti')
        ->update([
            'replaced_at'   => date('Y-m-d'), // 🧾 CATAT RIWAYAT
            'purchase_date'=> date('Y-m-d'), // 🔄 RESET UMUR
            'condition'    => 'baik',         // ♻️ AKTIF LAGI
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

    $count = $this->db->affectedRows();

    if ($count === 0) {
        return redirect()->back()->with(
            'warning',
            'Tidak ada aset yang diubah.'
        );
    }

    return redirect()->back()->with(
        'success',
        $count . ' aset berhasil diubah menjadi Diganti.'
    );
}
}
