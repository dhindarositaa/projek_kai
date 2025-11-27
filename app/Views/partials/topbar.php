<?php
  $uri     = service('uri');
  $segment = $uri->getSegment(1) ?: 'home';

  $session  = session();
  $userName = $session->get('name') ?? 'Guest';
  $userEmail = $session->get('email') ?? '';
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
                <a href="<?= base_url('bulk-input') ?>"
                   class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
                   <?= $segment === 'bulk-input'
                        ? 'bg-white text-[var(--accent)] shadow'
                        : 'text-white hover:bg-white/20' ?>">
                  <span class="text-lg">🧾</span>
                  <span>Input Bulk</span>
                </a>
              </li>

              <!-- PENGATURAN -->
              <li>
                <a href="<?= base_url('input') ?>"
                   class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full transition
                   <?= $segment === 'input'
                        ? 'bg-white text-[var(--accent)] shadow'
                        : 'text-white hover:bg-white/20' ?>">
                  <span class="text-lg">⚙️</span>
                  <span>Input Manual</span>
                </a>
              </li>

            </ul>
          </div>
        </nav>
      </div>
<!-- KANAN: AVATAR + DROPDOWN -->
<div class="flex items-center gap-4">
  <div class="relative">
    <!-- BUTTON AVATAR -->
    <button id="avatarMenuButton" type="button"
            class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden ring-2 ring-white/40">
      <img src="https://i.pravatar.cc/80" alt="Avatar pengguna"
           class="w-full h-full object-cover" />
    </button>

      <!-- DROPDOWN -->
      <div id="avatarDropdown"
          class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg py-2 text-sm z-40">
        <!-- NAMA USER -->
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="font-semibold text-gray-800"><?= esc($userName) ?></p>
          <?php if ($userEmail) : ?>
            <p class="text-xs text-gray-500"><?= esc($userEmail) ?></p>
          <?php endif; ?>
        </div>

        <!-- TOMBOL LOGOUT -->
        <form action="<?= base_url('logout') ?>" method="post" class="mt-1">
          <?= csrf_field() ?>
          <button type="submit"
                  class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>



    </div>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btn  = document.getElementById('avatarMenuButton');
    const menu = document.getElementById('avatarDropdown');

    if (!btn || !menu) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    // klik di luar dropdown -> tutup
    document.addEventListener('click', function () {
      if (!menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
      }
    });
  });
</script>
