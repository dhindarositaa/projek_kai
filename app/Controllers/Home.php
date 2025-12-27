<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetsModel;
use CodeIgniter\I18n\Time;

class Home extends BaseController
{
    public function index()
    {
        $assetsModel = new AssetsModel();

        $allAssets = $assetsModel->getAssetsWithRelations();

        $now         = Time::now('Asia/Jakarta', 'en_US');
        $masaManfaat = 5;

        $perluPengadaan = 0;

        foreach ($allAssets as $asset) {
            $baseDateString = $asset['purchase_date'] ?? null;

            if (empty($baseDateString) && !empty($asset['procurement_date'])) {
                $baseDateString = $asset['procurement_date'];
            }

            if (empty($baseDateString)) {
                continue;
            }

            try {
                $baseDate = Time::parse($baseDateString, 'Asia/Jakarta', 'en_US');
            } catch (\Exception $e) {
                continue;
            }

            $selisihTahun = $now->getYear() - $baseDate->getYear();
            $tahunKe      = $selisihTahun + 1;

            if ($tahunKe < 1) {
                $tahunKe = 1;
            }
            if ($tahunKe > $masaManfaat) {
                $tahunKe = $masaManfaat;
            }
            if ($tahunKe >= 4) {
                $perluPengadaan++;
            }
        }
        $totalBarang = count($allAssets);
        $totalRusak = $assetsModel->where('condition', 'rusak')->countAllResults();
        $totalBaik  = $assetsModel->where('condition', 'baik')->countAllResults();

        $data = [
            'title' => 'Dashboard',
            'stats' => [
                'perlu_pengadaan' => $perluPengadaan,
                'rusak'           => $totalRusak,
                'baik'            => $totalBaik,
                'total'           => $totalBarang,
            ],
        ];

        return view('dashboard/home', $data); 
    }
}
