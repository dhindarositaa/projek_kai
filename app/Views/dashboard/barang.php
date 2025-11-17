<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="bg-red-500 rounded-lg shadow mb-12 overflow-hidden">
  <div class="p-6 border-b flex items-center justify-between">
    <h3 class="text-lg font-semibold">Card Tables</h3>
    <button class="bg-gray-600 text-white px-4 py-2 rounded">LIHAT SEMUA</button>
  </div>

  <div class="p-6 bg-white">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left">
        <thead class="text-xs text-gray-500 uppercase">
          <tr>
            <th class="py-3">Jenis Perangkat</th>
            <th class="py-3">Tanggal Pengadaan</th>
            <th class="py-3">NO NPD</th>
            <th class="py-3">Unit</th>
            <th class="py-3">No Inventaris</th>
            <th class="py-3">Aksi</th>
          </tr>
        </thead>

        <tbody class="text-sm text-gray-700 divide-y">
          <!-- 5 data -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.2 Wojo</td>
            <td class="py-4">IT.057.0524.1.B060.00003</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <!-- Copy baris berikut sama seperti punyamu -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.3 Wates</td>
            <td class="py-4">IT.057.0524.1.B060.00004</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor Jembatan 6.2 Slo</td>
            <td class="py-4">IT.057.0524.1.B060.00005</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.13 KRO</td>
            <td class="py-4">IT.057.0524.1.B060.00006</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PRINTER</td>
            <td class="py-4">6/4/2024</td>
            <td class="py-4">1900038535</td>
            <td class="py-4">KOMERSIAL</td>
            <td class="py-4">IT.067.0624.1.B060.00002</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="bg-yellow-500 rounded-lg shadow mb-12 overflow-hidden">
  <div class="p-6 border-b flex items-center justify-between">
    <h3 class="text-lg font-semibold">Card Tables</h3>
    <button class="bg-gray-600 text-white px-4 py-2 rounded">LIHAT SEMUA</button>
  </div>

  <div class="p-6 bg-white">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left">
        <thead class="text-xs text-gray-500 uppercase">
          <tr>
            <th class="py-3">Jenis Perangkat</th>
            <th class="py-3">Tanggal Pengadaan</th>
            <th class="py-3">NO NPD</th>
            <th class="py-3">Unit</th>
            <th class="py-3">No Inventaris</th>
            <th class="py-3">Aksi</th>
          </tr>
        </thead>

        <tbody class="text-sm text-gray-700 divide-y">
          <!-- 5 data -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.2 Wojo</td>
            <td class="py-4">IT.057.0524.1.B060.00003</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <!-- Copy baris berikut sama seperti punyamu -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.3 Wates</td>
            <td class="py-4">IT.057.0524.1.B060.00004</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor Jembatan 6.2 Slo</td>
            <td class="py-4">IT.057.0524.1.B060.00005</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.13 KRO</td>
            <td class="py-4">IT.057.0524.1.B060.00006</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PRINTER</td>
            <td class="py-4">6/4/2024</td>
            <td class="py-4">1900038535</td>
            <td class="py-4">KOMERSIAL</td>
            <td class="py-4">IT.067.0624.1.B060.00002</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="bg-green-500 rounded-lg shadow mb-12 overflow-hidden">
  <div class="p-6 border-b flex items-center justify-between">
    <h3 class="text-lg font-semibold">Card Tables</h3>
    <button class="bg-gray-600 text-white px-4 py-2 rounded">LIHAT SEMUA</button>
  </div>

  <div class="p-6 bg-white">
    <div class="overflow-x-auto">
      <table class="min-w-full text-left">
        <thead class="text-xs text-gray-500 uppercase">
          <tr>
            <th class="py-3">Jenis Perangkat</th>
            <th class="py-3">Tanggal Pengadaan</th>
            <th class="py-3">NO NPD</th>
            <th class="py-3">Unit</th>
            <th class="py-3">No Inventaris</th>
            <th class="py-3">Aksi</th>
          </tr>
        </thead>

        <tbody class="text-sm text-gray-700 divide-y">
          <!-- 5 data -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.2 Wojo</td>
            <td class="py-4">IT.057.0524.1.B060.00003</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <!-- Copy baris berikut sama seperti punyamu -->
          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.3 Wates</td>
            <td class="py-4">IT.057.0524.1.B060.00004</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor Jembatan 6.2 Slo</td>
            <td class="py-4">IT.057.0524.1.B060.00005</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PC DEKSTOP</td>
            <td class="py-4">5/14/2024</td>
            <td class="py-4">1900026489</td>
            <td class="py-4">Resor 6.13 KRO</td>
            <td class="py-4">IT.057.0524.1.B060.00006</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

          <tr>
            <td class="py-4">PRINTER</td>
            <td class="py-4">6/4/2024</td>
            <td class="py-4">1900038535</td>
            <td class="py-4">KOMERSIAL</td>
            <td class="py-4">IT.067.0624.1.B060.00002</td>
            <td class="py-4 flex gap-2">
              <a class="px-3 py-1 bg-gray-600 text-white rounded text-xs">Edit</a>
              <a class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Detail</a>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</section>


<?= $this->endSection() ?>