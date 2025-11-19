<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetsModel;

class Assets extends BaseController
{
    protected $assetsModel;
    protected $db;
    protected $helpers = ['form', 'url', 'text'];

    public function __construct()
    {
        $this->assetsModel = new AssetsModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Index → menampilkan daftar barang
     * View: app/Views/dashboard/barang.php
     */
    public function index()
    {
        $perPage = 15;
        $page = (int)($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        $assets = $this->assetsModel->getAssetsWithRelations($perPage, $offset);
        $total  = (int)$this->db->table('assets')->countAllResults();
        $no     = $offset + 1;

        $pager = [
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage
        ];

        return view('dashboard/barang', [
            'title'  => 'Daftar Semua Barang',
            'assets' => $assets,
            'pager'  => $pager,
            'no'     => $no,
        ]);
    }

    /**
     * Create Form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Barang',
            'units' => $this->db->table('units')->orderBy('name')->get()->getResultArray(),
            'asset_models' => $this->db->table('asset_models')->orderBy('brand')->get()->getResultArray(),
            'procurements' => $this->db->table('procurements')->orderBy('id','DESC')->get()->getResultArray(),
            'employees' => $this->db->table('employees')->orderBy('name')->get()->getResultArray(),
        ];

        return view('dashboard/barang_create', $data);
    }

    /**
     * Store new data
     */
    public function store()
    {
        $input = $this->request->getPost();

        if (! $this->assetsModel->validate($input)) {
            return redirect()->back()->withInput()->with('error', $this->assetsModel->errors());
        }

        $fk = $this->assetsModel->foreignsExist($input);
        if (!empty($fk)) {
            return redirect()->back()->withInput()->with('error', $fk);
        }

        $payload = [
            'asset_code'      => $input['asset_code'],
            'procurement_id'  => $input['procurement_id'] ?: null,
            'asset_model_id'  => $input['asset_model_id'] ?: null,
            'serial_number'   => $input['serial_number'] ?? null,
            'purchase_date'   => $input['purchase_date'] ?: null,
            'unit_id'         => $input['unit_id'] ?: null,
            'employee_id'     => $input['employee_id'] ?: null,
            'specification'   => $input['specification'] ?? null,
            'label_attached'  => $input['label_attached'] ?? 'Belum',
            'condition'       => $input['condition'] ?? 'baik',
        ];

        try {
            $this->assetsModel->insert($payload);
            return redirect()->to(site_url('assets'))->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    /**
     * Detail
     */
    public function show($id)
    {
        $asset = $this->assetsModel->findWithRelations($id);
        if (!$asset) return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');

        return view('dashboard/barang_detail', [
            'title' => 'Detail Barang',
            'asset' => $asset,
        ]);
    }

    /**
     * Edit Form
     */
    public function edit($id)
    {
        $asset = $this->assetsModel->findWithRelations($id);
        if (!$asset) return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');

        $data = [
            'title' => 'Edit Barang',
            'asset' => $asset,
            'units' => $this->db->table('units')->orderBy('name')->get()->getResultArray(),
            'asset_models' => $this->db->table('asset_models')->orderBy('brand')->get()->getResultArray(),
            'procurements' => $this->db->table('procurements')->orderBy('id','DESC')->get()->getResultArray(),
            'employees' => $this->db->table('employees')->orderBy('name')->get()->getResultArray(),
        ];

        return view('dashboard/barang_edit', $data);
    }

    /**
     * Update
     */
    public function update($id)
    {
        $asset = $this->assetsModel->find($id);
        if (!$asset) return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');

        $input = $this->request->getPost();

        // Cek unique asset_code bila diubah
        if ($input['asset_code'] !== $asset['asset_code']) {
            $exists = $this->db->table('assets')
                ->where('asset_code', $input['asset_code'])
                ->where('id !=', $id)
                ->countAllResults();
            if ($exists) {
                return redirect()->back()->withInput()->with('error', ['asset_code' => 'Kode inventaris sudah dipakai.']);
            }
        }

        if (! $this->assetsModel->validate($input)) {
            return redirect()->back()->withInput()->with('error', $this->assetsModel->errors());
        }

        $fk = $this->assetsModel->foreignsExist($input);
        if (!empty($fk)) {
            return redirect()->back()->withInput()->with('error', $fk);
        }

        $payload = [
            'asset_code'      => $input['asset_code'],
            'procurement_id'  => $input['procurement_id'] ?: null,
            'asset_model_id'  => $input['asset_model_id'] ?: null,
            'serial_number'   => $input['serial_number'] ?? null,
            'purchase_date'   => $input['purchase_date'] ?: null,
            'unit_id'         => $input['unit_id'] ?: null,
            'employee_id'     => $input['employee_id'] ?: null,
            'specification'   => $input['specification'] ?? null,
            'label_attached'  => $input['label_attached'] ?? 'Belum',
            'condition'       => $input['condition'] ?? 'baik',
        ];

        try {
            $this->assetsModel->update($id, $payload);
            return redirect()->to('assets')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Delete (soft delete)
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('assets')->with('error', 'Metode tidak diizinkan.');
        }

        $asset = $this->assetsModel->find($id);
        if (!$asset) {
            return redirect()->to('assets')->with('error', 'Data tidak ditemukan.');
        }

        try {
            $this->assetsModel->delete($id);
            return redirect()->to('assets')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('assets')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
