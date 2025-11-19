<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="rounded-lg shadow bg-white overflow-hidden mb-10">
    <div class="p-6 border-b flex items-center justify-between">
        <h3 class="text-lg font-semibold"><?= esc($title ?? 'Daftar Semua Barang') ?></h3>

        <!-- Tombol buka modal -->
        <button id="openAddModalBtn" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Tambah Barang</button>
    </div>

    <div class="p-6">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <?php
                    $err = session()->getFlashdata('error');
                    if (is_array($err)) {
                        foreach ($err as $e) echo "<div>".esc($e)."</div>";
                    } else echo esc($err);
                ?>
            </div>
        <?php endif; ?>

        <!-- TABLE ASSETS -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3 px-3">Jenis Perangkat</th>
                        <th class="py-3 px-3">Tanggal Pengadaan</th>
                        <th class="py-3 px-3">NO NPD</th>
                        <th class="py-3 px-3">Unit</th>
                        <th class="py-3 px-3">No Inventaris</th>
                        <th class="py-3 px-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-sm text-gray-700 divide-y">
                    <?php if (!empty($assets)): ?>
                        <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td class="py-4 px-3"><?= esc($no++) ?></td>

                            <td class="py-4 px-3">
                                <?= esc(!empty($asset['brand']) ? ($asset['brand'] . ' ' . ($asset['model_name'] ?? '')) : ($asset['specification'] ?? '-')) ?>
                            </td>

                            <td class="py-4 px-3">
                                <?php
                                    $date = $asset['procurement_date'] ?? $asset['purchase_date'] ?? null;
                                    echo $date ? date('d/m/Y', strtotime($date)) : '-';
                                ?>
                            </td>

                            <td class="py-4 px-3"><?= esc($asset['no_npd'] ?? '-') ?></td>
                            <td class="py-4 px-3"><?= esc($asset['unit_name'] ?? '-') ?></td>
                            <td class="py-4 px-3"><?= esc($asset['asset_code'] ?? '-') ?></td>

                            <td class="py-4 px-3 flex gap-2">
                                <a href="<?= site_url('assets/'.$asset['id'].'/edit') ?>" class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
                                <a href="<?= site_url('assets/'.$asset['id']) ?>" class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>

                                <form action="<?= site_url('assets/'.$asset['id'].'/delete') ?>" method="post" onsubmit="return confirm('Hapus data ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-4 px-3 text-center text-gray-500">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="mt-4 text-sm text-gray-600">
                Menampilkan <?= count($assets ?? []) ?> dari <?= esc($pager['total'] ?? 0) ?> item
            </div>
        </div>
    </div>
</div>

<!-- =======================
       MODAL TAMBAH BARANG
======================== -->
<div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div id="modalOverlay" class="absolute inset-0 bg-black opacity-40"></div>

    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="p-4 border-b">
            <h4 class="text-lg font-semibold">Tambah Barang</h4>
        </div>

        <div class="p-4 space-y-4">

            <p class="text-sm text-gray-600">Pilih metode penambahan data:</p>

            <div class="flex flex-col sm:flex-row gap-3">

                <!-- Halaman Input Bulk -->
                <a href="<?= site_url('/bulk-input') ?>"
                   class="flex-1 px-4 py-2 bg-yellow-500 text-white rounded text-sm text-center hover:bg-yellow-600">
                    Input Bulk
                </a>

                <!-- Halaman Input Manual -->
                <a href="<?= site_url('/input-manual') ?>"
                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded text-sm text-center hover:bg-blue-700">
                    Input Manual
                </a>

            </div>

            <div class="flex justify-end mt-4">
                <button id="cancelModalBtn" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('addModal');
    const overlay = document.getElementById('modalOverlay');
    const openBtn = document.getElementById('openAddModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    openBtn.addEventListener('click', showModal);
    overlay.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hideModal();
    });
});
</script>

<?= $this->endSection() ?>
