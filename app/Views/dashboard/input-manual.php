<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- MAIN CONTENT -->
<section class="py-6">
  <!-- Container full-width (max width for readability) -->
  <div class="w-full px-6 space-y-6">
    
    <!-- CARD FULL WIDTH -->
    <section class="grid grid-cols-1">
      <div class="card-bg rounded shadow-sm overflow-hidden w-full bg-white">
        
        <!-- Header -->
        <div class="p-6 border-b">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">
              Input Data Manual
            </h2>
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded">
              Form
            </button>
          </div>
        </div>

        <!-- FORM -->
      <form id="inventoryForm" class="p-6 space-y-6"
      action="<?= site_url('input/store') ?>" method="post" novalidate>
          <?= csrf_field() ?>

          <!-- A. Informasi Pengadaan -->
          <fieldset>
            <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">
              A. Informasi Pengadaan
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- No RAB (wajib) -->
              <div>
                <label for="no_rab" class="text-xs text-gray-700">
                  No RAB <span class="text-red-600">*</span>
                </label>
                <input id="no_rab" name="no_rab" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_no_rab" class="hidden text-xs text-red-600 mt-1">
                  No RAB wajib diisi.
                </p>
              </div>

              <!-- No NPD (wajib) -->
              <div>
                <label for="no_npd" class="text-xs text-gray-700">
                  No NPD <span class="text-red-600">*</span>
                </label>
                <input id="no_npd" name="no_npd" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_no_npd" class="hidden text-xs text-red-600 mt-1">
                  No NPD wajib diisi.
                </p>
              </div>

              <!-- Tanggal Pengadaan (wajib) -->
              <div>
                <label for="tanggal_pengadaan" class="text-xs text-gray-700">
                  Tanggal Pengadaan <span class="text-red-600">*</span>
                </label>
                <input id="tanggal_pengadaan" name="tanggal_pengadaan" type="date" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_tanggal_pengadaan" class="hidden text-xs text-red-600 mt-1">
                  Tanggal Pengadaan wajib diisi.
                </p>
              </div>

              <!-- No BAST BMC (opsional) -->
              <div>
                <label for="no_bast_bmc" class="text-xs text-gray-700">
                  No BAST BMC
                </label>
                <input id="no_bast_bmc" name="no_bast_bmc" type="text"
                       class="mt-1 w-full border rounded px-3 py-2" />
              </div>

              <!-- No WO BAST (opsional) -->
              <div>
                <label for="no_wo_bast" class="text-xs text-gray-700">
                  No WO BAST
                </label>
                <input id="no_wo_bast" name="no_wo_bast" type="text"
                       class="mt-1 w-full border rounded px-3 py-2" />
              </div>

              <!-- Link File BAST (opsional) -->
              <div>
                <label for="link_bast" class="text-xs text-gray-700">
                  Link File BAST
                </label>
                <input id="link_bast" name="link_bast" type="url" placeholder="https://..."
                       class="mt-1 w-full border rounded px-3 py-2" />
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
              <!-- Jenis Perangkat (wajib) - INPUT TEXT -->
              <div>
                <label for="jenis_perangkat" class="text-xs text-gray-700">
                  Jenis Perangkat <span class="text-red-600">*</span>
                </label>
                <input id="jenis_perangkat" name="jenis_perangkat" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2"
                       placeholder="Contoh: Laptop, Komputer, Printer, Router" />
                <p id="err_jenis_perangkat" class="hidden text-xs text-red-600 mt-1">
                  Jenis Perangkat wajib diisi.
                </p>
              </div>

              <!-- Merek/Tipe (wajib) -->
              <div>
                <label for="merk_tipe" class="text-xs text-gray-700">
                  Merek / Tipe <span class="text-red-600">*</span>
                </label>
                <input id="merk_tipe" name="merk_tipe" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_merk_tipe" class="hidden text-xs text-red-600 mt-1">
                  Merek / Tipe wajib diisi.
                </p>
              </div>

              <!-- Serial Number (wajib) -->
              <div>
                <label for="serial_number" class="text-xs text-gray-700">
                  Serial Number <span class="text-red-600">*</span>
                </label>
                <input id="serial_number" name="serial_number" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_serial_number" class="hidden text-xs text-red-600 mt-1">
                  Serial Number wajib diisi.
                </p>
              </div>

              <!-- No Inventaris (wajib) -->
              <div>
                <label for="no_inventaris" class="text-xs text-gray-700">
                  No Inventaris <span class="text-red-600">*</span>
                </label>
                <input id="no_inventaris" name="no_inventaris" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_no_inventaris" class="hidden text-xs text-red-600 mt-1">
                  No Inventaris wajib diisi.
                </p>
              </div>

              <!-- Spesifikasi (opsional, full width) -->
              <div class="md:col-span-2">
                <label for="spesifikasi" class="text-xs text-gray-700">
                  Spesifikasi
                </label>
                <textarea id="spesifikasi" name="spesifikasi" rows="3"
                          placeholder="CPU, RAM, Storage, OS, dll."
                          class="mt-1 w-full border rounded px-3 py-2"></textarea>
              </div>

              <!-- Link Dokumen (opsional) -->
              <div>
                <label for="link_dokumen" class="text-xs text-gray-700">
                  Link Dokumen (Opsional)
                </label>
                <input id="link_dokumen" name="link_dokumen" type="url" placeholder="https://..."
                       class="mt-1 w-full border rounded px-3 py-2" />
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
              <!-- Nama Pengguna (opsional) -->
              <div>
                <label for="nama_pengguna" class="text-xs text-gray-700">
                  Nama Pengguna
                </label>
                <input id="nama_pengguna" name="nama_pengguna" type="text"
                       class="mt-1 w-full border rounded px-3 py-2" />
              </div>

              <!-- Unit (wajib) -->
              <div>
                <label for="unit" class="text-xs text-gray-700">
                  Unit <span class="text-red-600">*</span>
                </label>
                <input id="unit" name="unit" type="text" required aria-required="true"
                       class="mt-1 w-full border rounded px-3 py-2" />
                <p id="err_unit" class="hidden text-xs text-red-600 mt-1">
                  Unit wajib diisi.
                </p>
              </div>

              <!-- NIPP (opsional) -->
              <div>
                <label for="nipp" class="text-xs text-gray-700">
                  NIPP
                </label>
                <input id="nipp" name="nipp" type="text"
                       class="mt-1 w-full border rounded px-3 py-2" />
              </div>

              <!-- Kondisi (wajib) -->
              <div>
                <label for="condition" class="text-xs text-gray-700">
                  Kondisi <span class="text-red-600">*</span>
                </label>
                <select id="condition" name="condition" required aria-required="true"
                        class="mt-1 w-full border rounded px-3 py-2">
                  <option value="">Pilih...</option>
                  <option value="baik">baik</option>
                  <option value="rusak">rusak</option>
                  <option value="dipinjam">dipinjam</option>
                  <option value="disposal">disposal</option>
                </select>
                <p id="err_condition" class="hidden text-xs text-red-600 mt-1">
                  Kondisi wajib dipilih.
                </p>
              </div>
              <!-- Keterangan (opsional, full width) -->
              <div class="md:col-span-3">
                <label for="keterangan" class="text-xs text-gray-700">
                  Keterangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          placeholder="Catatan tambahan terkait perangkat..."
                          class="mt-1 w-full border rounded px-3 py-2"></textarea>
              </div>
            </div>
          </fieldset>

          <!-- Tombol -->
          <div class="flex items-center justify-end gap-3">
            <button type="reset" class="px-4 py-2 rounded border border-gray-200">
              Reset
            </button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">
              Simpan
            </button>
          </div>

        </form>
      </div>
    </section>
  </div>
</section>

<!-- CLIENT-SIDE VALIDATION SCRIPT -->
<script>
  (function () {
    const form = document.getElementById('inventoryForm');

    const requiredFields = [
      { id: 'no_rab',            err: 'err_no_rab' },
      { id: 'no_npd',            err: 'err_no_npd' },
      { id: 'tanggal_pengadaan', err: 'err_tanggal_pengadaan' },
      { id: 'jenis_perangkat',   err: 'err_jenis_perangkat' },
      { id: 'merk_tipe',         err: 'err_merk_tipe' },
      { id: 'serial_number',     err: 'err_serial_number' },
      { id: 'no_inventaris',     err: 'err_no_inventaris' },
      { id: 'condition',         err: 'err_condition' },
      { id: 'unit',              err: 'err_unit' }
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

        const value = el.value.trim();
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

    requiredFields.forEach(f => {
      const el = document.getElementById(f.id);
      if (!el) return;
      el.addEventListener('assets', () => clearError(el, f.err));
      el.addEventListener('change', () => clearError(el, f.err));
    });
  })();
</script>

<?= $this->endSection() ?>
