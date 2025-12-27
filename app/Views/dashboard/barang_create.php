<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4">
    <section class="grid grid-cols-1">
      <div class="card-bg rounded shadow-sm overflow-hidden w-full bg-white border border-gray-200">

        <!-- HEADER -->
        <div class="p-6 border-b border-gray-300 bg-gray-50">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-800">
              <?= esc($title ?? (isset($asset) ? 'Edit Barang' : 'Input Data Manual')) ?>
            </h2>
            <a href="<?= site_url('assets') ?>"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">
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

                <!-- No RAB -->
                <div>
                  <label class="text-xs font-medium text-gray-800">
                    No RAB <span class="text-red-600">*</span>
                  </label>
                  <input id="proc_no_rab" name="proc_no_rab" type="text" required
                         value="<?= esc(old('proc_no_rab') ?? ($asset['no_rab'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm
                                focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none" />
                </div>

                <!-- No NPD -->
                <div>
                  <label class="text-xs font-medium text-gray-800">
                    No NPD <span class="text-red-600">*</span>
                  </label>
                  <input id="proc_no_npd" name="proc_no_npd" type="text" required
                         value="<?= esc(old('proc_no_npd') ?? ($asset['no_npd'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm
                                focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none" />
                </div>

                <!-- Tanggal -->
                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Tanggal Pengadaan <span class="text-red-600">*</span>
                  </label>
                  <input id="procurement_date" name="procurement_date" type="date" required
                         value="<?= esc(old('procurement_date') ?? ($asset['procurement_date'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm
                                focus:border-blue-600 focus:ring-2 focus:ring-blue-200 outline-none" />
                </div>

                <!-- No BAST -->
                <div>
                  <label class="text-xs font-medium text-gray-800">No BAST BMC</label>
                  <input id="no_bast_bmc" name="no_bast_bmc" type="text"
                         value="<?= esc(old('no_bast_bmc') ?? ($asset['no_bast_bmc'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <!-- No WO -->
                <div>
                  <label class="text-xs font-medium text-gray-800">No WO BAST</label>
                  <input id="no_wo_bast" name="no_wo_bast" type="text"
                         value="<?= esc(old('no_wo_bast') ?? ($asset['no_wo_bast'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <!-- Link -->
                <div>
                  <label class="text-xs font-medium text-gray-800">Link File BAST</label>
                  <input id="link_bast" name="link_bast" type="url"
                         placeholder="https://..."
                         value="<?= esc(old('link_bast') ?? ($asset['link_bast'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

              </div>
            </fieldset>

            <hr class="border-t-2 border-gray-300 my-6"/>

            <!-- B. INFORMASI PERANGKAT -->
            <fieldset>
              <legend class="text-xs font-semibold uppercase mb-3 text-gray-800">
                B. Informasi Perangkat
              </legend>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Jenis Perangkat <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_brand" name="asset_brand" type="text" required
                         value="<?= esc(old('asset_brand') ?? ($asset['brand'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Merek / Tipe <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_model_name" name="asset_model_name" type="text" required
                         value="<?= esc(old('asset_model_name') ?? ($asset['model_name'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Serial Number <span class="text-red-600">*</span>
                  </label>
                  <input id="serial_number" name="serial_number" type="text" required
                         value="<?= esc(old('serial_number') ?? ($asset['serial_number'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    No Inventaris <span class="text-red-600">*</span>
                  </label>
                  <input id="asset_code" name="asset_code" type="text" required
                         value="<?= esc(old('asset_code') ?? ($asset['asset_code'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div class="md:col-span-2">
                  <label class="text-xs font-medium text-gray-800">Spesifikasi</label>
                  <textarea id="specification" name="specification" rows="3"
                            class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm"><?= esc(old('specification') ?? ($asset['specification'] ?? '')) ?></textarea>
                </div>

              </div>
            </fieldset>

            <hr class="border-t-2 border-gray-300 my-6"/>

            <!-- C. INFORMASI PENGGUNA -->
            <fieldset>
              <legend class="text-xs font-semibold uppercase mb-3 text-gray-800">
                C. Informasi Pengguna
              </legend>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                  <label class="text-xs font-medium text-gray-800">Nama Pengguna</label>
                  <input id="employee_name" name="employee_name" type="text"
                         value="<?= esc(old('employee_name') ?? ($asset['employee_name'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Unit <span class="text-red-600">*</span>
                  </label>
                  <input id="unit_name" name="unit_name" type="text" required
                         value="<?= esc(old('unit_name') ?? ($asset['unit_name'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">NIPP</label>
                  <input id="employee_nipp" name="employee_nipp" type="text"
                         value="<?= esc(old('employee_nipp') ?? ($asset['employee_nipp'] ?? '')) ?>"
                         class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm" />
                </div>

                <div>
                  <label class="text-xs font-medium text-gray-800">
                    Kondisi <span class="text-red-600">*</span>
                  </label>
                  <?php $cond = old('condition') ?? ($asset['condition'] ?? ''); ?>
                  <select id="condition" name="condition" required
                          class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm">
                    <option value="">Pilih...</option>
                    <option value="baik" <?= $cond==='baik'?'selected':'' ?>>baik</option>
                    <option value="rusak" <?= $cond==='rusak'?'selected':'' ?>>rusak</option>
                    <option value="dipinjam" <?= $cond==='dipinjam'?'selected':'' ?>>dipinjam</option>
                    <option value="disposal" <?= $cond==='disposal'?'selected':'' ?>>disposal</option>
                  </select>
                </div>

                <div class="md:col-span-3">
                  <label class="text-xs font-medium text-gray-800">Keterangan</label>
                  <textarea id="keterangan" name="keterangan" rows="3"
                            class="mt-1 w-full rounded border-2 border-gray-400 px-3 py-2 text-sm"><?= esc(old('keterangan') ?? ($asset['keterangan'] ?? '')) ?></textarea>
                </div>

              </div>
            </fieldset>

            <!-- BUTTON -->
            <div class="flex justify-end gap-3">
              <button type="reset"
                      class="px-4 py-2 rounded border border-gray-300 text-sm">
                Reset
              </button>
              <button type="submit"
                      class="px-4 py-2 rounded bg-blue-600 text-white text-sm">
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
