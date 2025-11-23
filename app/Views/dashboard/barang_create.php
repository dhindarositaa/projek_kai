<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4">
    <section class="grid grid-cols-1">
      <div class="card-bg rounded shadow-sm overflow-hidden w-full bg-white">
        <!-- Header -->
        <div class="p-6 border-b">
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
                    foreach ($err as $k => $e) {
                        echo '<div>'.esc(is_string($k) ? "$k: $e" : $e).'</div>';
                    }
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

            <!-- A. Informasi Pengadaan -->
            <fieldset>
              <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">A. Informasi Pengadaan</legend>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- No RAB (wajib) -->
                <div>
                  <label for="proc_no_rab" class="text-xs text-gray-700">
                    No RAB <span class="text-red-600">*</span>
                  </label>
                  <input id="proc_no_rab" name="proc_no_rab" type="text" required
                         value="<?= esc(old('proc_no_rab') ?? ($asset['no_rab'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_proc_no_rab" class="hidden text-xs text-red-600 mt-1">
                    No RAB wajib diisi.
                  </p>
                </div>

                <!-- No NPD (wajib) -->
                <div>
                  <label for="proc_no_npd" class="text-xs text-gray-700">
                    No NPD <span class="text-red-600">*</span>
                  </label>
                  <input id="proc_no_npd" name="proc_no_npd" type="text" required
                         value="<?= esc(old('proc_no_npd') ?? ($asset['no_npd'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_proc_no_npd" class="hidden text-xs text-red-600 mt-1">
                    No NPD wajib diisi.
                  </p>
                </div>

                <!-- Tanggal Pengadaan (wajib) -->
                <div>
                  <label for="procurement_date" class="text-xs text-gray-700">
                    Tanggal Pengadaan <span class="text-red-600">*</span>
                  </label>
                  <input id="procurement_date" name="procurement_date" type="date" required
                         value="<?= esc(old('procurement_date') ?? ($asset['procurement_date'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_procurement_date" class="hidden text-xs text-red-600 mt-1">
                    Tanggal Pengadaan wajib diisi.
                  </p>
                </div>

                <!-- No BAST BMC (opsional) -->
                <div>
                  <label for="no_bast_bmc" class="text-xs text-gray-700">No BAST BMC</label>
                  <input id="no_bast_bmc" name="no_bast_bmc" type="text"
                         value="<?= esc(old('no_bast_bmc') ?? ($asset['no_bast_bmc'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                </div>

                <!-- No WO BAST (opsional) -->
                <div>
                  <label for="no_wo_bast" class="text-xs text-gray-700">No WO BAST</label>
                  <input id="no_wo_bast" name="no_wo_bast" type="text"
                         value="<?= esc(old('no_wo_bast') ?? ($asset['no_wo_bast'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                </div>

                <!-- Link File BAST (opsional) -->
                <div>
                  <label for="link_bast" class="text-xs text-gray-700">Link File BAST</label>
                  <input id="link_bast" name="link_bast" type="url"
                         placeholder="https://..."
                         value="<?= esc(old('link_bast') ?? ($asset['link_bast'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                </div>
              </div>
            </fieldset>

            <hr class="border-t"/>

            <!-- B. Informasi Perangkat -->
            <fieldset>
              <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">B. Informasi Perangkat</legend>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jenis Perangkat (wajib) -->
                <div>
                  <label for="asset_brand" class="text-xs text-gray-700">
                    Jenis Perangkat <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_brand" name="asset_brand" type="text" required
                         value="<?= esc(old('asset_brand') ?? ($asset['brand'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_asset_brand" class="hidden text-xs text-red-600 mt-1">
                    Jenis Perangkat wajib diisi.
                  </p>
                </div>

                <!-- Merek / Tipe (wajib) -->
                <div>
                  <label for="asset_model_name" class="text-xs text-gray-700">
                    Merek / Tipe <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_model_name" name="asset_model_name" type="text" required
                         value="<?= esc(old('asset_model_name') ?? ($asset['model_name'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_asset_model_name" class="hidden text-xs text-red-600 mt-1">
                    Merek / Tipe wajib diisi.
                  </p>
                </div>

                <!-- Serial Number (wajib) -->
                <div>
                  <label for="serial_number" class="text-xs text-gray-700">
                    Serial Number <span class="text-red-600">*</span>
                  </label>
                  <input id="serial_number" name="serial_number" type="text" required
                         value="<?= esc(old('serial_number') ?? ($asset['serial_number'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_serial_number" class="hidden text-xs text-red-600 mt-1">
                    Serial Number wajib diisi.
                  </p>
                </div>

                <!-- No Inventaris (wajib) -->
                <div>
                  <label for="asset_code" class="text-xs text-gray-700">
                    No Inventaris <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_code" name="asset_code" type="text" required
                         value="<?= esc(old('asset_code') ?? ($asset['asset_code'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_asset_code" class="hidden text-xs text-red-600 mt-1">
                    No Inventaris wajib diisi.
                  </p>
                </div>

                <!-- Spesifikasi (opsional) -->
                <div class="md:col-span-2">
                  <label for="specification" class="text-xs text-gray-700">
                    Spesifikasi
                  </label>
                  <textarea id="specification" name="specification" rows="3"
                            class="mt-1 w-full border rounded px-3 py-2"
                            placeholder="CPU, RAM, Storage, OS, dll."><?= esc(old('specification') ?? ($asset['specification'] ?? '')) ?></textarea>
                  <p id="err_specification" class="hidden text-xs text-red-600 mt-1">
                    Spesifikasi wajib diisi.
                  </p>
                </div>

                <!-- Link Dokumen (opsional, jika mau dipakai) -->
                <div>
                  <label for="doc_link" class="text-xs text-gray-700">Link Dokumen (Opsional)</label>
                  <input id="doc_link" name="doc_link" type="url"
                         placeholder="https://..."
                         value="<?= esc(old('doc_link') ?? ($asset['doc_link'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                </div>
              </div>
            </fieldset>

            <hr class="border-t"/>

            <!-- C. Informasi Pengguna -->
            <fieldset>
              <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">C. Informasi Pengguna</legend>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Nama Pengguna (opsional) -->
                <div>
                  <label for="employee_name" class="text-xs text-gray-700">
                    Nama Pengguna
                  </label>
                  <input id="employee_name" name="employee_name" type="text"
                         value="<?= esc(old('employee_name') ?? ($asset['employee_name'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_employee_name" class="hidden text-xs text-red-600 mt-1">
                    Nama Pengguna wajib diisi.
                  </p>
                </div>

                <!-- Unit (wajib) -->
                <div>
                  <label for="unit_name" class="text-xs text-gray-700">
                    Unit <span class="text-red-600">*</span>
                  </label>
                  <input id="unit_name" name="unit_name" type="text" required
                         value="<?= esc(old('unit_name') ?? ($asset['unit_name'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_unit_name" class="hidden text-xs text-red-600 mt-1">
                    Unit wajib diisi.
                  </p>
                </div>

                <!-- NIPP (opsional) -->
                <div>
                  <label for="employee_nipp" class="text-xs text-gray-700">
                    NIPP
                  </label>
                  <input id="employee_nipp" name="employee_nipp" type="text"
                         value="<?= esc(old('employee_nipp') ?? ($asset['employee_nipp'] ?? '')) ?>"
                         class="mt-1 w-full border rounded px-3 py-2" />
                  <p id="err_employee_nipp" class="hidden text-xs text-red-600 mt-1">
                    NIPP wajib diisi.
                  </p>
                </div>

                <!-- Kondisi (wajib) -->
                <div>
                  <label for="condition" class="text-xs text-gray-700">
                    Kondisi <span class="text-red-600">*</span>
                  </label>

                  <?php $cond = old('condition') ?? ($asset['condition'] ?? ''); ?>

                  <select id="condition" name="condition" required class="mt-1 w-full border rounded px-3 py-2">
                    <option value="">Pilih...</option>
                    <option value="baik"     <?= $cond === 'baik' ? 'selected' : '' ?>>baik</option>
                    <option value="rusak"    <?= $cond === 'rusak' ? 'selected' : '' ?>>rusak</option>
                    <option value="dipinjam" <?= $cond === 'dipinjam' ? 'selected' : '' ?>>dipinjam</option>
                    <option value="disposal" <?= $cond === 'disposal' ? 'selected' : '' ?>>disposal</option>
                  </select>

                  <p id="err_condition" class="hidden text-xs text-red-600 mt-1">
                    Kondisi wajib dipilih.
                  </p>
                </div>


                <!-- Keterangan (opsional, full width) -->
                <div class="md:col-span-3">
                  <label for="keterangan" class="text-xs text-gray-700">Keterangan</label>
                  <textarea id="keterangan" name="keterangan" rows="3"
                            placeholder="Catatan tambahan terkait perangkat..."
                            class="mt-1 w-full border rounded px-3 py-2"><?= esc(old('keterangan') ?? ($asset['keterangan'] ?? '')) ?></textarea>
                </div>
              </div>
            </fieldset>

            <div class="flex items-center justify-end gap-3">
              <button type="reset" class="px-4 py-2 rounded border border-gray-200">
                Reset
              </button>
              <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">
                <?= $isEdit ? 'Update' : 'Simpan' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</section>

