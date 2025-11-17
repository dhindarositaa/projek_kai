<?php

namespace App\Controllers;

class BarangController extends BaseController
{
        public function index()
    {
        $data = [
            'title'      => 'Bulk Input',
            'page_title' => 'Bulk Input',
            'show_stats' => true, // jika ingin menampilkan sticky stats
            // data lain untuk view
        ];
        return view('dashboard/barang', $data);
    }
}