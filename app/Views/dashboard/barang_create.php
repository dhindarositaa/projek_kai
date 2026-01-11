<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4">
    <section class="grid grid-cols-1">
      <div class="card-bg rounded shadow-sm overflow-hidden w-full bg-white border border-gray-200">

        <div class="p-6 border-b border-gray-300 bg-gray-50">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-800">
              <?= esc($title ?? (isset($asset) ? 'Edit Barang' : 'Input Data Manual')) ?>
            </h2>
            <a href="<?= site_url('assets') ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">
              Kembali ke Daftar
            </a>
          </div>
        </div>

        <div class="p-6 space-y-6">

          <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-xs">
              <?php
                $err = session()->getFlashdata('error');
                if (is_array($err)) {
                  foreach ($err as $e) echo '<div>'.esc($e).'</div>';
                } else {
                  echo esc($err);
                }
              ?>
            </div>
          <?php endif; ?>

          <?php $isEdit = isset($asset); ?>

          <form action="<?= $isEdit ? site_url('assets/'.$asset['id'].'/update') : site_url('assets') ?>"
                method="post" id="createAssetForm" class="space-y-6" novalidate>
            <?= csrf_field() ?>

            <!-- A. INFORMASI PENGADAAN -->
            <fieldset>
              <legend class="text-xs font-semibold uppercase mb-3 text-gray-800">
                A. Informasi Pengadaan
              </legend>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                  <label class="text-xs font-medium text-gray-800">No RAB *</label>
                  <input name="proc_no_rab" type="text" required
                         value="<?= esc(old('proc_no_rab') ?? ($asset['no_rab'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">No NPD *</label>
                  <input name="proc_no_npd" type="text" required
                         value="<?= esc(old('proc_no_npd') ?? ($asset['no_npd'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">Tanggal Pengadaan *</label>
                  <input name="procurement_date" type="date" required
                         value="<?= esc(old('procurement_date') ?? ($asset['procurement_date'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">No BAST</label>
                  <input name="no_bast_bmc" type="text"
                         value="<?= esc(old('no_bast_bmc') ?? ($asset['no_bast_bmc'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">No WO</label>
                  <input name="no_wo_bast" type="text"
                         value="<?= esc(old('no_wo_bast') ?? ($asset['no_wo_bast'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">Link BAST</label>
                  <input name="link_bast" type="url"
                         value="<?= esc(old('link_bast') ?? ($asset['link_bast'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

              </div>
            </fieldset>

            <hr>

            <!-- B. INFORMASI PERANGKAT -->
            <fieldset>
              <legend class="text-xs font-semibold uppercase mb-3 text-gray-800">
                B. Informasi Perangkat
              </legend>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                  <label>Jenis</label>
                  <input name="asset_brand" required
                         value="<?= esc(old('asset_brand') ?? ($asset['brand'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>Model</label>
                  <input name="asset_model_name" required
                         value="<?= esc(old('asset_model_name') ?? ($asset['model_name'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>Serial</label>
                  <input name="serial_number" required
                         value="<?= esc(old('serial_number') ?? ($asset['serial_number'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>No Inventaris</label>
                  <input name="asset_code" required
                         value="<?= esc(old('asset_code') ?? ($asset['asset_code'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div class="md:col-span-2">
                  <label>Spesifikasi</label>
                  <textarea name="specification" class="w-full border-2 px-3 py-2 rounded"><?= esc(old('specification') ?? ($asset['specification'] ?? '')) ?></textarea>
                </div>

              </div>
            </fieldset>

            <hr>

            <!-- C. INFORMASI PENGGUNA -->
            <fieldset>
              <legend class="text-xs font-semibold uppercase mb-3 text-gray-800">
                C. Informasi Pengguna
              </legend>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                  <label>Nama</label>
                  <input name="employee_name"
                         value="<?= esc(old('employee_name') ?? ($asset['employee_name'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>Unit</label>
                  <input name="unit_name" required
                         value="<?= esc(old('unit_name') ?? ($asset['unit_name'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>NIPP</label>
                  <input name="employee_nipp"
                         value="<?= esc(old('employee_nipp') ?? ($asset['employee_nipp'] ?? '')) ?>"
                         class="w-full border-2 px-3 py-2 rounded" />
                </div>

                <div>
                  <label>Kondisi</label>
                  <?php $cond = old('condition') ?? ($asset['condition'] ?? ''); ?>
                    <select name="condition" class="w-full border-2 px-3 py-2 rounded">
                      <option value="">Pilih...</option>
                      <option value="baik" <?= $cond=='baik'?'selected':'' ?>>baik</option>
                      <option value="rusak" <?= $cond=='rusak'?'selected':'' ?>>rusak</option>
                      <option value="dipinjam" <?= $cond=='dipinjam'?'selected':'' ?>>dipinjam</option>
                      <option value="disposal" <?= $cond=='disposal'?'selected':'' ?>>disposal</option>
                      <option value="diganti" <?= $cond=='diganti'?'selected':'' ?>>diganti</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                  <label>Keterangan</label>
                  <textarea name="keterangan" class="w-full border-2 px-3 py-2 rounded">
<?= esc(old('keterangan') ?? ($asset['note'] ?? '')) ?>
</textarea>
                </div>

              </div>
            </fieldset>

            <div class="flex justify-end gap-3">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                <?= $isEdit ? 'Update' : 'Simpan' ?>
              </button>
            </div>

          </form>
        </div>
      </div>
    </section>
  </div>
</section>

<?= $this->endSection() ?>
