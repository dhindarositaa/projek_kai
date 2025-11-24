<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="space-y-4">
  <!-- KURANG DARI 1 TAHUN (MERAH) -->
  <div class="rounded-[1rem] bg-red-100 p-5 shadow mx-10">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-red-900">Kurang dari 1 Tahun</h3>
      <button class="text-xs px-3 py-1 rounded-full bg-red-200 hover:bg-red-300 text-red-800">
        SEE ALL
      </button>
    </div>

    <!-- isi data putih di dalam kotak merah -->
    <div class="bg-white rounded-xl divide-y">
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PRINTER</span>
        <span class="text-gray-600">6/4/2024</span>
      </div>
    </div>
  </div>

  <!-- KURANG DARI 2 TAHUN (KUNING) -->
  <div class="rounded-[1rem] bg-yellow-100 p-5 shadow mx-10">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-yellow-900">Kurang dari 2 Tahun</h3>
      <button class="text-xs px-3 py-1 rounded-full bg-yellow-200 hover:bg-yellow-300 text-yellow-900">
        SEE ALL
      </button>
    </div>

    <div class="bg-white rounded-xl divide-y">
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PRINTER</span>
        <span class="text-gray-600">6/4/2024</span>
      </div>
    </div>
  </div>

  <!-- LEBIH DARI 3 TAHUN (HIJAU) -->
  <div class="rounded-[1rem] bg-green-100 p-5 shadow mx-10">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-green-900">Lebih dari 3 Tahun</h3>
      <button class="text-xs px-3 py-1 rounded-full bg-green-200 hover:bg-green-300 text-green-900">
        SEE ALL
      </button>
    </div>

    <div class="bg-white rounded-xl divide-y">
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PC DEKSTOP</span>
        <span class="text-gray-600">5/14/2024</span>
      </div>
      <div class="px-4 py-3 flex items-center justify-between text-sm">
        <span class="font-medium text-gray-800">PRINTER</span>
        <span class="text-gray-600">6/4/2024</span>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
