<?php

namespace App\Controllers;

class InputController extends BaseController
{
        public function index()
    {
        $data = [
            'title'      => 'Input Manual',
            'page_title' => 'Input Manual',
            'show_stats' => true, // jika ingin menampilkan sticky stats
            // data lain untuk view
        ];

        return view('dashboard/input-manual', $data);
    }
}
