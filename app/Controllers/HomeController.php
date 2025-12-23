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

        // Ambil semua aset + relasi (pakai helper yang sudah ada di modelmu)
        $allAssets = $assetsModel->getAssetsWithRelations();

        $now         = Time::now('Asia/Jakarta', 'en_US');
        $masaManfaat = 5; // horizon 5 tahun

        $merah  = []; // tahun_ke 4–5 → siap diajukan pengadaan baru
        $kuning = []; // tahun_ke 3   → ancang-ancang diganti
        $hijau  = []; // tahun_ke 1–2 → masih relatif baru

        foreach ($allAssets as $asset) {
            // Tanggal acuan: purchase_date dulu, kalau kosong pakai procurement_date
            $baseDateString = $asset['purchase_date'] ?? null;

            if (empty($baseDateString) && !empty($asset['procurement_date'])) {
                $baseDateString = $asset['procurement_date'];
            }

            // Kalau benar-benar tidak punya tanggal, lewati
            if (empty($baseDateString)) {
                continue;
            }

            try {
                $baseDate = Time::parse($baseDateString, 'Asia/Jakarta', 'en_US');
            } catch (\Exception $e) {
                // Kalau format tanggal bermasalah, skip saja
                continue;
            }

            // Hitung selisih tahun sederhana
            $selisihTahun = $now->getYear() - $baseDate->getYear();

            // Tahun ke-berapa dari awal pengadaan:
            //   pengadaan 2022, sekarang 2022 → tahun_ke = 1
            //   pengadaan 2022, sekarang 2023 → tahun_ke = 2
            //   pengadaan 2022, sekarang 2024 → tahun_ke = 3
            //   pengadaan 2022, sekarang 2025 → tahun_ke = 4
            $tahunKe = $selisihTahun + 1;

            if ($tahunKe < 1) {
                $tahunKe = 1;
            }

            // Kalau lewat masa manfaat 5 tahun, mentokin di 5 (tetap merah)
            if ($tahunKe > $masaManfaat) {
                $tahunKe = $masaManfaat;
            }

            // Tambahan info buat ditampilkan di view
            $asset['tahun_ke']          = $tahunKe;
            $asset['base_date']         = $baseDateString;
            $asset['base_date_display'] = $baseDate->toLocalizedString('dd/MM/yyyy');

            // Bentuk nama aset: brand + model, fallback ke asset_code kalau kosong
            $brand = $asset['brand']      ?? '';
            $model = $asset['model_name'] ?? '';
            $name  = trim($brand . ' ' . $model);

            if ($name === '') {
                $name = $asset['asset_code'] ?? '-';
            }

            $asset['asset_name'] = $name;

            // KATEGORI:
            // tahun_ke 4–5 → MERAH
            // tahun_ke 3   → KUNING
            // tahun_ke 1–2 → HIJAU
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

        // TAMPILKAN HANYA 5 ITEM PER KATEGORI
        'assets_merah'   => array_slice($merah, 0, 5),
        'assets_kuning'  => array_slice($kuning, 0, 5),
        'assets_hijau'   => array_slice($hijau, 0, 5),

        // Jika kamu butuh total aslinya untuk badge/angka statistik:
        'count_merah'    => count($merah),
        'count_kuning'   => count($kuning),
        'count_hijau'    => count($hijau),
        
        'stats' => [
        'perlu_pengadaan' => count($merah),
        'rusak' => $assetsModel->where('condition', 'rusak')->countAllResults(),
        'baik'  => $assetsModel->where('condition', 'baik')->countAllResults(),
        'total' => count($allAssets),
        ],
    ];

        // View: resources/views/dashboard/home.php
        return view('dashboard/home', $data);
    }
}
