<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../layout/_top.php';

// ================================================================
// STAT CARDS
// ================================================================
$total_applications = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM applications"))[0];
$total_in_process   = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM applications WHERE status='in_process'"))[0];
$total_completed    = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM applications WHERE status='completed'"))[0];
$total_paid         = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM payments WHERE status='paid'"))[0];
$total_pendapatan   = mysqli_fetch_row(mysqli_query($connection, "SELECT COALESCE(SUM(amount_paid),0) FROM payments"))[0];
$total_pending      = mysqli_fetch_row(mysqli_query($connection, "SELECT COALESCE(SUM(amount_total-amount_paid),0) FROM payments WHERE status != 'paid'"))[0];

// Stat bulan ini vs bulan lalu (untuk growth label)
$this_month_apps  = mysqli_fetch_row(mysqli_query($connection,
    "SELECT COUNT(*) FROM applications WHERE YEAR(created_at)=YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW())"))[0];
$last_month_apps  = mysqli_fetch_row(mysqli_query($connection,
    "SELECT COUNT(*) FROM applications WHERE YEAR(created_at)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) AND MONTH(created_at)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))"))[0];
$growth_pct = $last_month_apps > 0 ? round((($this_month_apps - $last_month_apps) / $last_month_apps) * 100) : 0;

$today_process = mysqli_fetch_row(mysqli_query($connection,
    "SELECT COUNT(*) FROM applications WHERE status='in_process' AND DATE(created_at)=CURDATE()"))[0];

$last_month_completed = mysqli_fetch_row(mysqli_query($connection,
    "SELECT COUNT(*) FROM applications WHERE status='completed' AND YEAR(created_at)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) AND MONTH(created_at)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))"))[0];
$growth_completed = $last_month_completed > 0 ? round((($total_completed - $last_month_completed) / $last_month_completed) * 100) : 0;

$total_dp         = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM payments WHERE status='dp'"))[0];
$total_payments   = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM payments"))[0];
$payment_rate     = $total_payments > 0 ? round(($total_paid / $total_payments) * 100) : 0;

