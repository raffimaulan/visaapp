<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../layout/_top.php';

$paymentModel = new Payment($connection);
$payments     = $paymentModel->getAll();

function avatarInitials($name) {
    $parts = explode(' ', trim($name));
    $init  = strtoupper(substr($parts[0], 0, 1));
    if (isset($parts[1])) $init .= strtoupper(substr($parts[1], 0, 1));
    return $init;
}
$avatarColors = ['#6777ef','#f59e0b','#10b981','#ef4444','#8b5cf6','#3b82f6','#ec4899','#14b8a6'];
function avatarColor($name) {
    global $avatarColors;
    return $avatarColors[abs(crc32($name)) % count($avatarColors)];
}
function visaBadgeClass($type) {
    $map = ['Tourist'=>'tourist','Student'=>'student','Business'=>'business','Work'=>'work','Transit'=>'transit'];
    return $map[$type] ?? 'default';
}
function statusLabel($status) {
    $map = ['paid'=>'Lunas','partial'=>'Baru DP','unpaid'=>'Belum Bayar'];
    return $map[$status] ?? ucfirst($status);
}
function payTypeLabel($type) {
    $map = ['full'=>'Pembayaran Penuh','dp'=>'DP / Uang Muka'];
    return $map[$type] ?? ucfirst($type);
}
?>

<link rel="stylesheet" href="../assets/css/filter-bar.css">
<link rel="stylesheet" href="../assets/css/payment-index.css">
<link rel="stylesheet" href="../assets/css/bulk-select.css">

<section class="section">
  <div class="section-header">
    <h1>Data Pembayaran</h1>
    <div class="section-header-button">
      <a href="create.php" class="btn btn-primary">+ Tambah Pembayaran</a>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- BULK TOOLBAR -->
        <div class="bulk-toolbar" id="bulkToolbar">
          <span class="bulk-count" id="bulkCount">0 item dipilih</span>
          <button type="button" class="btn-bulk-cancel" id="btnBulkCancel">
            <i class="fas fa-times"></i> Batal
          </button>
          <button type="button" class="btn-bulk-delete" id="btnBulkDelete">
            <i class="fas fa-trash"></i> Hapus Terpilih
          </button>
        </div>

        <div class="filter-bar">
          <div class="filter-search">
            <i class="fas fa-search filter-search-icon"></i>
            <input type="text" id="searchInput" class="filter-search-input" placeholder="Cari pemohon...">
          </div>
          <select id="filterStatus" class="filter-select">
            <option value="">All Payments</option>
            <option value="paid">Lunas</option>
            <option value="partial">Baru DP</option>
            <option value="unpaid">Belum Bayar</option>
          </select>
        </div>

        <form id="formBulkDelete" action="delete_bulk.php" method="POST" style="display:none">
          <div id="bulkInputs"></div>
        </form>

        <div class="table-responsive">
          <table class="table" id="table-payments">
            <thead>
              <tr>
                <th class="cb-col"><input type="checkbox" id="cb-all" title="Pilih Semua"></th>
                <th>No</th>
                <th>Pemohon</th>
                <th>Tujuan</th>
                <th>Jenis Bayar</th>
                <th>Biaya &amp; Progress</th>
                <th>Tgl Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; while ($row = mysqli_fetch_assoc($payments)):
                $initials  = avatarInitials($row['applicant_name'] ?? 'U');
                $bgColor   = avatarColor($row['applicant_name'] ?? '');
                $visaClass = visaBadgeClass($row['visa_type'] ?? '');
                $statusLbl = statusLabel($row['status'] ?? 'unpaid');
                $ptLabel   = payTypeLabel($row['payment_type'] ?? '');
                $ptClass   = ($row['payment_type'] === 'full') ? 'full' : 'dp';
                $total     = (float)($row['amount_total'] ?? 0);
                $paid      = (float)($row['amount_paid']  ?? 0);
                $pct       = $total > 0 ? min(100, round($paid / $total * 100)) : 0;
                $fillClass = $row['status'] ?? 'unpaid';
              ?>
              <tr>
                <td class="cb-row"><input type="checkbox" class="row-cb" value="<?= $row['id'] ?>"></td>
                <td><?= $no++ ?></td>
                <td data-search="<?= htmlspecialchars($row['applicant_name'] ?? '') ?>">
                  <div class="pay-applicant">
                    <div class="pay-avatar" style="background:<?= $bgColor ?>"><?= htmlspecialchars($initials) ?></div>
                    <div>
                      <div class="pay-name"><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></div>
                      <div class="pay-sub"><?= htmlspecialchars($row['country'] ?? '') ?></div>
                    </div>
                  </div>
                </td>
                <td data-search="<?= htmlspecialchars(($row['country'] ?? '') . ' ' . ($row['visa_type'] ?? '')) ?>">
                  <div style="font-size:14px;font-weight:500;color:#374151"><?= htmlspecialchars($row['country'] ?? '-') ?></div>
                  <div>
                    <span class="badge-visa <?= $visaClass ?>">
                      <i class="fas fa-suitcase"></i>
                      <?= htmlspecialchars($row['visa_type'] ?? '') ?>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="pay-type-chip <?= $ptClass ?>">
                    <i class="fas fa-<?= $ptClass === 'full' ? 'check-circle' : 'layer-group' ?>"></i>
                    <?= htmlspecialchars($ptLabel) ?>
                  </span>
                </td>
                <td>
                  <div class="pay-progress-wrap">
                    <div class="pay-amount">Rp <?= number_format($total, 0, ',', '.') ?></div>
                    <div class="pay-progress-label">
                      <span>Dibayar: Rp <?= number_format($paid, 0, ',', '.') ?></span>
                      <span><?= $pct ?>%</span>
                    </div>
                    <div class="pay-progress-bar">
                      <div class="pay-progress-fill <?= $fillClass ?>" style="width:<?= $pct ?>%"></div>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if ($row['paid_at']): ?>
                    <div class="pay-date">
                      <span><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['paid_at'])) ?></span>
                      <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($row['paid_at'])) ?></span>
                    </div>
                  <?php else: ?>
                    <span style="color:#9ca3af;font-size:13px">—</span>
                  <?php endif; ?>
                </td>
                <td data-status="<?= htmlspecialchars($row['status'] ?? 'unpaid') ?>">
                  <span class="status-badge <?= htmlspecialchars($row['status'] ?? 'unpaid') ?>">
                    <?= $statusLbl ?>
                  </span>
                </td>
                <td>
                  <div class="aksi-wrap">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-aksi">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn-more" title="Opsi lainnya">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="aksi-dropdown">
                      <a href="../applications/index.php?id=<?= $row['application_id'] ?>">
                        <i class="fas fa-file-alt"></i> Lihat Pengajuan
                      </a>
                      <?php if ($row['proof']): ?>
                        <a href="../<?= htmlspecialchars($row['proof']) ?>" target="_blank">
                          <i class="fas fa-image"></i> Bukti Bayar
                        </a>
                      <?php endif; ?>
                      <div class="divider"></div>
                      <a href="delete.php?id=<?= $row['id'] ?>" class="text-danger"
                         onclick="return confirm('Yakin hapus data pembayaran ini?')">
                        <i class="fas fa-trash"></i> Hapus
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>
