<header class="bg-accent sticky top-0 z-30" style="background:var(--accent);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center gap-3">
        <button id="openSidebarBtn" class="md:hidden p-2 rounded bg-white/10 text-white" aria-label="Buka menu">☰</button>
        <h1 class="text-white text-lg font-semibold"><?= esc($page_title ?? 'Dashboard') ?></h1>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block">
          <label for="search" class="sr-only">Cari</label>
          <input id="search" type="search" placeholder="Search..." class="rounded px-3 py-2 text-gray-700 w-56" />
        </div>

        <div class="flex items-center gap-3">
          <button class="hidden sm:inline-flex items-center gap-2 bg-white/20 text-white px-3 py-1 rounded">🔔</button>
          <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
            <img src="https://i.pravatar.cc/80" alt="Avatar pengguna" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
