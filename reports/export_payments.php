<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';

$format    = $_GET['format']    ?? 'excel';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to   = $_GET['date_to']   ?? date('Y-m-d');
$status    = $_GET['status']    ?? '';

// Sanitasi input
$date_from = mysqli_real_escape_string($connection, $date_from);
$date_to   = mysqli_real_escape_string($connection, $date_to);
$status    = mysqli_real_escape_string($connection, $status);

// Build query dengan filter
$where = "WHERE DATE(p.created_at) BETWEEN '$date_from' AND '$date_to'";
if ($status) $where .= " AND p.status = '$status'";

$query = "SELECT p.id, ap.name AS applicant_name, ap.phone, ap.passport_number, a.country, a.visa_type,
                 p.payment_type, p.amount_total, p.dp_amount, p.amount_paid,
                 p.status, p.paid_at, p.created_at
          FROM payments p
          LEFT JOIN applications a  ON p.application_id = a.id
          LEFT JOIN applicants ap   ON a.applicant_id = ap.id
          $where
          ORDER BY p.created_at DESC";

$result = mysqli_query($connection, $query);
$rows   = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Hitung total
$total_tagihan  = array_sum(array_column($rows, 'amount_total'));
$total_dp       = array_sum(array_column($rows, 'dp_amount'));
$total_diterima = array_sum(array_column($rows, 'amount_paid'));
$total_sisa     = $total_tagihan - $total_diterima;

$status_label = ['unpaid' => 'Belum Bayar', 'dp' => 'DP', 'paid' => 'Lunas'];
$period       = date('d/m/Y', strtotime($date_from)) . ' s/d ' . date('d/m/Y', strtotime($date_to));
$filename     = 'Laporan_Pembayaran_' . date('Ymd');

// ================================================================
// EXPORT EXCEL
// ================================================================
if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    ?>
    <html>
    <head><meta charset="UTF-8"></head>
    <body>
    <table border="1">
      <tr>
        <td colspan="11" style="font-weight:bold;font-size:14pt;text-align:center;">LAPORAN PEMBAYARAN — AKUVISA</td>
      </tr>
      <tr>
        <td colspan="11" style="text-align:center;">Periode: <?= $period ?></td>
      </tr>
      <tr><td colspan="11"></td></tr>
      <tr style="background:#1F4E79;color:#FFFFFF;font-weight:bold;text-align:center;">
        <td>No</td>
        <td>Nama Pemohon</td>
        <td>No. HP</td>
        <td>No. Passport</td>
        <td>Negara / Visa</td>
        <td>Jenis Pembayaran</td>
        <td>Total Biaya</td>
        <td>DP</td>
        <td>Sudah Dibayar</td>
        <td>Status</td>
        <td>Tgl Bayar</td>
      </tr>
      <?php $no = 1; foreach ($rows as $row): ?>
      <tr>
        <td style="text-align:center;"><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['passport_number'] ?? '-') ?></td>
        <td><?= htmlspecialchars(($row['country'] ?? '-') . ' / ' . ($row['visa_type'] ?? '-')) ?></td>
        <td><?= htmlspecialchars($row['payment_type']) ?></td>
        <td style="text-align:right;">Rp <?= number_format($row['amount_total'], 0, ',', '.') ?></td>
        <td style="text-align:right;">Rp <?= number_format($row['dp_amount'], 0, ',', '.') ?></td>
        <td style="text-align:right;">Rp <?= number_format($row['amount_paid'], 0, ',', '.') ?></td>
        <td><?= $status_label[$row['status']] ?? $row['status'] ?></td>
        <td><?= $row['paid_at'] ? date('d/m/Y', strtotime($row['paid_at'])) : '-' ?></td>
      </tr>
      <?php endforeach; ?>
      <tr><td colspan="11"></td></tr>
      <tr style="font-weight:bold;background:#EBF3FB;">
        <td colspan="6" style="text-align:right;">TOTAL</td>
        <td style="text-align:right;">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></td>
        <td style="text-align:right;">Rp <?= number_format($total_dp, 0, ',', '.') ?></td>
        <td style="text-align:right;">Rp <?= number_format($total_diterima, 0, ',', '.') ?></td>
        <td colspan="2">Sisa: Rp <?= number_format($total_sisa, 0, ',', '.') ?></td>
      </tr>
    </table>
    </body>
    </html>
    <?php
    exit;
}

