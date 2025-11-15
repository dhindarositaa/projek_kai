<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- MAIN CONTENT (akan discroll terpisah dari stats/header) -->
          <section class="py-6">
            <!-- FORM + PROFILE (responsive layout) -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- FORM (wide) -->
              <div class="lg:col-span-2 card-bg rounded shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                  <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Input Data Manual</h2>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Form</button>
                  </div>
                </div>

                <form class="p-6 space-y-6" action="#" method="post" novalidate>
                  <!-- A. Informasi Pengadaan -->
                  <fieldset>
                    <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">A. Informasi Pengadaan</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="text-xs text-gray-700">No RAB</label>
                        <input name="no_rab" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs text-gray-700">No NPD</label>
                        <input name="no_npd" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">Tanggal Pengadaan</label>
                        <input name="tanggal_pengadaan" type="date" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs text-gray-700">No BAST BMC</label>
                        <input name="no_bast_bmc" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">No WO BAST</label>
                        <input name="no_wo_bast" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs text-gray-700">Link File BAST</label>
                        <input name="link_bast" type="url" placeholder="https://..." class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                    </div>
                  </fieldset>

                  <hr class="border-t" />

                  <!-- B. Informasi Perangkat -->
                  <fieldset>
                    <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">B. Informasi Perangkat</legend>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="text-xs text-gray-700">Jenis Perangkat</label>
                        <select name="jenis_perangkat" class="mt-1 w-full border rounded px-3 py-2">
                          <option value="">Pilih...</option>
                          <option>Komputer</option>
                          <option>Laptop</option>
                          <option>Printer</option>
                          <option>Router</option>
                        </select>
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">Merk / Tipe</label>
                        <input name="merk_tipe" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">Serial Number</label>
                        <input name="serial_number" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">No Inventaris</label>
                        <input name="no_inventaris" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>

                      <div class="md:col-span-2">
                        <label class="text-xs text-gray-700">Spesifikasi</label>
                        <textarea name="spesifikasi" rows="3" class="mt-1 w-full border rounded px-3 py-2" placeholder="CPU, RAM, Storage, OS, dll."></textarea>
                      </div>

                      <div>
                        <label class="text-xs text-gray-700">Upload Dokumen (opsional)</label>
                        <input name="dokumen" type="file" class="mt-1 w-full" />
                      </div>
                    </div>
                  </fieldset>

                  <hr class="border-t" />

                  <!-- C. Informasi Pengguna -->
                  <fieldset>
                    <legend class="text-xs text-gray-700 uppercase font-semibold mb-3">C. Informasi Pengguna</legend>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label class="text-xs text-gray-700">Nama Pengguna</label>
                        <input name="nama_pengguna" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs text-gray-700">Unit / Bagian</label>
                        <input name="unit" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs text-gray-700">NIPP</label>
                        <input name="nipp" type="text" class="mt-1 w-full border rounded px-3 py-2" />
                      </div>
                    </div>
                  </fieldset>

                  <div class="flex items-center justify-end gap-3">
                    <button type="reset" class="px-4 py-2 rounded border border-gray-200">Reset</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Simpan</button>
                  </div>
                </form>
              </div>

              <!-- PROFILE / SUMMARY (side card) -->
              <aside class="card-bg rounded shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Profil Perangkat</h3>

                <dl class="text-sm text-gray-700 space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Status</dt>
                    <dd>Aktif</dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Lokasi Pengguna</dt>
                    <dd>Ruang IT — Lantai 2</dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Terakhir Update</dt>
                    <dd>2025-11-12</dd>
                  </div>
                </dl>

                <div class="mt-6">
                  <button class="w-full px-4 py-2 rounded bg-green-600 text-white">Export CSV</button>
                </div>
              </aside>
            </section>
          </section>

        </main>
      </div>
    </div>

    <!-- Simple script to toggle mobile sidebar -->
    <script>
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const openBtn = document.getElementById('openSidebarBtn');
      const closeBtn = document.getElementById('closeSidebarBtn');

      function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
      }

      openBtn?.addEventListener('click', openSidebar);
      closeBtn?.addEventListener('click', closeSidebar);
      overlay?.addEventListener('click', closeSidebar);

      // close sidebar on Escape
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
      });
    </script>

<?= $this->endSection() ?>
