<!doctype html>
<html lang="id">
<head>
  <?= $this->include('partials/head') ?>
</head>
<body class="antialiased text-gray-700 bg-slate-100 min-h-screen">

  <!-- Main area -->
  <div class="min-h-screen">
    <!-- Topbar -->
    <?= $this->include('partials/topbar') ?>

    <!-- Sticky stats (optional) -->
    <?php if (isset($show_stats) && $show_stats): ?>
      <?= $this->include('partials/stats') ?>
      <!-- spacer kecil supaya konten tidak ketimpa sticky, tapi tetap dekat -->
      <div class="h-8 md:h-6"></div>
    <?php endif ?>

    <!-- Page content -->
    <main class="w-full px-0 pt-2 pb-8">
      <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('partials/footer') ?>
  </div>

  <!-- Scripts -->
  <?= $this->renderSection('scripts') ?>
</body>
</html>
