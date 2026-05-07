<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../layout/_top.php';

// Ambil semua pengajuan yang BELUM LUNAS:
// - Belum punya payment sama sekali
// - Punya payment tapi statusnya unpaid atau dp
$sql = "SELECT a.id, a.country, a.visa_type, ap.name AS applicant_name,
               p.id AS payment_id, p.status AS payment_status,
               p.amount_total, p.amount_paid, p.dp_amount
        FROM applications a
        LEFT JOIN applicants ap ON a.applicant_id = ap.id
        LEFT JOIN payments p ON p.application_id = a.id
        WHERE a.id NOT IN (
            SELECT application_id FROM payments WHERE status = 'paid'
        )
        ORDER BY ap.name ASC";

$result   = mysqli_query($connection, $sql);
$app_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $app_list[] = $row;
}
?>
<link rel="stylesheet" href="../assets/css/filter-bar.css">

<section class="section">
  <div class="section-header">
    <h1>Tambah / Lunasi Pembayaran</h1>
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

        <form method="POST" action="store.php" enctype="multipart/form-data">

          <!-- PILIH PENGAJUAN -->
          <div class="form-group">
            <label>Pengajuan Visa</label>
            <select name="application_id" id="applicationSelect" class="form-control" required>
              <option value="">-- Pilih Pengajuan --</option>
              <?php foreach ($app_list as $a):
                $badge = '';
                if (!empty($a['payment_id'])) {
                    if ($a['payment_status'] === 'partial') {
                        $sisa  = $a['amount_total'] - $a['amount_paid'];
                        $badge = ' [Baru DP | Sisa: Rp ' . number_format($sisa, 0, ',', '.') . ']';
                    } else {
                        $badge = ' [Belum Bayar]';
                    }
                }
              ?>
              <option value="<?= $a['id'] ?>"
                data-pid="<?= (int)($a['payment_id'] ?? 0) ?>"
                data-pstatus="<?= htmlspecialchars($a['payment_status'] ?? '') ?>"
                data-total="<?= (float)($a['amount_total'] ?? 0) ?>"
                data-paid="<?= (float)($a['amount_paid'] ?? 0) ?>"
                data-dp="<?= (float)($a['dp_amount'] ?? 0) ?>">
                <?= htmlspecialchars($a['applicant_name']) ?> —
                <?= htmlspecialchars($a['country']) ?>
                (<?= htmlspecialchars($a['visa_type']) ?>)
                <?= $badge ?>
              </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Hanya menampilkan pengajuan yang belum lunas.</small>
          </div>

          <!-- INFO JIKA SUDAH ADA PAYMENT -->
          <div id="infoBox" class="alert alert-warning" style="display:none">
            <i class="fas fa-info-circle"></i>
            Pengajuan ini sudah memiliki data pembayaran (<span id="infoStatus"></span>).
            Form ini akan <strong>memperbarui</strong> data yang ada.
            <br>Total: <strong id="infoTotal"></strong> |
            Sudah dibayar: <strong id="infoPaid"></strong> |
            Sisa: <strong id="infoSisa"></strong>
          </div>

          <!-- Hidden field: 0 = INSERT baru, >0 = UPDATE existing -->
          <input type="hidden" name="payment_id" id="paymentId" value="0">

          <!-- JENIS PEMBAYARAN -->
          <div class="form-group">
            <label>Jenis Pembayaran</label>
            <select name="payment_type" class="form-control" required>
              <option value="">-- Pilih Jenis --</option>
              <option value="dp">DP / Uang Muka</option>
              <option value="full">Pembayaran Penuh</option>
              
            </select>
          </div>

          <!-- NOMINAL -->
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Total Biaya (Rp)</label>
              <input type="number" name="amount_total" id="fTotal"
                     class="form-control" placeholder="0" min="0" step="any" required>
            </div>
            <div class="form-group col-md-4">
              <label>DP / Uang Muka (Rp)</label>
              <input type="number" name="dp_amount" id="fDp"
                     class="form-control" placeholder="0" min="0" step="any" value="0">
            </div>
            <div class="form-group col-md-4">
              <label>Jumlah Dibayar (Rp)</label>
              <input type="number" name="amount_paid" id="fPaid"
                     class="form-control" placeholder="0" min="0" step="any" value="0">
            </div>
          </div>

          <!-- STATUS (auto-detect, tidak perlu dipilih manual) -->
          <div class="form-group">
            <label>Status Pembayaran <small class="text-muted">(otomatis dari jumlah dibayar)</small></label>
            <div id="statusPreview" class="mt-1">
              <span class="badge badge-danger" style="font-size:0.9rem; padding:6px 12px;">Belum Bayar</span>
            </div>
            <input type="hidden" name="status" id="fStatus" value="unpaid">
          </div>

          <!-- BUKTI -->
          <div class="form-group">
            <label>Bukti Pembayaran DP
              <small class="text-muted">(opsional, jpg/png/pdf maks 2MB)</small>
            </label>
            <input type="file" name="proof" class="form-control-file"
                   accept=".jpg,.jpeg,.png,.pdf">
          </div>

          <div class="form-group">
            <label>Bukti Pelunasan
              <small class="text-muted">(opsional, diisi saat melunasi — jpg/png/pdf maks 2MB)</small>
            </label>
            <input type="file" name="proof_pelunasan" class="form-control-file"
                   accept=".jpg,.jpeg,.png,.pdf">
          </div>

          <!-- TANGGAL -->
          <div class="form-group">
            <label>Tanggal Bayar <small class="text-muted">(opsional)</small></label>
            <input type="date" name="paid_at" class="form-control">
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan
          </button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<script src="../assets/js/payment.js"></script>
<script>
// Trigger auto-fill saat halaman load jika dropdown sudah ada pilihan
document.addEventListener('DOMContentLoaded', function () {
  var sel = document.getElementById('applicationSelect');
  if (sel && sel.value) {
    sel.dispatchEvent(new Event('change'));
  }
});
</script>
<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>