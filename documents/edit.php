<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../layout/_top.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$docModel = new Document($connection);
$doc      = $docModel->getById($id);

if (!$doc) {
    $_SESSION['error'] = 'Dokumen tidak ditemukan.';
    header('Location: index.php');
    exit;
}

// Ambil info pengajuan terkait
$application = mysqli_fetch_assoc(mysqli_query($connection,
    "SELECT a.*, ap.name AS applicant_name
     FROM applications a
     LEFT JOIN applicants ap ON a.applicant_id = ap.id
     WHERE a.id = {$doc['application_id']} LIMIT 1"
));
?>

<section class="section">
  <div class="section-header">
    <h1>Edit Dokumen</h1>
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

        <!-- Info dokumen saat ini -->
        <div class="alert alert-info">
          <strong>Pengajuan:</strong>
          <?= htmlspecialchars($application['applicant_name'] ?? '-') ?> —
          <?= htmlspecialchars($application['country'] ?? '') ?>
          (<?= htmlspecialchars($application['visa_type'] ?? '') ?>)
          <br>
          <strong>File saat ini:</strong>
          <a href="../<?= htmlspecialchars($doc['file_path']) ?>" target="_blank">
            <?= htmlspecialchars(basename($doc['file_path'])) ?>
          </a>
          <span class="text-muted ml-2">(diupload <?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?>)</span>
        </div>

        <form method="POST" action="update.php" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $doc['id'] ?>">

          <div class="form-group">
            <label>Ganti File Dokumen</label>
            <input type="file" name="document" class="form-control-file"
                   accept=".jpg,.jpeg,.png,.pdf" required>
            <small class="text-muted">Format: JPG, PNG, PDF. Maks 2MB. File lama akan dihapus otomatis.</small>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan File Baru
          </button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>