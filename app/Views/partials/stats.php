<div id="stats" class="w-full mt-4">
  <div class="bg-slate-100/90 backdrop-blur-sm px-3 py-4 shadow-sm">
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

      <!-- PERLU PENGADAAN -->
      <a href="<?= site_url('assets/monitoring?kategori=merah') ?>"
         class="relative bg-white rounded-md shadow hover:shadow-md transition p-4 overflow-hidden">

        <span class="absolute left-0 top-0 h-full w-1 bg-red-500"></span>

        <div class="text-sm text-slate-600 font-medium">
          Perlu Perhatian & Pengadaan
        </div>

        <div class="text-3xl font-bold tracking-tight text-slate-800 mt-1">
          <?= esc($stats['perlu_pengadaan'] ?? 0) ?>
        </div>

        <div class="mt-3 h-2 bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full bg-red-500 transition-all"
               style="width: <?= esc($stats['persen_pengadaan'] ?? 0) ?>%"></div>
        </div>

        <div class="absolute right-4 top-4 text-red-500/30">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M12 9v2m0 4h.01M5 19h14l-7-14-7 14z"/>
          </svg>
        </div>
      </a>

      <!-- BARANG RUSAK -->
      <a href="<?= site_url('assets?condition=rusak') ?>"
         class="relative bg-white rounded-md shadow hover:shadow-md transition p-4 overflow-hidden">

        <span class="absolute left-0 top-0 h-full w-1 bg-orange-500"></span>

        <div class="text-sm text-slate-600 font-medium">
          Barang Kondisi Rusak
        </div>

        <div class="text-3xl font-bold tracking-tight text-slate-800 mt-1">
          <?= esc($stats['rusak'] ?? 0) ?>
        </div>

        <div class="mt-3 h-2 bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full bg-orange-500 transition-all"
               style="width: <?= esc($stats['persen_rusak'] ?? 0) ?>%"></div>
        </div>

        <div class="absolute right-4 top-4 text-orange-500/30">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M4 4v6h6M20 20v-6h-6"/>
          </svg>
        </div>
      </a>

      <!-- BARANG BAIK -->
      <a href="<?= site_url('assets?condition=baik') ?>"
         class="relative bg-white rounded-md shadow hover:shadow-md transition p-4 overflow-hidden">

        <span class="absolute left-0 top-0 h-full w-1 bg-green-500"></span>

        <div class="text-sm text-slate-600 font-medium">
          Barang Kondisi Baik
        </div>

        <div class="text-3xl font-bold tracking-tight text-slate-800 mt-1">
          <?= esc($stats['baik'] ?? 0) ?>
        </div>

        <div class="mt-3 h-2 bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full bg-green-500 transition-all"
               style="width: <?= esc($stats['persen_baik'] ?? 0) ?>%"></div>
        </div>

        <div class="absolute right-4 top-4 text-green-500/30">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7"/>
          </svg>
        </div>
      </a>

      <!-- TOTAL BARANG -->
      <a href="<?= site_url('assets') ?>"
         class="relative bg-white rounded-md shadow hover:shadow-md transition p-4 overflow-hidden">

        <span class="absolute left-0 top-0 h-full w-1 bg-blue-500"></span>

        <div class="text-sm text-slate-600 font-medium">
          Total Barang Tersedia
        </div>

        <div class="text-3xl font-bold tracking-tight text-slate-800 mt-1">
          <?= esc($stats['total'] ?? 0) ?>
        </div>

        <div class="mt-3 h-2 bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full bg-blue-500 w-full"></div>
        </div>

        <div class="absolute right-4 top-4 text-blue-500/30">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path d="M3 3h18v18H3z"/>
          </svg>
        </div>
      </a>

    </section>
  </div>
</div>
