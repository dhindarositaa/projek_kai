<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetsModel;
use CodeIgniter\I18n\Time;

class HomeController extends BaseController
{
    public function index()
    {
        $assetsModel = new AssetsModel();

        $allAssets = $assetsModel->getAssetsWithRelations();

        $now         = Time::now('Asia/Jakarta', 'en_US');
        $masaManfaat = 5; 

        $merah  = []; 
        $kuning = []; 
        $hijau  = []; 

        foreach ($allAssets as $asset) {
            if (($asset['condition'] ?? '') !== 'baik') {
                continue;
            }
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

            $asset['tahun_ke']          = $tahunKe;
            $asset['base_date']         = $baseDateString;
            $asset['base_date_display'] = $baseDate->toLocalizedString('dd/MM/yyyy');

            $brand = $asset['brand']      ?? '';
            $model = $asset['model_name'] ?? '';
            $name  = trim($brand . ' ' . $model);

            if ($name === '') {
                $name = $asset['asset_code'] ?? '-';
            }

            $asset['asset_name'] = $name;
            if ($tahunKe >= 4) {
                $merah[] = $asset;
            } elseif ($tahunKe == 3) {
                $kuning[] = $asset;
            } else {
                $hijau[] = $asset;
            }
        }

        $data = [
            'title'          => 'Dashboard Aset',
            'page_title'     => 'Dashboard Aset',
            'show_stats'     => true,

            'assets_merah'   => array_slice($merah, 0, 5),
            'assets_kuning'  => array_slice($kuning, 0, 5),
            'assets_hijau'   => array_slice($hijau, 0, 5),

            'count_merah'    => count($merah),
            'count_kuning'   => count($kuning),
            'count_hijau'    => count($hijau),
            'stats' => $this->getStats(),
        ];

        return view('dashboard/home', $data);
    }
}
