/* ================================================================
   Payments Create — Auto-fill, info box, & auto-status logic
   ================================================================ */
(function () {
  var sel           = document.getElementById('applicationSelect');
  var infoBox       = document.getElementById('infoBox');
  var infoStatus    = document.getElementById('infoStatus');
  var infoTotal     = document.getElementById('infoTotal');
  var infoPaid      = document.getElementById('infoPaid');
  var infoSisa      = document.getElementById('infoSisa');
  var pidInput      = document.getElementById('paymentId');
  var fTotal        = document.getElementById('fTotal');
  var fDp           = document.getElementById('fDp');
  var fPaid         = document.getElementById('fPaid');
  var fStatus       = document.getElementById('fStatus');
  var statusPreview = document.getElementById('statusPreview');

  // Simpan total dari data existing agar bisa dipakai di updateStatusPreview
  var existingTotal = 0;
  var existingPaid  = 0;

  function formatRp(num) {
    return 'Rp ' + parseInt(num).toLocaleString('id-ID');
  }

  // Auto-update status badge berdasarkan nominal
  function updateStatusPreview() {
    if (!statusPreview || !fStatus) return;
    var total    = parseFloat(fTotal ? fTotal.value : 0) || 0;
    var newPaid  = parseFloat(fPaid  ? fPaid.value  : 0) || 0;
    // Total yang sudah dibayar = existing + input baru
    var totalPaid = existingPaid + newPaid;

    var status, label, badgeClass;
    if (total > 0 && totalPaid >= total) {
      status = 'paid';    label = 'Lunas';       badgeClass = 'badge-success';
    } else if (totalPaid > 0) {
      status = 'partial'; label = 'Baru DP';     badgeClass = 'badge-warning';
    } else {
      status = 'unpaid';  label = 'Belum Bayar'; badgeClass = 'badge-danger';
    }

    fStatus.value = status;
    statusPreview.innerHTML = '<span class="badge ' + badgeClass + '" style="font-size:0.9rem;padding:6px 12px;">' + label + '</span>';
  }

  // Pasang listener ke input nominal
  if (fTotal) fTotal.addEventListener('input', updateStatusPreview);
  if (fPaid)  fPaid.addEventListener('input',  updateStatusPreview);

  // Listener dropdown pengajuan
  if (sel) {
    sel.addEventListener('change', function () {
      var opt     = sel.options[sel.selectedIndex];
      var pid     = parseInt(opt.getAttribute('data-pid')    || '0');
      var pstatus = opt.getAttribute('data-pstatus')         || '';
      var total   = parseFloat(opt.getAttribute('data-total')|| '0');
      var paid    = parseFloat(opt.getAttribute('data-paid') || '0');
      var dp      = parseFloat(opt.getAttribute('data-dp')   || '0');

      if (pid > 0) {
        // MODE UPDATE: pengajuan sudah punya payment (unpaid/partial)
        pidInput.value = pid;
        existingTotal  = total;
        existingPaid   = paid;

        // Tampilkan info box ringkasan pembayaran sebelumnya
        var label = pstatus === 'partial' ? 'Baru DP' : 'Belum Bayar';
        infoStatus.textContent = label;
        infoTotal.textContent  = formatRp(total);
        infoPaid.textContent   = formatRp(paid);
        infoSisa.textContent   = formatRp(total - paid);
        infoBox.style.display  = 'block';

        // Auto-fill: Total Biaya otomatis terisi, DP terisi, Jumlah Dibayar dikosongkan
        fTotal.value = total;
        fDp.value    = dp;
        fPaid.value  = 0;  // kosongkan — user isi nominal pembayaran baru

        // Fokus ke field Jumlah Dibayar supaya user langsung bisa input
        if (fPaid) fPaid.focus();

      } else {
        // MODE INSERT: pengajuan belum punya payment sama sekali
        pidInput.value = '0';
        existingTotal  = 0;
        existingPaid   = 0;
        infoBox.style.display = 'none';
        fTotal.value  = '';
        fDp.value     = '0';
        fPaid.value   = '0';
      }

      // Update status preview
      updateStatusPreview();
    });
  }
})();