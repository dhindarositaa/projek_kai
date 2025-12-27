<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-8">
  <div class="w-full px-6 space-y-6">

    <!-- CARD -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-300 overflow-hidden">

      <!-- HEADER -->
      <div class="p-8 border-b border-gray-300 bg-gray-50">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
            Input Data Manual
          </h2>
          <button class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold shadow">
            Form
          </button>
        </div>
      </div>

      <!-- FORM -->
      <form id="inventoryForm"
            action="<?= site_url('input/store') ?>"
            method="post"
            class="p-8 space-y-10 text-base">
        <?= csrf_field() ?>

        <!-- A. INFORMASI PENGADAAN -->
        <fieldset>
          <legend class="text-sm font-bold text-gray-800 uppercase mb-5 tracking-wide">
            A. Informasi Pengadaan
          </legend>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <?php
            function inputClass() {
              return 'mt-2 w-full rounded-lg border-2 border-gray-400 px-4 py-3
                      text-base text-gray-800
                      focus:border-blue-600 focus:ring-2 focus:ring-blue-200
                      outline-none transition';
            }
            ?>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                No RAB <span class="text-red-600">*</span>
              </label>
              <input type="text" name="no_rab" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                No NPD <span class="text-red-600">*</span>
              </label>
              <input type="text" name="no_npd" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Tanggal Pengadaan <span class="text-red-600">*</span>
              </label>
              <input type="date" name="tanggal_pengadaan" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                No BAST BMC
              </label>
              <input type="text" name="no_bast_bmc" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                No WO BAST
              </label>
              <input type="text" name="no_wo_bast" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Link File BAST
              </label>
              <input type="url" placeholder="https://..." name="link_bast" class="<?= inputClass() ?>">
            </div>
          </div>
        </fieldset>

        <hr class="border-t-2 border-gray-300">

        <!-- B. INFORMASI PERANGKAT -->
        <fieldset>
          <legend class="text-sm font-bold text-gray-800 uppercase mb-5 tracking-wide">
            B. Informasi Perangkat
          </legend>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Jenis Perangkat <span class="text-red-600">*</span>
              </label>
              <input type="text" placeholder="Laptop, Printer, Router"
                     name="jenis_perangkat" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Merek / Tipe <span class="text-red-600">*</span>
              </label>
              <input type="text" name="merk_tipe" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Serial Number <span class="text-red-600">*</span>
              </label>
              <input type="text" name="serial_number" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                No Inventaris <span class="text-red-600">*</span>
              </label>
              <input type="text" name="no_inventaris" class="<?= inputClass() ?>">
            </div>

            <div class="md:col-span-2">
              <label class="text-sm font-semibold text-gray-800">
                Spesifikasi
              </label>
              <textarea rows="4" name="spesifikasi"
                class="<?= inputClass() ?>"></textarea>
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Link Dokumen
              </label>
              <input type="url" placeholder="https://..."
                     name="link_dokumen" class="<?= inputClass() ?>">
            </div>
          </div>
        </fieldset>

        <hr class="border-t-2 border-gray-300">

        <!-- C. INFORMASI PENGGUNA -->
        <fieldset>
          <legend class="text-sm font-bold text-gray-800 uppercase mb-5 tracking-wide">
            C. Informasi Pengguna
          </legend>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Nama Pengguna
              </label>
              <input type="text" name="nama_pengguna" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Unit <span class="text-red-600">*</span>
              </label>
              <input type="text" name="unit" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                NIPP
              </label>
              <input type="text" name="nipp" class="<?= inputClass() ?>">
            </div>

            <div>
              <label class="text-sm font-semibold text-gray-800">
                Kondisi <span class="text-red-600">*</span>
              </label>
              <select name="condition" class="<?= inputClass() ?>">
                <option value="">Pilih...</option>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="disposal">Disposal</option>
                <option value="diganti">Diganti</option>
              </select>
            </div>

            <div class="md:col-span-3">
              <label class="text-sm font-semibold text-gray-800">
                Keterangan
              </label>
              <textarea rows="4" name="keterangan"
                class="<?= inputClass() ?>"></textarea>
            </div>
          </div>
        </fieldset>

        <!-- BUTTON -->
        <div class="flex justify-end gap-4 pt-6">
          <button type="reset"
            class="px-6 py-3 rounded-lg border-2 border-gray-400 font-semibold text-gray-700 hover:bg-gray-100">
            Reset
          </button>
          <button type="submit"
            class="px-6 py-3 rounded-lg bg-blue-600 text-white font-semibold shadow hover:bg-blue-700">
            Simpan
          </button>
        </div>

      </form>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
