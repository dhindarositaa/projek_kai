<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4 space-y-6">

    <!-- HEADER -->
    <header class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-slate-800">
        <?= esc($page_title ?? 'Daftar Aset') ?>
      </h1>
      <p class="text-sm text-slate-500">
        <?= esc($subtitle ?? 'Menampilkan semua aset sesuai kategori yang dipilih.') ?>
      </p>
    </header>

    <!-- CARD WRAPPER -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
      <!-- TABEL ASET -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="px-4 py-3 text-left">#</th>
              <th class="px-4 py-3 text-left">Nama Aset</th>
              <th class="px-4 py-3 text-left">Kode Aset</th>
              <th class="px-4 py-3 text-left">Unit</th>
              <th class="px-4 py-3 text-left">PIC</th>
              <th class="px-4 py-3 text-left">Tgl Pengadaan</th>
              <th class="px-4 py-3 text-left">Tahun ke-</th>
              <th class="px-4 py-3 text-left">Kategori Umur</th>
              <th class="px-4 py-3 text-left">Kondisi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($assets)) : ?>
              <?php $no = 1; ?>
              <?php foreach ($assets as $a) : ?>
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-2 align-top"><?= $no++ ?></td>

                  <td class="px-4 py-2 align-top">
                    <div class="font-medium text-slate-800">
                      <?= esc($a['asset_name'] ?? (($a['brand'] ?? '') . ' ' . ($a['model_name'] ?? ''))) ?>
                    </div>
                    <?php if (!empty($a['no_npd'])) : ?>
                      <div class="text-xs text-slate-500">
                        NPD: <?= esc($a['no_npd']) ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['asset_code'] ?? '-') ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['unit_name'] ?? '-') ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['employee_name'] ?? '-') ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['base_date_display'] ?? (isset($a['purchase_date']) ? date('d/m/Y', strtotime($a['purchase_date'])) : '-')) ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['tahun_ke'] ?? '-') ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?php
                      // tentukan kategori umur (opsional: diisi dari controller)
                      $kat = $a['kategori_umur'] ?? null;
                      if ($kat === null && isset($a['tahun_ke'])) {
                          if ($a['tahun_ke'] >= 4) {
                              $kat = 'merah';
                          } elseif ($a['tahun_ke'] == 3) {
                              $kat = 'kuning';
                          } else {
                              $kat = 'hijau';
                          }
                      }

                      $badgeClass = 'bg-slate-200 text-slate-800';
                      if ($kat === 'merah') {
                          $badgeClass = 'bg-red-100 text-red-800';
                      } elseif ($kat === 'kuning') {
                          $badgeClass = 'bg-yellow-100 text-yellow-800';
                      } elseif ($kat === 'hijau') {
                          $badgeClass = 'bg-green-100 text-green-800';
                      }
                    ?>
                    <?php if (!empty($kat)) : ?>
                      <span class="px-2 py-1 rounded-full text-xs <?= $badgeClass ?>">
                        <?= ucfirst($kat) ?>
                      </span>
                    <?php else : ?>
                      -
                    <?php endif; ?>
                  </td>

                  <td class="px-4 py-2 align-top">
                    <?= esc($a['condition'] ?? '-') ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="9" class="px-4 py-6 text-center text-slate-500">
                  Tidak ada data aset untuk ditampilkan.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<?= $this->endSection() ?>
