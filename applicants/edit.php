<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Applicant.php';
require_once __DIR__ . '/../layout/_top.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: index.php");
    exit;
}

$applicantModel = new Applicant($connection);
$applicant = $applicantModel->getById($id);

if (!$applicant) {
    $_SESSION['error'] = "Data pemohon tidak ditemukan.";
    header("Location: index.php");
    exit;
}
?>

<section class="section">
  <div class="section-header">
    <h1>Edit Pemohon</h1>
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
          <input type="hidden" name="id" value="<?= $applicant['id'] ?>">

          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($applicant['name']) ?>" required>
          </div>

          <div class="form-group">
            <label>No. HP / WhatsApp</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($applicant['phone']) ?>" required>
          </div>

          <div class="form-group">
            <label>No. Passport <small class="text-muted">(opsional)</small></label>
            <input type="text" name="passport_number" class="form-control"
                   value="<?= htmlspecialchars($applicant['passport_number'] ?? '') ?>"
                   placeholder="Contoh: A1234567">
          </div>

          <button type="submit" class="btn btn-primary">Update</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>