<?php

namespace App\Controllers;

class BulkInputController extends BaseController
{
        public function index()
    {
        $data = [
            'title'      => 'Bulk Input',
            'page_title' => 'Bulk Input',
            'show_stats' => true,

            'stats' => $this->getStats(),
        ];

        return view('dashboard/bulk-input', $data);
    }
}
