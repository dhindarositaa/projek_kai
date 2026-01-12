<!-- app/Views/dashboard/barang_detail.php -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- MAIN CONTENT -->
<section class="py-6">
  <!-- Container full-width (max width for readability) -->
  <div class="max-w-screen-xl mx-auto px-4">
    
    <!-- CARD FULL WIDTH -->
    <section class="grid grid-cols-1">
      <div class="card-bg rounded shadow-sm overflow-hidden w-full">
        
        <!-- Header -->
        <div class="p-6 border-b">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-800">
              <?= esc($title ?? 'Detail Barang') ?>
            </h2>
            <a href="<?= site_url('assets') ?>" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm">
              Kembali ke Daftar
            </a>
          </div>
        </div>

        <!-- CONTENT DETAIL (mirip struktur form) -->
        <div class="p-6 space-y-6">
          <!-- A. Informasi Pengadaan -->
          <fieldset>
            <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">
              A. Informasi Pengadaan
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- No RAB -->
              <div>
                <label class="text-xs text-gray-700">No RAB</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['no_rab'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- No NPD -->
              <div>
                <label class="text-xs text-gray-700">No NPD</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['no_npd'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Tanggal Pengadaan -->
              <div>
                <label class="text-xs text-gray-700">Tanggal Pengadaan</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?php
                         $tanggal = $asset['procurement_date'] ?? $asset['purchase_date'] ?? null;
                         echo $tanggal ? date('d/m/Y', strtotime($tanggal)) : '-';
                       ?>"
                       readonly />
              </div>

              <!-- No BAST BMC -->
              <div>
                <label class="text-xs text-gray-700">No BAST BMC</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['no_bast_bmc'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- No WO BAST -->
              <div>
                <label class="text-xs text-gray-700">No WO BAST</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['no_wo_bast'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Link File BAST -->
              <div>
                <label class="text-xs text-gray-700">Link File BAST</label>
                <?php if (!empty($asset['link_bast'])): ?>
                  <a href="<?= esc($asset['link_bast']) ?>"
                     target="_blank"
                     class="mt-1 block text-sm underline break-all text-blue-600">
                    <?= esc($asset['link_bast']) ?>
                  </a>
                <?php else: ?>
                  <input type="text"
                         class="mt-1 w-full border rounded px-3 py-2"
                         value="-"
                         readonly />
                <?php endif; ?>
              </div>
            </div>
          </fieldset>

          <hr class="border-t" />

          <!-- B. Informasi Perangkat -->
          <fieldset>
            <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">
              B. Informasi Perangkat
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Jenis Perangkat -->
              <div>
                <label class="text-xs text-gray-700">Jenis Perangkat</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['jenis_perangkat'] ?? $asset['brand'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Merek / Tipe -->
              <div>
                <label class="text-xs text-gray-700">Merek / Tipe</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['model_name'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Serial Number -->
              <div>
                <label class="text-xs text-gray-700">Serial Number</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['serial_number'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- No Inventaris -->
              <div>
                <label class="text-xs text-gray-700">No Inventaris</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['asset_code'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Spesifikasi (full width) -->
              <div class="md:col-span-2">
                <label class="text-xs text-gray-700">Spesifikasi</label>
                <textarea rows="3"
                          class="mt-1 w-full border rounded px-3 py-2"
                          readonly><?= esc($asset['specification'] ?? '-') ?></textarea>
              </div>

              <!-- Kondisi (status/kondisi perangkat) -->
              <div>
                <label class="text-xs text-gray-700">Kondisi</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['status'] ?? $asset['condition'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Label Terpasang -->
              <div>
                <label class="text-xs text-gray-700">Label Terpasang</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['label_attached'] ?? '-') ?>"
                       readonly />
              </div>
            </div>
          </fieldset>

          <hr class="border-t" />

          <!-- C. Informasi Pengguna -->
          <fieldset>
            <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">
              C. Informasi Pengguna
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Nama Pengguna -->
              <div>
                <label class="text-xs text-gray-700">Nama Pengguna</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['employee_name'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Unit -->
              <div>
                <label class="text-xs text-gray-700">Unit</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['unit_name'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- NIPP -->
              <div>
                <label class="text-xs text-gray-700">NIPP</label>
                <input type="text"
                       class="mt-1 w-full border rounded px-3 py-2"
                       value="<?= esc($asset['nipp'] ?? '-') ?>"
                       readonly />
              </div>

              <!-- Keterangan (full width) -->
              <div class="md:col-span-3">
                <label class="text-xs text-gray-700">Keterangan</label>
                <textarea rows="3"
                          class="mt-1 w-full border rounded px-3 py-2"
                          readonly><?= esc($asset['note'] ?? '-') ?></textarea>
              </div>
            </div>
          </fieldset>

          <!-- Tombol aksi (hanya kembali) -->
          <div class="flex items-center justify-end">
            <a href="<?= site_url('assets') ?>"
               class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
              Kembali ke Daftar
            </a>
          </div>

          <div>
          <label class="text-xs text-gray-700">Terakhir Diganti</label>
          <input type="text"
                class="mt-1 w-full border rounded px-3 py-2 bg-gray-50"
                value="<?=
                  !empty($asset['replaced_at'])
                    ? date('d/m/Y', strtotime($asset['replaced_at']))
                    : 'Belum Pernah Diganti'
                ?>"
                readonly />
        </div>

        </div>
      </div>
    </section>

  </div>
</section>

<?= $this->endSection() ?>
