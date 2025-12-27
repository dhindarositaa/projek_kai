<?php

namespace App\Controllers;

class BarangController extends BaseController
{
        public function index()
    {
        $data = [
            'title'      => 'Bulk Input',
            'page_title' => 'Bulk Input',
            'show_stats' => true, 
        ];
        return view('dashboard/barang', $data);
    }
}