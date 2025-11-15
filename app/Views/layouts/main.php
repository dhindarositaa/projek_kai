<!doctype html>
<html lang="id">
<head>
  <?= $this->include('partials/head') ?>
</head>
<body class="antialiased text-gray-700 bg-slate-100 min-h-screen">

  <!-- Sidebar -->
  <?= $this->include('partials/sidebar') ?>

  <!-- Main area -->
  <div class="flex-1 min-h-screen md:pl-64">
    <!-- Topbar -->
    <?= $this->include('partials/topbar') ?>

    <!-- Sticky stats (optional) -->
    <?php if (isset($show_stats) && $show_stats): ?>
      <?= $this->include('partials/stats') ?>
      <div class="h-28 md:h-20"></div>
    <?php endif ?>

    <!-- Page content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('partials/footer') ?>
  </div>

  <!-- Scripts -->
  <?= $this->renderSection('scripts') ?>
</body>
</html>
