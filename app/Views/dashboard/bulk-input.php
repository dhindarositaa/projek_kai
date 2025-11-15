<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-xl w-full mx-auto py-6">
  <label id="dropzoneLabel" for="fileInput" class="sr-only">Unggah file</label>

  <div id="dropzone"
       class="relative bg-white rounded-lg p-4 md:p-5 border border-gray-200 shadow-sm"
       aria-labelledby="dropzoneLabel">

    <!-- Simple dashed area (non-absolute, compact) -->
    <div id="dropzoneInner" class="rounded-md border-2 border-dashed border-blue-200 p-4 flex items-center gap-4 cursor-pointer">
      <!-- Icon -->
      <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center">
        <svg viewBox="0 0 24 24" fill="none" class="w-8 h-8" aria-hidden="true">
          <path d="M16 16V12" stroke="#1e88e5" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 12V4" stroke="#1e88e5" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M8 16V12" stroke="#1e88e5" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 20C8.686 20 6 17.314 6 14C6 11.794 7.018 9.829 8.7 8.6C9.28 6.86 10.98 5.6 13 5.6C15.76 5.6 18 7.84 18 10.6C20.21 10.6 22 12.39 22 14.6C22 17.3137 19.3137 20 16 20H12Z"
                stroke="#1e88e5" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round" fill="#1e90ff12"/>
        </svg>
      </div>

      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-slate-800">Drop file di sini</p>
        <p class="text-xs text-slate-500">atau klik <button id="browseBtnCompact" class="text-xs font-medium text-blue-600 underline">Browse</button></p>
      </div>

      <div class="text-xs text-slate-500">
        <span id="fileNameCompact" class="block max-w-[10rem] truncate"></span>
      </div>
    </div>

    <!-- preview area (compact) -->
    <div id="previewCompact" class="mt-3"></div>

    <input id="fileInput" type="file" class="hidden" accept="image/*,application/pdf,video/*" />
    <p id="errorCompact" class="mt-2 text-xs text-red-600 hidden" role="alert"></p>
  </div>
</div>

<style>
  /* compact dragover visual */
  #dropzoneInner.dragover { box-shadow: 0 6px 20px rgba(30,144,255,0.08); transform: translateY(-1px); }
  .thumb-compact { width:48px; height:48px; border-radius:.5rem; object-fit:cover; }
</style>

<script>
(function () {
  const dropzone = document.getElementById('dropzoneInner');
  const fileInput = document.getElementById('fileInput');
  const fileNameEl = document.getElementById('fileNameCompact');
  const preview = document.getElementById('previewCompact');
  const err = document.getElementById('errorCompact');
  const browseBtn = document.getElementById('browseBtnCompact');
  const MAX_BYTES = 10 * 1024 * 1024;

  function humanFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes)/Math.log(k));
    return (bytes/Math.pow(k,i)).toFixed(2) + ' ' + sizes[i];
  }

  function clear() {
    preview.innerHTML = '';
    fileNameEl.textContent = '';
    err.classList.add('hidden');
  }

  function showError(msg) {
    err.textContent = msg;
    err.classList.remove('hidden');
  }

  function renderFile(file) {
    clear();
    fileNameEl.textContent = file.name;

    const wrap = document.createElement('div');
    wrap.className = 'flex items-center gap-3';

    if (file.type.startsWith('image/')) {
      const img = document.createElement('img');
      img.className = 'thumb-compact';
      img.alt = file.name;
      img.src = URL.createObjectURL(file);
      img.onload = () => URL.revokeObjectURL(img.src);
      wrap.appendChild(img);
    } else {
      const icon = document.createElement('div');
      icon.className = 'w-12 h-12 rounded-md flex items-center justify-center bg-gray-50 border';
      icon.innerHTML = '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#475569" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="#475569" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      wrap.appendChild(icon);
    }

    const meta = document.createElement('div');
    meta.className = 'text-xs';
    meta.innerHTML = '<div class="font-medium text-slate-800 truncate">'+file.name+'</div><div class="text-slate-500">'+humanFileSize(file.size)+'</div>';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ml-auto text-xs text-red-600 px-2 py-1';
    btn.textContent = 'Hapus';
    btn.addEventListener('click', () => { fileInput.value=''; clear(); });

    const container = document.createElement('div');
    container.className = 'flex items-center gap-3';
    container.appendChild(wrap);
    container.appendChild(meta);
    container.appendChild(btn);

    preview.appendChild(container);
  }

  function handleFiles(files) {
    const file = files[0];
    if (!file) return;
    if (file.size > MAX_BYTES) {
      showError('Maks 10 MB');
      fileInput.value = '';
      return;
    }
    renderFile(file);
  }

  // drag events
  ['dragenter','dragover'].forEach(e => {
    dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.add('dragover'); });
  });
  ['dragleave','drop','dragend'].forEach(e => {
    dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.remove('dragover'); });
  });

  dropzone.addEventListener('drop', ev => {
    const dt = ev.dataTransfer;
    if (!dt) return;
    if (dt.files && dt.files.length) handleFiles(dt.files);
  });

  fileInput.addEventListener('change', () => {
    err.classList.add('hidden');
    if (fileInput.files && fileInput.files.length) handleFiles(fileInput.files);
    else clear();
  });

  browseBtn.addEventListener('click', () => fileInput.click());
  browseBtn.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); } });

  // click on whole area to open filepicker
  dropzone.addEventListener('click', (e) => {
    if (e.target.tagName.toLowerCase() === 'button') return;
    fileInput.click();
  });

  clear();
})();
</script>

<?= $this->endSection() ?>
