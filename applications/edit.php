<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../layout/_top.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: index.php");
    exit;
}

$applicationModel = new Application($connection);
$app = $applicationModel->getById($id);

if (!$app) {
    $_SESSION['error'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$applicants_raw = mysqli_query($connection, "SELECT id, name, phone, passport_number FROM applicants ORDER BY name ASC");
$applicants_data = [];
while ($a = mysqli_fetch_assoc($applicants_raw)) {
    $applicants_data[] = $a;
}
?>

<section class="section">
  <div class="section-header">
    <h1>Edit Pengajuan Visa</h1>
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

        <form method="POST" action="update.php">
          <input type="hidden" name="id" value="<?= $app['id'] ?>">

          <div class="form-group">
            <label>Pemohon</label>
            <select name="applicant_id" class="form-control" id="applicantSelect" required>
              <option value="">-- Pilih Pemohon --</option>
              <?php foreach ($applicants_data as $a): ?>
                <option value="<?= $a['id'] ?>"
                        data-passport="<?= htmlspecialchars($a['passport_number'] ?? '') ?>"
                        <?= $a['id'] == $app['applicant_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($a['name']) ?> - <?= htmlspecialchars($a['phone']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>No. Passport</label>
            <input type="text" id="passportDisplay" class="form-control"
                   value="<?= htmlspecialchars($app['passport_number'] ?? 'Tidak ada data passport') ?>"
                   placeholder="Otomatis terisi saat memilih pemohon" readonly
                   style="background:#f8f9fa; cursor:default;">
          </div>

          <div class="form-group">
            <label>Negara Tujuan</label>
            <input type="text" name="country" class="form-control"
                   value="<?= htmlspecialchars($app['country']) ?>" required>
          </div>

          <div class="form-group">
            <label>Jenis Visa</label>
            <select name="visa_type" class="form-control" required>
              <?php foreach (['Tourist','Business','Student','Work','Transit'] as $type): ?>
                <option value="<?= $type ?>" <?= $app['visa_type'] === $type ? 'selected' : '' ?>>
                  <?= $type ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="in_process"  <?= $app['status'] === 'in_process'  ? 'selected' : '' ?>>Diproses</option>
              <option value="completed"   <?= $app['status'] === 'completed'   ? 'selected' : '' ?>>Selesai</option>
              <option value="rejected"    <?= $app['status'] === 'rejected'    ? 'selected' : '' ?>>Ditolak</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<script>
document.getElementById('applicantSelect').addEventListener('change', function () {
  var opt = this.options[this.selectedIndex];
  var passport = opt.getAttribute('data-passport') || '';
  document.getElementById('passportDisplay').value = passport || 'Tidak ada data passport';
});
</script>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>