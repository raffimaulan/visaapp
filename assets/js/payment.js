/* ================================================================
   Payments — Format hint Rupiah + auto-fill + status logic
   ================================================================ */
(function () {

  /* ---- Helper: angka → "Rp 1.500.000" ---- */
  function toRp(val) {
    var n = parseInt(val) || 0;
    if (!n) return '';
    return 'Rp\u00a0' + n.toLocaleString('id-ID');
  }

  /* ---- Pasang hint Rupiah di bawah input number ---- */
  function attachHint(inputEl, hintEl) {
    if (!inputEl || !hintEl) return;
    function refresh() {
      hintEl.textContent = toRp(inputEl.value);
    }
    inputEl.addEventListener('input', refresh);
    refresh(); // tampilkan nilai awal
  }

  /* ---- CREATE PAGE ---- */
  var fTotal = document.getElementById('fTotal');
  var fDp    = document.getElementById('fDp');
  var fPaid  = document.getElementById('fPaid');

  attachHint(fTotal, document.getElementById('hintTotal'));
  attachHint(fDp,    document.getElementById('hintDp'));
  attachHint(fPaid,  document.getElementById('hintPaid'));

  /* ---- EDIT PAGE ---- */
  attachHint(document.getElementById('eTotal'), document.getElementById('hintTotal'));
  attachHint(document.getElementById('eDp'),    document.getElementById('hintDp'));
  attachHint(document.getElementById('ePaid'),  document.getElementById('hintPaid'));

  /* ---- Status badge (create) ---- */
  var fStatus       = document.getElementById('fStatus');
  var statusPreview = document.getElementById('statusPreview');
  var existingPaid  = 0;

  function updateStatusPreview() {
    if (!statusPreview || !fStatus) return;
    var total     = parseInt(fTotal ? fTotal.value : 0) || 0;
    var newPaid   = parseInt(fPaid  ? fPaid.value  : 0) || 0;
    var totalPaid = existingPaid + newPaid;

    var status, label, cls;
    if (total > 0 && totalPaid >= total) {
      status = 'paid';    label = 'Lunas';       cls = 'badge-success';
    } else if (totalPaid > 0) {
      status = 'partial'; label = 'Baru DP';     cls = 'badge-warning';
    } else {
      status = 'unpaid';  label = 'Belum Bayar'; cls = 'badge-danger';
    }
    fStatus.value = status;
    statusPreview.innerHTML =
      '<span class="badge ' + cls + '" style="font-size:0.9rem;padding:6px 12px;">' + label + '</span>';
  }

  if (fTotal) fTotal.addEventListener('input', updateStatusPreview);
  if (fPaid)  fPaid.addEventListener('input',  updateStatusPreview);

  /* ---- Dropdown pengajuan (create) ---- */
  var sel      = document.getElementById('applicationSelect');
  var infoBox  = document.getElementById('infoBox');
  var pidInput = document.getElementById('paymentId');

  if (sel) {
    sel.addEventListener('change', function () {
      var opt     = sel.options[sel.selectedIndex];
      var pid     = parseInt(opt.getAttribute('data-pid')    || '0');
      var pstatus = opt.getAttribute('data-pstatus')         || '';
      var total   = parseFloat(opt.getAttribute('data-total')|| '0');
      var paid    = parseFloat(opt.getAttribute('data-paid') || '0');
      var dp      = parseFloat(opt.getAttribute('data-dp')   || '0');

      if (pid > 0) {
        pidInput.value = pid;
        existingPaid   = paid;

        var label = pstatus === 'partial' ? 'Baru DP' : 'Belum Bayar';
        document.getElementById('infoStatus').textContent = label;
        document.getElementById('infoTotal').textContent  = 'Rp\u00a0' + total.toLocaleString('id-ID');
        document.getElementById('infoPaid').textContent   = 'Rp\u00a0' + paid.toLocaleString('id-ID');
        document.getElementById('infoSisa').textContent   = 'Rp\u00a0' + (total - paid).toLocaleString('id-ID');
        infoBox.style.display = 'block';

        fTotal.value = total;
        fDp.value    = dp;
        fPaid.value  = '';

        // Update hint setelah isi nilai
        document.getElementById('hintTotal').textContent = toRp(total);
        document.getElementById('hintDp').textContent    = toRp(dp);
        document.getElementById('hintPaid').textContent  = '';

        fPaid.focus();
      } else {
        pidInput.value = '0';
        existingPaid   = 0;
        infoBox.style.display = 'none';
        fTotal.value = ''; fDp.value = '0'; fPaid.value = '0';
        document.getElementById('hintTotal').textContent = '';
        document.getElementById('hintDp').textContent    = toRp(0);
        document.getElementById('hintPaid').textContent  = toRp(0);
      }
      updateStatusPreview();
    });
  }

  /* ---- Status badge (edit) ---- */
  var eTotal   = document.getElementById('eTotal');
  var ePaid    = document.getElementById('ePaid');
  var fStatusE = document.getElementById('fStatusEdit');
  var previewE = document.getElementById('statusPreviewEdit');

  function updateStatusEdit() {
    if (!previewE || !fStatusE) return;
    var total = parseInt(eTotal ? eTotal.value : 0) || 0;
    var paid  = parseInt(ePaid  ? ePaid.value  : 0) || 0;
    var status, label, cls;
    if (total > 0 && paid >= total) {
      status = 'paid'; label = 'Lunas'; cls = 'badge-success';
    } else if (paid > 0) {
      status = 'partial'; label = 'Baru DP'; cls = 'badge-warning';
    } else {
      status = 'unpaid'; label = 'Belum Bayar'; cls = 'badge-danger';
    }
    fStatusE.value = status;
    previewE.innerHTML =
      '<span class="badge ' + cls + '" style="font-size:0.9rem;padding:6px 12px;">' + label + '</span>';
  }

  if (eTotal) eTotal.addEventListener('input', updateStatusEdit);
  if (ePaid)  ePaid.addEventListener('input',  updateStatusEdit);
  updateStatusEdit();

})();