// ================================================================
// EXPORT PDF
// ================================================================
if ($format === 'pdf') {
    header('Content-Type: text/html; charset=UTF-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Laporan Pembayaran</title>
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #1F4E79; padding-bottom: 10px; }
        .header h1 { font-size: 16px; color: #1F4E79; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead tr { background: #1F4E79; color: #fff; }
        thead th { padding: 7px 5px; text-align: left; font-size: 10px; }
        tbody tr:nth-child(even) { background: #EBF3FB; }
        tbody td { padding: 5px; border-bottom: 1px solid #ddd; font-size: 10px; }
        tfoot tr { background: #d0e4f5; font-weight: bold; }
        tfoot td { padding: 6px 5px; font-size: 10px; }
        .badge { padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-success { background: #28a745; color: #fff; }
        .badge-danger  { background: #dc3545; color: #fff; }
        .text-right { text-align: right; }
        .summary-box { display: flex; gap: 10px; margin-bottom: 14px; }
        .summary-item { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 8px 10px; }
        .summary-item .label { font-size: 9px; color: #888; }
        .summary-item .value { font-size: 12px; font-weight: bold; color: #1F4E79; }
        .footer { margin-top: 16px; display: flex; justify-content: space-between; font-size: 9px; color: #888; }
        @media print {
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>

      <div class="no-print" style="padding:10px;background:#f0f0f0;margin-bottom:15px;text-align:center;">
        <button onclick="window.print()" style="padding:8px 20px;background:#dc3545;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px;">
          🖨️ Cetak / Save PDF
        </button>
        <button onclick="if(window.opener){window.close();}else{window.history.back();}" style="padding:8px 20px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px;margin-left:8px;">
          ✕ Tutup
        </button>
      </div>

      <div class="header">
        <h1>LAPORAN PEMBAYARAN</h1>
        <p>AKUVISA — Sistem Manajemen Pengajuan Visa</p>
        <p>Periode: <?= $period ?> <?= $status ? '| Status: ' . ($status_label[$status] ?? $status) : '' ?></p>
      </div>

      <!-- Summary boxes -->
      <div class="summary-box">
        <div class="summary-item">
          <div class="label">Total Tagihan</div>
          <div class="value">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></div>
        </div>
        <div class="summary-item">
          <div class="label">Total Diterima</div>
          <div class="value" style="color:#28a745">Rp <?= number_format($total_diterima, 0, ',', '.') ?></div>
        </div>
        <div class="summary-item">
          <div class="label">Sisa Tagihan</div>
          <div class="value" style="color:#dc3545">Rp <?= number_format($total_sisa, 0, ',', '.') ?></div>
        </div>
        <div class="summary-item">
          <div class="label">Jumlah Data</div>
          <div class="value"><?= count($rows) ?> transaksi</div>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width:25px">No</th>
            <th>Pemohon</th>
            <th>No. Passport</th>
            <th>Negara / Visa</th>
            <th>Jenis Bayar</th>
            <th class="text-right">Total</th>
            <th class="text-right">Dibayar</th>
            <th class="text-right">Sisa</th>
            <th>Status</th>
            <th>Tgl Bayar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $row):
            $sisa = $row['amount_total'] - $row['amount_paid'];
          ?>
          <tr>
            <td class="text-right"><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['passport_number'] ?? '-') ?></td>
            <td><?= htmlspecialchars(($row['country'] ?? '-')) ?><br><small><?= htmlspecialchars($row['visa_type'] ?? '') ?></small></td>
            <td><?= htmlspecialchars($row['payment_type']) ?></td>
            <td class="text-right">Rp <?= number_format($row['amount_total'], 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($row['amount_paid'], 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
            <td>
              <?php if ($row['status'] === 'paid'): ?>
                <span class="badge badge-success">Lunas</span>
              <?php elseif ($row['status'] === 'dp'): ?>
                <span class="badge badge-warning">DP</span>
              <?php else: ?>
                <span class="badge badge-danger">Belum Bayar</span>
              <?php endif; ?>
            </td>
            <td><?= $row['paid_at'] ? date('d/m/Y', strtotime($row['paid_at'])) : '-' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-right">TOTAL</td>
            <td class="text-right">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($total_diterima, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($total_sisa, 0, ',', '.') ?></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>

      <div class="footer">
        <span>AKUVISA &copy; <?= date('Y') ?></span>
        <span>Dicetak: <?= date('d/m/Y H:i:s') ?></span>
      </div>

    </body>
    </html>
    <?php
    exit;
}