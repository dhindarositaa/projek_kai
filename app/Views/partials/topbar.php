<?php
  $uri     = service('uri');
  $segment = $uri->getSegment(1) ?: 'home';
?>

<header class="bg-accent sticky top-0 z-30" style="background:var(--accent);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- SATU BARIS: TITLE (KIRI) + NAV (TENGAH) + SEARCH & AVATAR (KANAN) -->
    <div class="flex items-center h-20 gap-6">

      <!-- KIRI: TITLE -->
      <div class="flex items-center gap-3">
        <h1 class="text-white text-lg font-semibold">
          <?= esc($page_title ?? 'Dashboard') ?>
        </h1>
      </div>

      <!-- TENGAH: MENU NAV (dari sidebar) -->
      <div class="flex-1 flex justify-center">
        <nav class="text-white">
          <div class="rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg px-4 sm:px-6">
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

              <!-- DETAIL DATA -->
              <li>
                <a href="<?= base_url('assets') ?>"
                   class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
                   <?= $segment === 'assets'
                        ? 'bg-white text-[var(--accent)] shadow'
                        : 'text-white hover:bg-white/20' ?>">
                  <span class="text-lg">📦</span>
                  <span>Detail Data</span>
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
        </nav>
      </div>

      <!-- KANAN: SEARCH + AVATAR -->
      <div class="flex items-center gap-4">
        <!-- AVATAR -->
        <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
          <img src="https://i.pravatar.cc/80" alt="Avatar pengguna" class="w-full h-full object-cover" />
        </div>
      </div>

    </div>
  </div>
</header>