// ================================================================
// RECENT APPLICATIONS (7 terbaru dengan data pemohon)
// ================================================================
$recent = mysqli_query($connection,
    "SELECT a.country, a.visa_type, a.status, a.created_at,
            ap.name AS applicant_name, ap.phone,
            p.status AS pay_status
     FROM applications a
     LEFT JOIN applicants ap ON a.applicant_id = ap.id
     LEFT JOIN payments p ON p.application_id = a.id
     ORDER BY a.created_at DESC LIMIT 7");

// ================================================================
// TOP DESTINATIONS
// ================================================================
$dest_result = mysqli_query($connection,
    "SELECT country, COUNT(*) as total FROM applications GROUP BY country ORDER BY total DESC LIMIT 5");
$destinations = [];
while ($r = mysqli_fetch_assoc($dest_result)) {
    $destinations[] = $r;
}
$max_dest = !empty($destinations) ? $destinations[0]['total'] : 1;
?>

<!-- Dashboard CSS -->
<link rel="stylesheet" href="../assets/css/dashboard.css">

<section class="section">
  <div class="section-header d-flex align-items-center justify-content-between">
    <div>
      <h1>Dashboard</h1>
    </div>
  </div>

  <!-- STAT CARDS -->
  <div class="row stat-row">

    <div class="col-stat col-md-6 col-sm-6 col-12">
      <div class="dash-stat-card">
        <div class="dash-stat-icon dash-icon-blue">
          <i class="far fa-file-alt"></i>
        </div>
        <div class="dash-stat-number"><?= $total_applications ?></div>
        <div class="dash-stat-label">Total Applications</div>
        <div class="dash-stat-growth <?= $growth_pct >= 0 ? 'growth-up' : 'growth-down' ?>">
          <?= $growth_pct >= 0 ? '↑' : '↓' ?> <?= abs($growth_pct) ?>% this month
        </div>
      </div>
    </div>

    <div class="col-stat col-md-6 col-sm-6 col-12">
      <div class="dash-stat-card">
        <div class="dash-stat-icon dash-icon-orange">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="dash-stat-number"><?= $total_in_process ?></div>
        <div class="dash-stat-label">In Process</div>
        <div class="dash-stat-growth growth-up">
          ↑ <?= $today_process ?> new today
        </div>
      </div>
    </div>

    <div class="col-stat col-md-6 col-sm-6 col-12">
      <div class="dash-stat-card">
        <div class="dash-stat-icon dash-icon-green">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="dash-stat-number"><?= $total_completed ?></div>
        <div class="dash-stat-label">Completed</div>
        <div class="dash-stat-growth <?= $growth_completed >= 0 ? 'growth-up' : 'growth-down' ?>">
          <?= $growth_completed >= 0 ? '↑' : '↓' ?> <?= abs($growth_completed) ?>% vs last month
        </div>
      </div>
    </div>

    <div class="col-stat col-md-6 col-sm-6 col-12">
      <div class="dash-stat-card">
        <div class="dash-stat-icon dash-icon-yellow">
          <i class="fas fa-wallet"></i>
        </div>
        <div class="dash-stat-number"><?= $total_dp ?></div>
        <div class="dash-stat-label">Down Payment (DP)</div>
        <div class="dash-stat-growth growth-up">
          Pembayaran sebagian
        </div>
      </div>
    </div>

    <div class="col-stat col-md-6 col-sm-6 col-12">
      <div class="dash-stat-card">
        <div class="dash-stat-icon dash-icon-purple">
          <i class="fas fa-credit-card"></i>
        </div>
        <div class="dash-stat-number"><?= $total_paid ?></div>
        <div class="dash-stat-label">Paid Applications</div>
        <div class="dash-stat-growth growth-up">
          <?= $payment_rate ?>% payment rate
        </div>
      </div>
    </div>

  </div>

  <!-- MAIN CONTENT ROW -->
  <div class="row">

    <!-- Recent Applications -->
    <div class="col-lg-7 col-md-12">
      <div class="dash-card">
        <div class="dash-card-header">
          <span class="dash-card-title">Recent Applications</span>
          <a href="../applications/index.php" class="dash-view-all">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="dash-card-body p-0">
          <table class="dash-table">
            <thead>
              <tr>
                <th>APPLICANT</th>
                <th>COUNTRY</th>
                <th>STATUS</th>
                <th>PAYMENT</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($recent)): ?>
              <tr>
                <td>
                  <div class="dash-applicant">
                    <div class="dash-avatar" style="background:<?= '#' . substr(md5($row['applicant_name'] ?? 'X'), 0, 6) ?>;">
                      <?= strtoupper(substr($row['applicant_name'] ?? 'X', 0, 2)) ?>
                    </div>
                    <div>
                      <div class="dash-name"><?= htmlspecialchars($row['applicant_name'] ?? '-') ?></div>
                      <div class="dash-phone"><?= htmlspecialchars($row['phone'] ?? '') ?></div>
                    </div>
                  </div>
                </td>
                <td class="dash-country"><?= htmlspecialchars($row['country']) ?></td>
                <td>
                  <?php if ($row['status'] === 'in_process'): ?>
                    <span class="dash-badge badge-orange">● In Process</span>
                  <?php elseif ($row['status'] === 'completed'): ?>
                    <span class="dash-badge badge-green">● Completed</span>
                  <?php else: ?>
                    <span class="dash-badge badge-red">● Rejected</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $ps = $row['pay_status'] ?? 'unpaid';
                    if ($ps === 'paid'): ?>
                    <span class="dash-badge badge-green">● Paid</span>
                  <?php elseif ($ps === 'dp'): ?>
                    <span class="dash-badge badge-orange">● DP</span>
                  <?php else: ?>
                    <span class="dash-badge badge-red">● Unpaid</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Top Destinations -->
    <div class="col-lg-5 col-md-12">
      <div class="dash-card">
        <div class="dash-card-header">
          <span class="dash-card-title">Top Destinations</span>
        </div>
        <div class="dash-card-body">
          <?php foreach ($destinations as $dest): ?>
          <?php $pct = round(($dest['total'] / $max_dest) * 100); ?>
          <div class="dash-dest-row">
            <div class="dash-dest-avatar">
              <?= strtoupper(substr($dest['country'], 0, 2)) ?>
            </div>
            <div class="dash-dest-info">
              <div class="dash-dest-name"><?= htmlspecialchars($dest['country']) ?></div>
              <div class="dash-dest-bar-wrap">
                <div class="dash-dest-bar" style="width:<?= $pct ?>%;"></div>
              </div>
            </div>
            <div class="dash-dest-count"><?= $dest['total'] ?></div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($destinations)): ?>
          <div class="text-center text-muted py-4">Belum ada data</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>