<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="py-6">
  <div class="max-w-screen-xl mx-auto px-4 space-y-6">

    <header>
      <h1 class="text-2xl font-semibold text-slate-800">
        <?= esc($page_title) ?>
      </h1>
      <p class="text-sm text-slate-500">
        Pilih aset yang akan diubah statusnya menjadi <b>Diganti</b>.
      </p>
    </header>

    <form id="bulkForm" method="post"
          action="<?= site_url('assets/monitoring-status') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="mode" value="selected">

      <button type="button" id="btnChangeStatus"
              class="mb-3 px-4 py-2 bg-red-600 text-white rounded">
        Ubah Status → Diganti
      </button>

      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-4 py-3 text-center">
                <input type="checkbox" id="checkAll">
              </th>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">Unit</th>
              <th class="px-4 py-3">PIC</th>
              <th class="px-4 py-3">Tahun</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($assets as $a): ?>
              <tr class="border-t">
                <td class="text-center">
                  <input type="checkbox"
                         class="item-checkbox"
                         name="asset_ids[]"
                         value="<?= $a['id'] ?>">
                </td>
                <td><?= esc($a['asset_name']) ?></td>
                <td><?= esc($a['asset_code']) ?></td>
                <td><?= esc($a['unit_name'] ?? '-') ?></td>
                <td><?= esc($a['employee_name'] ?? '-') ?></td>
                <td><?= esc($a['tahun_ke']) ?></td>
                <td><?= esc($a['condition']) ?></td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</section>

<script>
const checkAll = document.getElementById('checkAll');
const items   = document.querySelectorAll('.item-checkbox');
const btn     = document.getElementById('btnChangeStatus');
const form    = document.getElementById('bulkForm');

checkAll.addEventListener('change', () => {
  items.forEach(cb => cb.checked = checkAll.checked);
});

btn.addEventListener('click', () => {
  const selected = [...items].some(i => i.checked);
  if (!selected) {
    alert('Pilih minimal satu aset.');
    return;
  }
  if (confirm('Yakin ubah status aset terpilih menjadi Diganti?')) {
    form.submit();
  }
});
</script>

<?= $this->endSection() ?>
