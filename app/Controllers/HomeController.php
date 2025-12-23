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

        // Ambil semua aset + relasi
        $allAssets = $assetsModel->getAssetsWithRelations();

        $now         = Time::now('Asia/Jakarta', 'en_US');
        $masaManfaat = 5; // horizon 5 tahun

        $merah  = []; // tahun_ke 4–5
        $kuning = []; // tahun_ke 3
        $hijau  = []; // tahun_ke 1–2

        foreach ($allAssets as $asset) {

            // =====================================================
            // 👉 SKIP aset yang SUDAH DIGANTI
            // =====================================================
            if (($asset['condition'] ?? '') === 'diganti') {
                continue;
            }

            // =====================================================
            // Tentukan tanggal acuan
            // =====================================================
            $baseDateString = $asset['purchase_date'] ?? null;

            if (empty($baseDateString) && !empty($asset['procurement_date'])) {
                $baseDateString = $asset['procurement_date'];
            }

            // Kalau tidak punya tanggal sama sekali, skip
            if (empty($baseDateString)) {
                continue;
            }

            try {
                $baseDate = Time::parse($baseDateString, 'Asia/Jakarta', 'en_US');
            } catch (\Exception $e) {
                // format tanggal bermasalah
                continue;
            }

            // =====================================================
            // Hitung tahun_ke
            // =====================================================
            $selisihTahun = $now->getYear() - $baseDate->getYear();
            $tahunKe      = $selisihTahun + 1;

            if ($tahunKe < 1) {
                $tahunKe = 1;
            }

            if ($tahunKe > $masaManfaat) {
                $tahunKe = $masaManfaat;
            }

            // =====================================================
            // Data tambahan untuk view
            // =====================================================
            $asset['tahun_ke']          = $tahunKe;
            $asset['base_date']         = $baseDateString;
            $asset['base_date_display'] = $baseDate->toLocalizedString('dd/MM/yyyy');

            // Nama aset: brand + model (fallback asset_code)
            $brand = $asset['brand']      ?? '';
            $model = $asset['model_name'] ?? '';
            $name  = trim($brand . ' ' . $model);

            if ($name === '') {
                $name = $asset['asset_code'] ?? '-';
            }

            $asset['asset_name'] = $name;

            // =====================================================
            // Klasifikasi kategori umur
            // =====================================================
            if ($tahunKe >= 4) {
                $merah[] = $asset;
            } elseif ($tahunKe == 3) {
                $kuning[] = $asset;
            } else {
                $hijau[] = $asset;
            }
        }

        // =====================================================
        // Data ke view
        // =====================================================
        $data = [
            'title'          => 'Dashboard Aset',
            'page_title'     => 'Dashboard Aset',
            'show_stats'     => true,

            // tampilkan maksimal 5 item per kategori
            'assets_merah'   => array_slice($merah, 0, 5),
            'assets_kuning'  => array_slice($kuning, 0, 5),
            'assets_hijau'   => array_slice($hijau, 0, 5),

            // total asli (untuk badge / statistik)
            'count_merah'    => count($merah),
            'count_kuning'   => count($kuning),
            'count_hijau'    => count($hijau),
            'stats' => $this->getStats(),
        ];

        return view('dashboard/home', $data);
    }
}
