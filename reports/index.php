<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../layout/_top.php';

// Hitung ringkasan untuk dashboard laporan
$total_applications = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM applications"))[0];
$total_payments     = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM payments"))[0];
$total_pendapatan   = mysqli_fetch_row(mysqli_query($connection, "SELECT COALESCE(SUM(amount_paid),0) FROM payments"))[0];
$total_pending      = mysqli_fetch_row(mysqli_query($connection, "SELECT COALESCE(SUM(amount_total - amount_paid),0) FROM payments WHERE status != 'paid'"))[0];
?>

<section class="section">
  <div class="section-header">
    <h1>Laporan</h1>
  </div>

  <div class="section-body">

    <!-- Ringkasan -->
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i class="fas fa-file-alt"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Total Pengajuan</h4></div>
            <div class="card-body"><?= $total_applications ?></div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i class="fas fa-money-bill"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Total Pembayaran</h4></div>
            <div class="card-body"><?= $total_payments ?></div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i class="fas fa-coins"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Total Diterima</h4></div>
            <div class="card-body">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger"><i class="fas fa-exclamation-circle"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Sisa Tagihan</h4></div>
            <div class="card-body">Rp <?= number_format($total_pending, 0, ',', '.') ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kartu Export -->
    <div class="row">

      <!-- Laporan Pengajuan -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4><i class="fas fa-file-alt mr-2"></i>Laporan Pengajuan Visa</h4></div>
          <div class="card-body">
            <p class="text-muted">Ekspor seluruh data pengajuan visa beserta nama pemohon, negara tujuan, jenis visa, dan status.</p>
            <form method="GET" action="export_applications.php">
              <div class="form-row align-items-end">
                <div class="col-md-5">
                  <label>Dari Tanggal</label>
                  <input type="date" name="date_from" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-5">
                  <label>Sampai Tanggal</label>
                  <input type="date" name="date_to" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="form-row mt-2">
                <div class="col-md-10">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="in_process">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="rejected">Ditolak</option>
                  </select>
                </div>
              </div>
              <div class="mt-3">
                <button type="submit" name="format" value="excel" class="btn btn-success">
                  <i class="fas fa-file-excel mr-1"></i> Export Excel
                </button>
                <button type="submit" name="format" value="pdf" class="btn btn-danger ml-2">
                  <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Laporan Pembayaran -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4><i class="fas fa-money-bill-wave mr-2"></i>Laporan Pembayaran</h4></div>
          <div class="card-body">
            <p class="text-muted">Ekspor seluruh data pembayaran beserta detail nominal, DP, status, dan tanggal bayar.</p>
            <form method="GET" action="export_payments.php">
              <div class="form-row align-items-end">
                <div class="col-md-5">
                  <label>Dari Tanggal</label>
                  <input type="date" name="date_from" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-5">
                  <label>Sampai Tanggal</label>
                  <input type="date" name="date_to" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="form-row mt-2">
                <div class="col-md-10">
                  <label>Status Pembayaran</label>
                  <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="unpaid">Belum Bayar</option>
                    <option value="dp">DP</option>
                    <option value="paid">Lunas</option>
                  </select>
                </div>
              </div>
              <div class="mt-3">
                <button type="submit" name="format" value="excel" class="btn btn-success">
                  <i class="fas fa-file-excel mr-1"></i> Export Excel
                </button>
                <button type="submit" name="format" value="pdf" class="btn btn-danger ml-2">
                  <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>