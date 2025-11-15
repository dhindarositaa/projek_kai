<!-- minimal sidebar; fixed on desktop -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 transform -translate-x-full md:translate-x-0 transition-transform bg-white border-r border-gray-200 shadow-sm" aria-label="Sidebar">
  <div class="px-6 py-6 h-full flex flex-col">
    <div class="flex items-center justify-between mb-6">
      <div class="text-lg font-bold text-gray-800">PLAINHABIT</div>
      <button id="closeSidebarBtn" class="md:hidden p-1 rounded hover:bg-gray-100" aria-label="Tutup sidebar">✕</button>
    </div>

    <nav class="flex-1 overflow-y-auto">
      <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Menu</p>
      <ul class="space-y-1 text-sm">
        <li><a href="<?= base_url() ?>" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">📊 Dashboard</a></li>
        <li><a href="<?= base_url('inventory') ?>" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">📦 Inventaris</a></li>
        <li><a href="<?= base_url('reports') ?>" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">📋 Laporan</a></li>
        <li><a href="<?= base_url('settings') ?>" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-50">⚙️ Pengaturan</a></li>
      </ul>
    </nav>
  </div>
</aside>

<!-- overlay (mobile) -->
<div id="overlay" class="fixed inset-0 bg-black/30 z-20 hidden md:hidden"></div>

<!-- small script for toggling sidebar (keempatan utk include di assets) -->
<script>
  (function(){
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openBtn = document.getElementById('openSidebarBtn');
    const closeBtn = document.getElementById('closeSidebarBtn');

    function openSidebar(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); document.body.style.overflow='hidden'; }
    function closeSidebar(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); document.body.style.overflow=''; }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
  })();
</script>
