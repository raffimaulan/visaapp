/* ================================================================
   Documents Upload — Multi-file handler
   ================================================================ */

(function () {
  var dropzone    = document.getElementById('dropzone');
  var fileInput   = document.getElementById('fileInput');
  var browseBtn   = document.getElementById('browseBtn');
  var previewList = document.getElementById('previewList');
  var submitBtn   = document.getElementById('submitBtn');
  var form        = fileInput ? fileInput.closest('form') : null;

  var files = [];

  // ----------------------------------------------------------------
  // Buka file picker
  // ----------------------------------------------------------------
  browseBtn.addEventListener('click', function () { fileInput.click(); });

  dropzone.addEventListener('click', function (e) {
    if (e.target === browseBtn || browseBtn.contains(e.target)) return;
    fileInput.click();
  });

  // ----------------------------------------------------------------
  // File input change
  // ----------------------------------------------------------------
  fileInput.addEventListener('change', function () {
    addFiles(Array.from(fileInput.files));
    fileInput.value = '';
  });

  // ----------------------------------------------------------------
  // Drag & Drop
  // ----------------------------------------------------------------
  dropzone.addEventListener('dragover', function (e) {
    e.preventDefault();
    dropzone.classList.add('drag-over');
  });

  dropzone.addEventListener('dragleave', function () {
    dropzone.classList.remove('drag-over');
  });

  dropzone.addEventListener('drop', function (e) {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    addFiles(Array.from(e.dataTransfer.files));
  });

  // ----------------------------------------------------------------
  // Tambah file ke list (cegah duplikat)
  // ----------------------------------------------------------------
  function addFiles(newFiles) {
    newFiles.forEach(function (f) {
      if (files.find(function (x) { return x.name === f.name && x.size === f.size; })) return;
      files.push(f);
    });
    renderPreviews();
  }

  // ----------------------------------------------------------------
  // Hapus file dari list
  // ----------------------------------------------------------------
  function removeFile(idx) {
    files.splice(idx, 1);
    renderPreviews();
  }

  // ----------------------------------------------------------------
  // Render preview list
  // ----------------------------------------------------------------
  function renderPreviews() {
    previewList.innerHTML = '';

    files.forEach(function (f, i) {
      var isPdf     = f.type === 'application/pdf';
      var isImg     = f.type.startsWith('image/');
      var tooBig    = f.size > 2 * 1024 * 1024;
      var wrongType = !isPdf && !isImg;

      var item = document.createElement('div');
      item.className = 'upload-preview-item';
      item.innerHTML =
        '<div class="preview-icon ' + (isPdf ? 'pdf' : 'img') + '">' +
          '<i class="fas fa-' + (isPdf ? 'file-pdf' : 'file-image') + '"></i>' +
        '</div>' +
        '<span class="preview-name" title="' + escHtml(f.name) + '">' + escHtml(f.name) + '</span>' +
        (tooBig    ? '<span class="preview-error">File terlalu besar (maks 2MB)</span>' : '') +
        (wrongType ? '<span class="preview-error">Format tidak didukung</span>' : '') +
        '<span class="preview-size">' + formatSize(f.size) + '</span>' +
        '<button type="button" class="preview-remove" data-idx="' + i + '" title="Hapus">' +
          '<i class="fas fa-times"></i>' +
        '</button>';

      previewList.appendChild(item);
    });

    previewList.querySelectorAll('.preview-remove').forEach(function (btn) {
      btn.addEventListener('click', function () {
        removeFile(parseInt(btn.dataset.idx));
      });
    });

    var hasValid = files.some(function (f) {
      return f.size <= 2 * 1024 * 1024 &&
             (f.type === 'application/pdf' || f.type.startsWith('image/'));
    });
    submitBtn.disabled = !hasValid;
  }

  // ----------------------------------------------------------------
  // Submit handler
  // ----------------------------------------------------------------
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var validFiles = files.filter(function (f) {
        return f.size <= 2 * 1024 * 1024 &&
               (f.type === 'application/pdf' || f.type.startsWith('image/'));
      });

      if (validFiles.length === 0) {
        alert('Tidak ada file valid untuk diupload. Pastikan format JPG/PNG/PDF dan ukuran maksimal 2MB.');
        return;
      }

      // Coba pasang file ke fileInput via DataTransfer
      var dtOk = false;
      if (typeof DataTransfer !== 'undefined') {
        try {
          var dt = new DataTransfer();
          validFiles.forEach(function (f) { dt.items.add(f); });
          fileInput.files = dt.files;
          dtOk = (fileInput.files.length === validFiles.length);
        } catch (err) {
          dtOk = false;
        }
      }

      if (dtOk) {
        // DataTransfer berhasil — submit form biasa
        form.submit();
      } else {
        // Fallback: kirim via fetch dengan FormData manual
        submitViaFetch(validFiles);
      }
    });
  }

  // ----------------------------------------------------------------
  // Fallback: upload via fetch (browser tanpa DataTransfer support)
  // ----------------------------------------------------------------
  function submitViaFetch(validFiles) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengupload...';

    var fd = new FormData();

    // Salin semua field form kecuali file input
    var inputs = form.querySelectorAll('input:not([type=file]), select, textarea');
    inputs.forEach(function (el) {
      if (el.name) fd.append(el.name, el.value);
    });

    // Tambahkan file valid
    validFiles.forEach(function (f) {
      fd.append('documents[]', f, f.name);
    });

    fetch(form.action, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (res) {
      return res.json();
    })
    .then(function (data) {
      window.location.href = data.redirect || 'index.php';
    })
    .catch(function () {
      // Jika server tidak return JSON (misal masih versi lama), redirect manual
      window.location.href = 'index.php';
    });
  }

  // ----------------------------------------------------------------
  // Helpers
  // ----------------------------------------------------------------
  function formatSize(bytes) {
    if (bytes < 1024)        return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

})();