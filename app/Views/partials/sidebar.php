<?php
  $uri = service('uri');
  $segment = $uri->getSegment(1) ?: 'home';
?>

<nav class="w-full bg-[var(--accent)] text-white shadow-sm">
  <div class="px-4 sm:px-6 lg:px-8">
    <!-- wrapper menu: kapsul putih transparan yang floating di tengah -->
    <div class="flex justify-center">
      <div class="mt-3 mb-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg px-4 sm:px-6">
        <ul class="flex items-center gap-4 sm:gap-8 py-2 text-sm font-medium">

          <!-- DASHBOARD -->
          <li>
            <a href="<?= base_url('home') ?>"
               class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
               <?= $segment === 'home'
                    ? 'bg-white text-[var(--accent)] shadow'
                    : 'text-white hover:bg-white/20' ?>">
              <span class="text-lg">📊</span>
              <span>Dashboard</span>
            </a>
          </li>

          <!-- INVENTARIS -->
          <li>
            <a href <?= "=\"" . base_url('inventory') . "\""; ?>
               class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
               <?= $segment === 'inventory'
                    ? 'bg-white text-[var(--accent)] shadow'
                    : 'text-white hover:bg-white/20' ?>">
              <span class="text-lg">📦</span>
              <span>Inventaris</span>
            </a>
          </li>

          <!-- LAPORAN -->
          <li>
            <a href="<?= base_url('reports') ?>"
               class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
               <?= $segment === 'reports'
                    ? 'bg-white text-[var(--accent)] shadow'
                    : 'text-white hover:bg-white/20' ?>">
              <span class="text-lg">🧾</span>
              <span>Laporan</span>
            </a>
          </li>

          <!-- PENGATURAN -->
          <li>
            <a href="<?= base_url('settings') ?>"
               class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
               <?= $segment === 'settings'
                    ? 'bg-white text-[var(--accent)] shadow'
                    : 'text-white hover:bg-white/20' ?>">
              <span class="text-lg">⚙️</span>
              <span>Pengaturan</span>
            </a>
          </li>

        </ul>
      </div>
    </div>
  </div>
</nav>
