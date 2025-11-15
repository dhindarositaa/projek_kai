<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?= esc($title ?? 'Dashboard — PlainHabit') ?></title>

<!-- Tailwind CDN (development). Untuk produksi compile Tailwind dan panggil CSS build -->
<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="<?= base_url('css/app.css') ?>" />

<!-- small inline CSS variables -->
<style>
  :root{ --accent:#0ea5c9; --navy:#0b314f; }
  .card-bg{ background:#fff; }
</style>
