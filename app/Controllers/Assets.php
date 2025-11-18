<?php
namespace App\Controllers;

use App\Models\AssetsModel;

class Assets extends BaseController
{
    protected $assetsModel;

    public function __construct()
    {
        $this->assetsModel = new AssetsModel();
        helper(['url','form']);
    }

    public function index()
    {
        $page = (int) ($this->request->getVar('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $assets = $this->assetsModel->getWithRelations($perPage, $offset);
        $total = $this->db->table('assets')->countAll();

        $data = [
            'title' => 'Daftar Semua Barang',
            'assets' => $assets,
            'no' => $offset + 1,
            'pager' => [
                'current' => $page,
                'perPage' => $perPage,
                'total' => $total,
            ],
            // kalau view mu butuh flash/session, session() tersedia di view
        ];

        return view('dashboard/barang', $data);
    }
}
