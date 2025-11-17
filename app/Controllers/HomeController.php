<?php

namespace App\Controllers;

class HomeController extends BaseController
{
        public function index()
    {
        $data = [
            'title'      => 'Bulk Input',
            'page_title' => 'Bulk Input',
            'show_stats' => true, // jika ingin menampilkan sticky stats
            // data lain untuk view
        ];
        return view('dashboard/home', $data);
    }
}
