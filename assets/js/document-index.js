/* ================================================================
   Documents Index — Filter, Dropdown & Bulk Delete
   ================================================================ */

(function () {

  document.addEventListener('click', function (e) {
    document.querySelectorAll('.aksi-dropdown.show').forEach(function (d) {
      var wrap = d.closest('.aksi-wrap');
      if (wrap && !wrap.contains(e.target)) d.classList.remove('show');
    });
    var moreBtn = e.target.closest('.btn-more');
    if (moreBtn) {
      var wrap     = moreBtn.closest('.aksi-wrap');
      var dropdown = wrap ? wrap.querySelector('.aksi-dropdown') : null;
      if (dropdown) dropdown.classList.toggle('show');
      e.stopPropagation();
    }
  });

  // ── +N chip expand ───────────────────────────────────────────────
  document.querySelectorAll('.file-chip-more').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var container = btn.closest('.doc-files');
      if (!container) return;
      container.querySelectorAll('.file-chip.hidden-chip').forEach(function (chip) {
        chip.style.display = 'inline-flex';
      });
      btn.style.display = 'none';
    });
  });

  $(document).ready(function () {
    var table = $('#table-documents').DataTable({
      language: { url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json' },
      columnDefs: [{ orderable: false, targets: [0, 1, 4, 6, 7] }],
      order: [[5, 'desc']],
      dom: 'tip'
    });

    $('#searchInput').on('keyup', function () {
      table.search(this.value).draw();
    });

    $('#filterVisa').on('change', function () {
      var val = this.value;
      $.fn.dataTable.ext.search.pop();
      if (val) {
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
          var row = table.row(dataIndex).node();
          return $(row).find('td[data-visa]').data('visa') === val;
        });
      }
      table.draw();
    });

    // ── Bulk Select ──────────────────────────────────────────────
    var toolbar    = document.getElementById('bulkToolbar');
    var countLabel = document.getElementById('bulkCount');
    var cbAll      = document.getElementById('cb-all');

    function getChecked() {
      return document.querySelectorAll('#table-documents tbody .row-cb:checked');
    }

    function updateToolbar() {
      var checked = getChecked();
      var n = checked.length;
      if (n > 0) {
        toolbar.classList.add('show');
        countLabel.textContent = n + ' grup dokumen dipilih';
      } else {
        toolbar.classList.remove('show');
        cbAll.checked = false;
        cbAll.indeterminate = false;
      }
      var total = document.querySelectorAll('#table-documents tbody .row-cb').length;
      cbAll.indeterminate = n > 0 && n < total;
      cbAll.checked = n > 0 && n === total;
    }

    cbAll.addEventListener('change', function () {
      table.rows({ search: 'applied' }).nodes().each(function (node) {
        var cb = node.querySelector('.row-cb');
        if (cb) {
          cb.checked = cbAll.checked;
          node.classList.toggle('selected-row', cbAll.checked);
        }
      });
      updateToolbar();
    });

    $('#table-documents tbody').on('change', '.row-cb', function () {
      this.closest('tr').classList.toggle('selected-row', this.checked);
      updateToolbar();
    });

    document.getElementById('btnBulkCancel').addEventListener('click', function () {
      document.querySelectorAll('#table-documents .row-cb').forEach(function (cb) {
        cb.checked = false;
        cb.closest('tr').classList.remove('selected-row');
      });
      cbAll.checked = false;
      cbAll.indeterminate = false;
      toolbar.classList.remove('show');
    });

    document.getElementById('btnBulkDelete').addEventListener('click', function () {
      var checked = getChecked();
      if (!checked.length) return;
      if (!confirm(checked.length + ' grup dokumen akan dihapus semua filenya. Yakin?')) return;
      var form   = document.getElementById('formBulkDelete');
      var inputs = document.getElementById('bulkInputs');
      inputs.innerHTML = '';
      checked.forEach(function (cb) {
        // value = "1,2,3" (multiple file IDs per group)
        cb.value.split(',').forEach(function (id) {
          var val = document.createElement('input');
          val.type  = 'hidden';
          val.name  = 'ids[]';
          val.value = id.trim();
          inputs.appendChild(val);
        });
      });
      form.submit();
    });
  });

})();
