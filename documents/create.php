<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../layout/_top.php';

$applications = mysqli_query($connection,
    "SELECT a.id, a.country, a.visa_type, ap.name AS applicant_name
     FROM applications a
     LEFT JOIN applicants ap ON a.applicant_id = ap.id
     ORDER BY ap.name ASC"
);

$selected_id = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;
?>

<link rel="stylesheet" href="../assets/css/document.css">

<section class="section">
  <div class="section-header">
    <h1>Upload Dokumen</h1>
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

          <div class="form-group">
            <label>Pengajuan Visa</label>
            <select name="application_id" class="form-control" required>
              <option value="">-- Pilih Pengajuan --</option>
              <?php while ($a = mysqli_fetch_assoc($applications)): ?>
                <option value="<?= $a['id'] ?>" <?= ($a['id'] == $selected_id) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($a['applicant_name']) ?> —
                  <?= htmlspecialchars($a['country']) ?> (<?= htmlspecialchars($a['visa_type']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label>File Dokumen</label>

            <div class="upload-dropzone" id="dropzone">
              <input type="file" name="documents[]" id="fileInput"
                     accept=".jpg,.jpeg,.png,.pdf"
                     multiple hidden>
              <div class="upload-dropzone-inner" id="dropzoneInner">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <p class="upload-title">Klik atau seret file ke sini</p>
                <p class="upload-hint">JPG, PNG, PDF — Maks. 2MB per file</p>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="browseBtn">
                  <i class="fas fa-folder-open mr-1"></i> Pilih File
                </button>
              </div>
            </div>

            <div id="previewList" class="upload-preview-list"></div>
          </div>

          <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
            <i class="fas fa-upload mr-1"></i> Upload
          </button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<script src="../assets/js/document.js"></script>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>