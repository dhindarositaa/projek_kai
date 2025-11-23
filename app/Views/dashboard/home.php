<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4 space-y-6">

    <!-- HEADER -->
    <header class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-slate-800">
        <?= esc($page_title ?? 'Dashboard Aset') ?>
      </h1>
      <p class="text-sm text-slate-500">
        Monitoring umur aset 5 tahun: hijau (baru), kuning (ancang-ancang), merah (siap diajukan pengadaan).
      </p>
    </header>

    <!-- GRID 3 KARTU -->
    <section class="grid gap-4 grid-cols-1 lg:grid-cols-3">

      <!-- CARD MERAH -->
      <div class="bg-red-100 rounded-[1rem] shadow overflow-hidden">
        <div class="p-4 border-b border-red-200 flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-red-900">Kurang dari 1 Tahun</h3>
            <p class="text-xs text-red-800 mt-1">
              Aset di tahun ke-4 dan ke-5 (perlu perhatian & pengajuan pengadaan).
            </p>
          </div>
            <a href="<?= site_url('assets/monitoring?kategori=merah') ?>"
              class="text-xs px-3 py-1 rounded-full bg-red-200 hover:bg-red-300 text-red-800">
              SEE ALL
            </a>
        </div>

        <div class="divide-y">
          <?php if (!empty($assets_merah)) : ?>
            <?php foreach ($assets_merah as $item) : ?>
              <div class="p-4 flex items-center gap-3">
                <div class="w-36 text-sm font-medium">
                  <?= esc($item['asset_name']) ?>
                </div>
                <div class="w-28 text-xs">
                  <div><?= esc($item['base_date_display'] ?? '-') ?></div>
                  <div class="text-[10px] text-red-700">
                    Tahun ke-<?= esc($item['tahun_ke'] ?? '-') ?>
                  </div>
                </div>
                <div class="flex-1 text-sm">
                  <?= esc($item['asset_code'] ?? '-') ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="p-4 text-sm text-red-800">
              Tidak ada aset yang masuk kategori merah.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- CARD KUNING -->
      <div class="bg-yellow-100 rounded-[1rem] shadow overflow-hidden">
        <div class="p-4 border-b border-yellow-200 flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-yellow-900">Kurang dari 2 Tahun</h3>
            <p class="text-xs text-yellow-800 mt-1">
              Aset di tahun ke-3, ancang-ancang penggantian tahun berikutnya.
            </p>
          </div>
          <a href="<?= site_url('assets/monitoring?kategori=kuning') ?>"
            class="text-xs px-3 py-1 rounded-full bg-yellow-200 hover:bg-yellow-300 text-yellow-900">
            SEE ALL
          </a>
        </div>

        <div class="divide-y">
          <?php if (!empty($assets_kuning)) : ?>
            <?php foreach ($assets_kuning as $item) : ?>
              <div class="p-4 flex items-center gap-3">
                <div class="w-36 text-sm font-medium">
                  <?= esc($item['asset_name']) ?>
                </div>
                <div class="w-28 text-xs">
                  <div><?= esc($item['base_date_display'] ?? '-') ?></div>
                  <div class="text-[10px] text-yellow-700">
                    Tahun ke-<?= esc($item['tahun_ke'] ?? '-') ?>
                  </div>
                </div>
                <div class="flex-1 text-sm">
                  <?= esc($item['asset_code'] ?? '-') ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="p-4 text-sm text-yellow-900">
              Tidak ada aset yang masuk kategori kuning.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- CARD HIJAU -->
      <div class="bg-green-100 rounded-[1rem] shadow overflow-hidden">
        <div class="p-4 border-b border-green-200 flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-green-900">Lebih dari 3 Tahun</h3>
            <p class="text-xs text-green-800 mt-1">
              Aset di tahun ke-1 dan ke-2 (masih relatif baru, pemantauan saja).
            </p>
          </div>
          <a href="<?= site_url('assets/monitoring?kategori=hijau') ?>"
            class="text-xs px-3 py-1 rounded-full bg-green-200 hover:bg-green-300 text-green-900">
            SEE ALL
          </a>  
        </div>

        <div class="divide-y">
          <?php if (!empty($assets_hijau)) : ?>
            <?php foreach ($assets_hijau as $item) : ?>
              <div class="p-4 flex items-center gap-3">
                <div class="w-36 text-sm font-medium">
                  <?= esc($item['asset_name']) ?>
                </div>
                <div class="w-28 text-xs">
                  <div><?= esc($item['base_date_display'] ?? '-') ?></div>
                  <div class="text-[10px] text-green-700">
                    Tahun ke-<?= esc($item['tahun_ke'] ?? '-') ?>
                  </div>
                </div>
                <div class="flex-1 text-sm">
                  <?= esc($item['asset_code'] ?? '-') ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="p-4 text-sm text-green-900">
              Tidak ada aset yang masuk kategori hijau.
            </div>
          <?php endif; ?>
        </div>
      </div>

    </section>
  </div>
</section>

<?= $this->endSection() ?>
