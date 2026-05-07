<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../layout/_top.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: index.php");
    exit;
}

$paymentModel = new Payment($connection);
$payment = $paymentModel->getById($id);

if (!$payment) {
    $_SESSION['error'] = "Data pembayaran tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Ambil semua pengajuan untuk dropdown
// Tampilkan pengajuan yang terpilih sekarang + pengajuan lain yang belum punya payment
$applications = mysqli_query($connection,
    "SELECT a.id, a.country, a.visa_type, ap.name AS applicant_name
     FROM applications a
     LEFT JOIN applicants ap ON a.applicant_id = ap.id
     LEFT JOIN payments p ON p.application_id = a.id AND p.id != {$payment['id']}
     WHERE p.id IS NULL OR a.id = {$payment['application_id']}
     ORDER BY ap.name ASC"
);
?>

<section class="section">
  <div class="section-header">
    <h1>Edit Pembayaran</h1>
    <div class="section-header-breadcrumb">
      <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">

        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="update.php" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $payment['id'] ?>">
          <input type="hidden" name="existing_proof" value="<?= htmlspecialchars($payment['proof'] ?? '') ?>">
          <input type="hidden" name="existing_proof_pelunasan" value="<?= htmlspecialchars($payment['proof_pelunasan'] ?? '') ?>">

          <div class="form-group">
            <label>Pengajuan Visa</label>
            <select name="application_id" class="form-control" required>
              <option value="">-- Pilih Pengajuan --</option>
              <?php while ($a = mysqli_fetch_assoc($applications)): ?>
                <option value="<?= $a['id'] ?>"
                  <?= $a['id'] == $payment['application_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($a['applicant_name']) ?> —
                  <?= htmlspecialchars($a['country']) ?> (<?= htmlspecialchars($a['visa_type']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Jenis Pembayaran</label>
            <select name="payment_type" class="form-control" required>
              <?php $ptypes = ['dp' => 'DP / Uang Muka', 'full' => 'Pembayaran Penuh']; ?>
              <?php foreach ($ptypes as $val => $label): ?>
                <option value="<?= $val ?>"
                  <?= $payment['payment_type'] === $val ? 'selected' : '' ?>>
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Total Biaya (Rp)</label>
              <input type="number" name="amount_total" class="form-control"
                     value="<?= $payment['amount_total'] ?>" min="0" step="any" required>
            </div>
            <div class="form-group col-md-4">
              <label>DP / Uang Muka (Rp)</label>
              <input type="number" name="dp_amount" class="form-control"
                     value="<?= $payment['dp_amount'] ?>" min="0" step="any">
            </div>
            <div class="form-group col-md-4">
              <label>Jumlah Dibayar (Rp)</label>
              <input type="number" name="amount_paid" class="form-control"
                     value="<?= $payment['amount_paid'] ?>" min="0" step="any">
            </div>
          </div>

          <div class="form-group">
            <label>Status Pembayaran <small class="text-muted">(otomatis dari jumlah dibayar)</small></label>
            <div id="statusPreviewEdit" class="mt-1">
              <?php
                if ($payment['status'] === 'paid') {
                    echo '<span class="badge badge-success" style="font-size:0.9rem;padding:6px 12px;">Lunas</span>';
                } elseif ($payment['status'] === 'partial') {
                    echo '<span class="badge badge-warning" style="font-size:0.9rem;padding:6px 12px;">Baru DP</span>';
                } else {
                    echo '<span class="badge badge-danger" style="font-size:0.9rem;padding:6px 12px;">Belum Bayar</span>';
                }
              ?>
            </div>
            <input type="hidden" name="status" id="fStatusEdit"
                   value="<?= htmlspecialchars($payment['status']) ?>">
          </div>

          <div class="form-group">
            <label>Bukti Pembayaran DP</label>
            <?php if (!empty($payment['proof'])): ?>
              <p class="mb-1">
                <small>File saat ini:
                  <a href="../uploads/proofs/<?= htmlspecialchars($payment['proof']) ?>" target="_blank">
                    <?= htmlspecialchars($payment['proof']) ?>
                  </a>
                </small>
              </p>
            <?php endif; ?>
            <input type="file" name="proof" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti. Maks 2MB (jpg/png/pdf).</small>
          </div>

          <div class="form-group">
            <label>Bukti Pelunasan</label>
            <?php if (!empty($payment['proof_pelunasan'])): ?>
              <p class="mb-1">
                <small>File saat ini:
                  <a href="../uploads/proofs/<?= htmlspecialchars($payment['proof_pelunasan']) ?>" target="_blank">
                    <?= htmlspecialchars($payment['proof_pelunasan']) ?>
                  </a>
                </small>
              </p>
            <?php endif; ?>
            <input type="file" name="proof_pelunasan" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti. Maks 2MB (jpg/png/pdf).</small>
          </div>

          <div class="form-group">
            <label>Tanggal Bayar</label>
            <input type="date" name="paid_at" class="form-control"
                   value="<?= $payment['paid_at'] ? date('Y-m-d', strtotime($payment['paid_at'])) : '' ?>">
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>
<script>
(function() {
  var fTotal  = document.querySelector('[name="amount_total"]');
  var fPaid   = document.querySelector('[name="amount_paid"]');
  var fStatus = document.getElementById('fStatusEdit');
  var preview = document.getElementById('statusPreviewEdit');

  function updateStatus() {
    var total = parseFloat(fTotal.value) || 0;
    var paid  = parseFloat(fPaid.value)  || 0;
    var status, label, cls;
    if (total > 0 && paid >= total) {
      status = 'paid'; label = 'Lunas'; cls = 'badge-success';
    } else if (paid > 0) {
      status = 'partial'; label = 'Baru DP'; cls = 'badge-warning';
    } else {
      status = 'unpaid'; label = 'Belum Bayar'; cls = 'badge-danger';
    }
    fStatus.value = status;
    preview.innerHTML = '<span class="badge ' + cls + '" style="font-size:0.9rem;padding:6px 12px;">' + label + '</span>';
  }

  if (fTotal) fTotal.addEventListener('input', updateStatus);
  if (fPaid)  fPaid.addEventListener('input',  updateStatus);
})();
</script>