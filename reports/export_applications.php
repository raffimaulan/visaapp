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
$where = "WHERE DATE(a.created_at) BETWEEN '$date_from' AND '$date_to'";
if ($status) $where .= " AND a.status = '$status'";

$query = "SELECT a.id, ap.name AS applicant_name, ap.phone, ap.passport_number, a.country,
                 a.visa_type, a.status, a.created_at
          FROM applications a
          LEFT JOIN applicants ap ON a.applicant_id = ap.id
          $where
          ORDER BY a.created_at DESC";

$result = mysqli_query($connection, $query);
$rows   = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

$status_label = ['in_process' => 'Diproses', 'completed' => 'Selesai', 'rejected' => 'Ditolak'];
$period       = date('d/m/Y', strtotime($date_from)) . ' s/d ' . date('d/m/Y', strtotime($date_to));
$filename     = 'Laporan_Pengajuan_' . date('Ymd');

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
        <td colspan="7" style="font-weight:bold;font-size:14pt;text-align:center;">LAPORAN PENGAJUAN VISA — AKUVISA</td>
      </tr>
      <tr>
        <td colspan="7" style="text-align:center;">Periode: <?= $period ?></td>
      </tr>
      <tr><td colspan="7"></td></tr>
      <tr style="background:#1F4E79;color:#FFFFFF;font-weight:bold;text-align:center;">
        <td>No</td>
        <td>Nama Pemohon</td>
        <td>No. HP</td>
        <td>No. Passport</td>
        <td>Negara Tujuan</td>
        <td>Jenis Visa</td>
        <td>Status</td>
        <td>Tanggal Pengajuan</td>
      </tr>
      <?php $no = 1; foreach ($rows as $row): ?>
      <tr>
        <td style="text-align:center;"><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['passport_number'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['country']) ?></td>
        <td><?= htmlspecialchars($row['visa_type']) ?></td>
        <td><?= $status_label[$row['status']] ?? $row['status'] ?></td>
        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="8"></td>
      </tr>
      <tr>
        <td colspan="7" style="font-weight:bold;">Total Data</td>
        <td style="font-weight:bold;"><?= count($rows) ?> pengajuan</td>
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
      <title>Laporan Pengajuan Visa</title>
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1F4E79; padding-bottom: 10px; }
        .header h1 { font-size: 16px; color: #1F4E79; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead tr { background: #1F4E79; color: #fff; }
        thead th { padding: 8px 6px; text-align: left; font-size: 11px; }
        tbody tr:nth-child(even) { background: #EBF3FB; }
        tbody td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-warning  { background: #ffc107; color: #333; }
        .badge-success  { background: #28a745; color: #fff; }
        .badge-danger   { background: #dc3545; color: #fff; }
        .footer { margin-top: 20px; display: flex; justify-content: space-between; font-size: 10px; color: #888; }
        .summary { margin-bottom: 15px; font-size: 11px; }
        .summary span { font-weight: bold; color: #1F4E79; }
        @media print {
          .no-print { display: none; }
          body { margin: 0; }
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
        <h1>LAPORAN PENGAJUAN VISA</h1>
        <p>AKUVISA — Sistem Manajemen Pengajuan Visa</p>
        <p>Periode: <?= $period ?> <?= $status ? '| Status: ' . ($status_label[$status] ?? $status) : '' ?></p>
      </div>

      <div class="summary">
        Total Data: <span><?= count($rows) ?> pengajuan</span> &nbsp;|&nbsp;
        Dicetak: <span><?= date('d/m/Y H:i') ?></span>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width:30px">No</th>
            <th>Nama Pemohon</th>
            <th>No. HP</th>
            <th>No. Passport</th>
            <th>Negara Tujuan</th>
            <th>Jenis Visa</th>
            <th>Status</th>
            <th>Tgl Pengajuan</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($rows as $row): ?>
          <tr>
            <td style="text-align:center;"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['passport_number'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['country']) ?></td>
            <td><?= htmlspecialchars($row['visa_type']) ?></td>
            <td>
              <?php if ($row['status'] === 'in_process'): ?>
                <span class="badge badge-warning">Diproses</span>
              <?php elseif ($row['status'] === 'completed'): ?>
                <span class="badge badge-success">Selesai</span>
              <?php else: ?>
                <span class="badge badge-danger">Ditolak</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
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