<script>
  (function () {
    const form = document.getElementById('createAssetForm');

    // HANYA FIELD WAJIB SESUAI LIST KAMU
    const requiredFields = [
      { id: 'proc_no_rab',      err: 'err_proc_no_rab' },
      { id: 'proc_no_npd',      err: 'err_proc_no_npd' },
      { id: 'procurement_date', err: 'err_procurement_date' },
      { id: 'asset_brand',      err: 'err_asset_brand' },      // Jenis Perangkat
      { id: 'asset_model_name', err: 'err_asset_model_name' }, // Merek / Tipe
      { id: 'serial_number',    err: 'err_serial_number' },
      { id: 'asset_code',       err: 'err_asset_code' },
      { id: 'condition',        err: 'err_condition' },
      { id: 'unit_name',        err: 'err_unit_name' }
    ];

    function showError(el, errId) {
      if (!el) return;
      el.classList.add('border-red-600', 'ring-1', 'ring-red-200');
      el.setAttribute('aria-invalid', 'true');
      const e = document.getElementById(errId);
      if (e) e.classList.remove('hidden');
    }

    function clearError(el, errId) {
      if (!el) return;
      el.classList.remove('border-red-600', 'ring-1', 'ring-red-200');
      el.removeAttribute('aria-invalid');
      const e = document.getElementById(errId);
      if (e) e.classList.add('hidden');
    }

    form.addEventListener('submit', function (ev) {
      let valid = true;
      let firstInvalid = null;

      requiredFields.forEach(f => {
        const el = document.getElementById(f.id);
        if (!el) return;
        const value = (el.tagName.toLowerCase() === 'select') ? el.value : el.value.trim();
        if (!value) {
          valid = false;
          showError(el, f.err);
          if (!firstInvalid) firstInvalid = el;
        } else {
          clearError(el, f.err);
        }
      });

      if (!valid) {
        ev.preventDefault();
        if (firstInvalid) {
          firstInvalid.focus();
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    // Clear error styling ketika user input
    requiredFields.forEach(f => {
      const el = document.getElementById(f.id);
      if (!el) return;
      el.addEventListener('input', () => clearError(el, f.err));
      el.addEventListener('change', () => clearError(el, f.err));
    });
  })();
</script>

<?= $this->endSection() ?>
