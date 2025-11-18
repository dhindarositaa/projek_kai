<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="rounded-lg shadow bg-white overflow-hidden mb-10">
    <div class="p-6 border-b flex items-center justify-between">
        <h3 class="text-lg font-semibold"><?= esc($title ?? 'Daftar Semua Barang') ?></h3>
        <a href="<?= site_url('assets/create') ?>" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Tambah Barang</a>
    </div>

    <div class="p-6">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

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
                    <?php if (!empty($assets) && is_array($assets)): ?>
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

                                    <!-- delete via POST form -->
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

            <!-- simple pager summary -->
            <div class="mt-4 text-sm text-gray-600">
                Menampilkan <?= esc(is_array($assets) ? count($assets) : 0) ?> dari <?= esc($pager['total'] ?? 0) ?> item
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
