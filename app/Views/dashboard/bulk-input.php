<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-xl w-full mx-auto py-6">
  <form id="uploadForm" method="post" enctype="multipart/form-data" action="<?= site_url('import/process') ?>">
    <?= csrf_field() ?>

    <label id="dropzoneLabel" for="fileInput" class="sr-only">Unggah file</label>

    <div id="dropzone"
         class="relative bg-white rounded-lg p-4 md:p-5 border border-gray-200 shadow-sm"
         aria-labelledby="dropzoneLabel">

      <!-- Simple dashed area (non-absolute, compact) -->
      <div id="dropzoneInner" class="rounded-md border-2 border-dashed border-blue-200 p-4 flex items-center gap-4 cursor-pointer" role="button" tabindex="0">
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
          <p class="text-sm font-medium text-slate-800">Drop file Excel di sini</p>
          <p class="text-xs text-slate-500">atau klik <button id="browseBtnCompact" type="button" class="text-xs font-medium text-blue-600 underline">Browse</button></p>
        </div>

        <div class="text-xs text-slate-500">
          <span id="fileNameCompact" class="block max-w-[10rem] truncate"></span>
        </div>
      </div>

      <!-- preview area (compact) -->
      <div id="previewCompact" class="mt-3"></div>

      <input id="fileInput" name="file" type="file" class="hidden" accept=".xlsx,.xls" />
      <p id="errorCompact" class="mt-2 text-xs text-red-600 hidden" role="alert"></p>

      <!-- actions -->
      <div class="mt-4 flex items-center gap-3">
        <button id="uploadBtn" type="button" class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-60" disabled>Upload & Import</button>
        <button id="clearBtn" type="button" class="text-sm text-gray-600">Batal</button>
        <div id="progressWrap" class="flex-1 hidden">
          <div class="w-full bg-gray-100 rounded h-2 overflow-hidden">
            <div id="progressBar" class="h-2 bg-blue-500" style="width:0%"></div>
          </div>
          <div id="progressText" class="text-xs text-slate-500 mt-1">0%</div>
        </div>
      </div>

    </div>
  </form>

  <!-- hasil -->
  <div id="result" class="mt-4 hidden">
    <div id="summaryBox" class="bg-white border p-3 rounded shadow-sm">
      <h3 class="font-medium text-sm">Hasil Import</h3>
      <div id="summaryContent" class="text-sm text-slate-700 mt-2"></div>
      <div class="mt-2">
        <button id="downloadLogBtn" type="button" class="text-xs text-blue-600 underline hidden">Download log import</button>
      </div>
    </div>
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
  const uploadBtn = document.getElementById('uploadBtn');
  const clearBtn = document.getElementById('clearBtn');
  const progressWrap = document.getElementById('progressWrap');
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const result = document.getElementById('result');
  const summaryContent = document.getElementById('summaryContent');
  const downloadLogBtn = document.getElementById('downloadLogBtn');
  const MAX_BYTES = 25 * 1024 * 1024; // safer: 25MB limit

  function humanFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes)/Math.log(k));
    return (bytes/Math.pow(k,i)).toFixed(2) + ' ' + sizes[i];
  }

  function clearPreview() {
    preview.innerHTML = '';
    fileNameEl.textContent = '';
    err.classList.add('hidden');
    uploadBtn.disabled = true;
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    progressWrap.classList.add('hidden');
    result.classList.add('hidden');
  }

  function showError(msg) {
    err.textContent = msg;
    err.classList.remove('hidden');
  }

  function renderFile(file) {
    preview.innerHTML = '';
    fileNameEl.textContent = file.name;

    const wrap = document.createElement('div');
    wrap.className = 'flex items-center gap-3';

    const icon = document.createElement('div');
    icon.className = 'w-12 h-12 rounded-md flex items-center justify-center bg-gray-50 border';
    icon.innerHTML = '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#475569" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="#475569" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    wrap.appendChild(icon);

    const meta = document.createElement('div');
    meta.className = 'text-xs';
    meta.innerHTML = '<div class="font-medium text-slate-800 truncate">'+file.name+'</div><div class="text-slate-500">'+humanFileSize(file.size)+'</div>';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ml-auto text-xs text-red-600 px-2 py-1';
    btn.textContent = 'Hapus';
    btn.addEventListener('click', () => { fileInput.value=''; clearPreview(); });

    const container = document.createElement('div');
    container.className = 'flex items-center gap-3';
    container.appendChild(wrap);
    container.appendChild(meta);
    container.appendChild(btn);

    preview.appendChild(container);
    uploadBtn.disabled = false;
  }

  function handleFiles(files) {
    const file = files[0];
    if (!file) return;
    // type check: accept xlsx/xls by extension
    if (!/\.(xlsx|xls)$/i.test(file.name)) {
      showError('Format tidak valid — gunakan file .xlsx atau .xls');
      fileInput.value = '';
      return;
    }
    if (file.size > MAX_BYTES) {
      showError('Maks 25 MB per file');
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
    else clearPreview();
  });

  browseBtn.addEventListener('click', () => fileInput.click());
  browseBtn.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); } });

  // click on whole area to open filepicker
  dropzone.addEventListener('click', (e) => {
    if (e.target.tagName.toLowerCase() === 'button') return;
    fileInput.click();
  });

  clearBtn.addEventListener('click', () => {
    fileInput.value = '';
    clearPreview();
  });

  // upload logic with progress
  uploadBtn.addEventListener('click', () => {
    if (!fileInput.files || !fileInput.files.length) return;
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);

    // show progress UI
    progressWrap.classList.remove('hidden');
    uploadBtn.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const percent = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = percent + '%';
      }
    });

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        uploadBtn.disabled = false;
        progressWrap.classList.add('hidden');
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const res = JSON.parse(xhr.responseText);
            if (res.status === 'success' && res.summary) {
              result.classList.remove('hidden');
              summaryContent.innerHTML = `
                <div>Inserted: <strong>${res.summary.inserted}</strong></div>
                <div>Updated: <strong>${res.summary.updated}</strong></div>
                <div>Failed: <strong>${res.summary.failed}</strong></div>
                <div>Conflicts: <strong>${res.summary.conflicts}</strong></div>
              `;
              // show download log button if you implement an endpoint to fetch logs
              downloadLogBtn.classList.remove('hidden');
              downloadLogBtn.onclick = () => {
                // implement route /import/logs or similar to download logs (not implemented here)
                window.location.href = '<?= site_url('import/logs') ?>';
              };
            } else {
              showError(res.message || 'Import gagal. Cek server log.');
            }
          } catch (errjson) {
            showError('Response tidak valid dari server');
          }
        } else {
          showError('Upload gagal. Status: ' + xhr.status);
        }
      }
    };

    xhr.onerror = function () {
      uploadBtn.disabled = false;
      progressWrap.classList.add('hidden');
      showError('Terjadi kesalahan jaringan saat upload');
    };

    xhr.send(formData);
  });

  // init
  clearPreview();
})();
</script>

<?= $this->endSection() ?>
