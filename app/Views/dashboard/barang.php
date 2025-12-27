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

        <?php 
            $request   = \Config\Services::request();
            $q         = $request->getGet('q');
            $condition = $request->getGet('condition');
            $perPage   = (int)($request->getGet('perPage') ?? ($pager['perPage'] ?? 10));
            if (! in_array($perPage, [10,20,50,100])) $perPage = $pager['perPage'] ?? 10;
        ?>

        <!-- FILTER & SEARCH & PERPAGE -->
        <form method="get" action="<?= site_url('assets') ?>" class="mb-4 flex flex-col md:flex-row gap-3 items-start md:items-end">
            <div class="flex-1">
                <label for="q" class="block text-xs text-gray-600 mb-1">Pencarian</label>
                <input
                    type="text"
                    id="q"
                    name="q"
                    value="<?= esc($q) ?>"
                    placeholder="Cari jenis, unit, no inventaris, NPD, pengguna..."
                    class="w-full border rounded px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label for="condition" class="block text-xs text-gray-600 mb-1">Sort / Filter Keadaan</label>
                <select id="condition" name="condition" class="border rounded px-3 py-2 text-sm">
                    <option value="">Semua kondisi</option>
                    <option value="baik"     <?= $condition === 'baik'     ? 'selected' : '' ?>>Baik</option>
                    <option value="rusak"    <?= $condition === 'rusak'    ? 'selected' : '' ?>>Rusak</option>
                    <option value="dipinjam" <?= $condition === 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                    <option value="disposal" <?= $condition === 'disposal' ? 'selected' : '' ?>>Disposal</option>
                    <option value="diganti" <?= $condition === 'diganti' ? 'selected' : '' ?>>Diganti</option>
                </select>
            </div>

            <div>
                <label for="perPage" class="block text-xs text-gray-600 mb-1">Tampilkan</label>
                <select id="perPage" name="perPage" class="border rounded px-3 py-2 text-sm">
                    <option value="10"  <?= $perPage === 10  ? 'selected' : '' ?>>10</option>
                    <option value="20"  <?= $perPage === 20  ? 'selected' : '' ?>>20</option>
                    <option value="50"  <?= $perPage === 50  ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm mt-1 md:mt-0">
                    Terapkan
                </button>
                <a href="<?= site_url('assets') ?>" class="px-4 py-2 bg-gray-200 rounded text-sm mt-1 md:mt-0">
                    Reset
                </a>
            </div>
        </form>

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
        </div>

        <?php
            $total     = $pager['total']   ?? 0;
            $page      = $pager['page']    ?? 1;
            $perPage   = $pager['perPage'] ?? 10;
            $totalPage = $total > 0 ? (int)ceil($total / $perPage) : 1;

            if ($page > $totalPage) $page = $totalPage;

            // base param utk pagination link
            $baseParams = [
                'q'         => $q,
                'condition' => $condition,
                'perPage'   => $perPage,
            ];
            function pageUrlLocal($pageNum, $baseParams) {
                $params = array_merge($baseParams, ['page' => $pageNum]);
                return site_url('assets') . '?' . http_build_query($params);
            }
        ?>

        <!-- INFO + PAGINATION DI LUAR DIV TABLE (BIAR GA NGARUH KE TABEL) -->
        <div class="mt-4 space-y-2 text-sm text-gray-600">
            <div>
                Menampilkan <?= count($assets ?? []) ?> dari <?= esc($total) ?> item
            </div>

            <?php if ($totalPage > 1): ?>
                <div class="flex justify-center">
                    <nav class="inline-flex items-center gap-1">
                        <!-- Previous -->
                        <?php if ($page > 1): ?>
                            <a href="<?= pageUrlLocal($page-1, $baseParams) ?>"
                               class="px-2 py-1 border rounded hover:bg-gray-100 text-xs">&laquo;</a>
                        <?php endif; ?>

                        <?php
                            // kalau halaman banyak, batasi range angka biar nggak kepanjangan
                            $start = max(1, $page - 2);
                            $end   = min($totalPage, $page + 2);

                            if ($start > 1) {
                                echo '<span class="px-2 py-1 text-xs">...</span>';
                            }

                            for ($i = $start; $i <= $end; $i++):
                                $isActive = $i == $page;
                        ?>
                            <a href="<?= pageUrlLocal($i, $baseParams) ?>"
                               class="px-3 py-1 border rounded text-xs <?= $isActive ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($end < $totalPage): ?>
                            <span class="px-2 py-1 text-xs">...</span>
                        <?php endif; ?>

                        <!-- Next -->
                        <?php if ($page < $totalPage): ?>
                            <a href="<?= pageUrlLocal($page+1, $baseParams) ?>"
                               class="px-2 py-1 border rounded hover:bg-gray-100 text-xs">&raquo;</a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
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
                <a href="<?= site_url('/input') ?>"
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
