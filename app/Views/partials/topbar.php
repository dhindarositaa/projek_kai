<?php
  $uri     = service('uri');
  $segment = $uri->getSegment(1) ?: 'home';

  $session  = session();
  $userName = $session->get('name') ?? 'Guest';
  $userEmail = $session->get('email') ?? '';
?>


<header class="bg-accent sticky top-0 z-30" style="background:var(--accent);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex items-center h-16 sm:h-20 gap-4">

      <!-- KIRI: TITLE -->
      <div class="flex items-center gap-3 flex-shrink-0">
        <h1 class="text-white text-base sm:text-lg font-semibold truncate">
          <?= esc($page_title ?? 'Dashboard') ?>
        </h1>
      </div>

      <!-- TENGAH: NAV DESKTOP -->
      <div class="flex-1 justify-center hidden md:flex">
        <nav class="text-white">
          <div class="rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg
            px-6 sm:px-10 py-2 sm:py-1">
            <ul class="flex items-center gap-5 sm:gap-8 py-2 text-sm font-medium">

              <?php
                $menus = [
                  ['home','Dashboard','📊'],
                  ['assets','Detail Data','📦'],
                  ['bulk-input','Input Bulk','🧾'],
                  ['input','Input Manual','⚙️'],
                ];
              ?>

              <?php foreach ($menus as [$key,$label,$icon]): ?>
                <li>
                  <a href="<?= base_url($key) ?>"
                     class="flex items-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-full transition
                     <?= $segment === $key
                          ? 'bg-white text-[var(--accent)] shadow'
                          : 'text-white hover:bg-white/20' ?>">
                    <span><?= $icon ?></span>
                    <span><?= $label ?></span>
                  </a>
                </li>
              <?php endforeach ?>
            </ul>
          </div>
        </nav>
      </div>

      <!-- KANAN -->
      <div class="flex items-center gap-3 ml-auto md:ml-0">

        <!-- HAMBURGER (HP ONLY) -->
        <button id="mobileMenuBtn"
                class="md:hidden text-white text-2xl focus:outline-none">
          ☰
        </button>

        <!-- AVATAR -->
        <div class="relative">
          <button id="avatarMenuButton"
                  class="h-9 sm:h-10 px-3 sm:px-4 
                        flex items-center justify-center">
            <img src="<?= base_url('asset/images/logokai.png') ?>"
                alt="Logo"
                class="h-full object-contain">
          </button>


          <!-- DROPDOWN AVATAR -->
          <div id="avatarDropdown"
               class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg py-2 text-sm z-40">
            <div class="px-4 py-2 border-b">
              <p class="font-semibold"><?= esc($userName) ?></p>
              <?php if ($userEmail): ?>
                <p class="text-xs text-gray-500"><?= esc($userEmail) ?></p>
              <?php endif ?>
            </div>
            <form action="<?= base_url('logout') ?>" method="post">
              <?= csrf_field() ?>
              <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                Logout
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu"
         class="md:hidden hidden pb-4">
      <div class="mt-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20">
        <?php foreach ($menus as [$key,$label,$icon]): ?>
          <a href="<?= base_url($key) ?>"
             class="flex items-center gap-3 px-4 py-3 text-white text-sm
             <?= $segment === $key ? 'bg-white/20' : 'hover:bg-white/10' ?>">
            <span><?= $icon ?></span>
            <span><?= $label ?></span>
          </a>
        <?php endforeach ?>
      </div>
    </div>

  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const avatarBtn = document.getElementById('avatarMenuButton');
  const avatarMenu = document.getElementById('avatarDropdown');
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  avatarBtn?.addEventListener('click', e => {
    e.stopPropagation();
    avatarMenu.classList.toggle('hidden');
  });

  mobileBtn?.addEventListener('click', e => {
    e.stopPropagation();
    mobileMenu.classList.toggle('hidden');
  });

  document.addEventListener('click', () => {
    avatarMenu?.classList.add('hidden');
    mobileMenu?.classList.add('hidden');
  });
});
</script>

