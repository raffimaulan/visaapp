<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../layout/_top.php';
?>

<section class="section">
  <div class="section-header">
    <h1>Tambah Pemohon</h1>
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

        <form method="POST" action="store.php">
          <?= csrf_field() ?>

          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control"
                   placeholder="Masukkan nama lengkap pemohon" required>
          </div>

          <div class="form-group">
            <label>No. HP / WhatsApp</label>
            <input type="text" name="phone" class="form-control"
                   placeholder="Contoh: 08123456789" required>
          </div>

          <div class="form-group">
            <label>No. Passport <small class="text-muted">(opsional)</small></label>
            <input type="text" name="passport_number" class="form-control"
                   placeholder="Contoh: A1234567">
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>