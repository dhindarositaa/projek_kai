<div id="stats" class="w-full mt-4">
  <div class="bg-slate-100/90 backdrop-blur-sm px-2 py-4 shadow-sm">
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full px-4">
      
      <!-- PERLU PERHATIAN & PENGAJUAN PENGADAAN -->
      <a href="<?= site_url('assets/monitoring?kategori=merah') ?>" class="card-bg rounded p-4 shadow-md hover:shadow-lg transition-shadow block">
        <div class="text-xs text-gray-700">Perlu Perhatiann & Pengajuan Pengadaan</div>
        <div class="text-xl font-bold mt-1">
          <?= esc($stats['perlu_pengadaan'] ?? 0) ?>
        </div>
        <div class="mt-1 text-red-600 text-sm flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span class="text-gray-500">Segera Update!</span>
        </div>
      </a>

      <!-- BARANG KONDISI RUSAK -->
      <a href="<?= site_url('assets?condition=rusak') ?>" class="card-bg rounded p-4 shadow-md hover:shadow-lg transition-shadow block">
        <div class="text-xs text-gray-700">Barang dengan Kondisi Rusak</div>
        <div class="text-xl font-bold mt-1">
          <?= esc($stats['rusak'] ?? 0) ?>
        </div>
        <div class="mt-1 text-blue-600 text-sm flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5" />
          </svg>
          <span class="text-gray-600">Jangan lupa cek!</span>
        </div>
      </a>

      <!-- BARANG KONDISI BAIK -->
      <a href="<?= site_url('assets?condition=baik') ?>" class="card-bg rounded p-4 shadow-md hover:shadow-lg transition-shadow block">
        <div class="text-xs text-gray-700">Barang dengan Kondisi Baik</div>
        <div class="text-xl font-bold mt-1">
          <?= esc($stats['baik'] ?? 0) ?>
        </div>
        <div class="mt-1 text-orange-500 text-sm flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 12V7a2 2 0 00-1-1.732l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 7v5a2 2 0 001 1.732l7 4a2 2 0 002 0l7-4A2 2 0 0020 12z" />
          </svg>
          <span class="text-gray-600">Good Job!</span>
        </div>
      </a>

      <!-- TOTAL BARANG TERSEDIA -->
      <a href="<?= site_url('assets') ?>" class="card-bg rounded p-4 shadow-md hover:shadow-lg transition-shadow block">
        <div class="text-xs text-gray-700">Total Barang Tersedia</div>
        <div class="text-xl font-bold mt-1">
          <?= esc($stats['total'] ?? 0) ?>
        </div>
        <div class="mt-1 text-green-600 text-sm flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-gray-600">Great!</span>
        </div>
      </a>

    </section>
  </div>
</div>
