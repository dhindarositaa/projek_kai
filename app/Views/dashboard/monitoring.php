<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4 space-y-6">

    <!-- HEADER -->
    <header>
      <h1 class="text-2xl font-semibold text-slate-800">
        <?= esc($page_title ?? 'Daftar Aset') ?>
      </h1>
      <p class="text-sm text-slate-500">
        Pilih aset yang akan diubah statusnya menjadi <b>Digantikan</b>.
      </p>
    </header>

    <!-- FORM -->
    <form id="bulkForm"
          method="post"
          action="<?= site_url('assets/monitoring-status') ?>">
      <?= csrf_field() ?>

      <!-- ACTION BAR -->
      <div class="mb-3">
        <button type="button"
                id="btnChangeStatus"
                class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
          Ubah Status → Digantikan
        </button>
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-2xl shadow overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-4 py-3 text-center">
                <input type="checkbox" id="checkAll">
              </th>
              <th class="px-4 py-3 text-left">Nama Aset</th>
              <th class="px-4 py-3 text-left">Kode</th>
              <th class="px-4 py-3 text-left">Unit</th>
              <th class="px-4 py-3 text-left">PIC</th>
              <th class="px-4 py-3 text-left">Tahun ke-</th>
              <th class="px-4 py-3 text-left">Status</th>
            </tr>
          </thead>

          <tbody>
            <?php if (!empty($assets)): foreach ($assets as $a): ?>
              <tr class="border-t hover:bg-slate-50">
                <td class="px-4 py-2 text-center">
                  <input type="checkbox"
                         class="item-checkbox"
                         name="asset_ids[]"
                         value="<?= $a['id'] ?>">
                </td>
                <td class="px-4 py-2 font-medium"><?= esc($a['asset_name']) ?></td>
                <td class="px-4 py-2"><?= esc($a['asset_code']) ?></td>
                <td class="px-4 py-2"><?= esc($a['unit_name'] ?? '-') ?></td>
                <td class="px-4 py-2"><?= esc($a['employee_name'] ?? '-') ?></td>
                <td class="px-4 py-2"><?= esc($a['tahun_ke']) ?></td>
                <td class="px-4 py-2">
                  <?= esc($a['condition'] ?? '-') ?>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr>
                <td colspan="7" class="text-center py-6 text-slate-500">
                  Tidak ada data aset.
                </td>
              </tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>

    </form>
  </div>
</section>

<!-- MODAL KONFIRMASI -->
<div id="confirmModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-slate-800 mb-2">
      Konfirmasi Perubahan Status
    </h3>
    <p class="text-sm text-slate-600 mb-4">
      Anda yakin ingin mengubah status <b>barang yang dipilih</b> menjadi
      <span class="font-semibold text-red-600">Digantikan</span>?
      <br><br>
      Tindakan ini tidak dapat dibatalkan.
    </p>

    <div class="flex justify-end gap-2">
      <button type="button"
              id="cancelModal"
              class="px-4 py-2 text-sm rounded-lg bg-slate-200">
        Batal
      </button>
      <button type="button"
              id="confirmSubmit"
              class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white">
        Ya, Ubah Status
      </button>
    </div>
  </div>
</div>

<!-- JS -->
<script>
  const checkAll   = document.getElementById('checkAll');
  const items     = document.querySelectorAll('.item-checkbox');
  const btnAction = document.getElementById('btnChangeStatus');
  const modal     = document.getElementById('confirmModal');
  const cancelBtn = document.getElementById('cancelModal');
  const confirmBtn= document.getElementById('confirmSubmit');
  const form      = document.getElementById('bulkForm');

  // check all
  checkAll.addEventListener('change', () => {
    items.forEach(cb => cb.checked = checkAll.checked);
  });

  // open modal
  btnAction.addEventListener('click', () => {
    const checked = [...items].some(i => i.checked);
    if (!checked) {
      alert('Pilih minimal satu barang.');
      return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });

  // cancel
  cancelBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // confirm submit
  confirmBtn.addEventListener('click', () => {
    form.submit();
  });
</script>

<?= $this->endSection() ?>
