<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../layout/_top.php';

// Ambil semua applicant untuk dropdown (sertakan passport_number)
$applicants = mysqli_query($connection, "SELECT id, name, phone, passport_number FROM applicants ORDER BY name ASC");

// Simpan ke array untuk dipakai di JS
$applicants_data = [];
while ($a = mysqli_fetch_assoc($applicants)) {
    $applicants_data[] = $a;
}
?>

<section class="section">
  <div class="section-header">
    <h1>Tambah Pengajuan Visa</h1>
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
            <label>Pemohon</label>
            <select name="applicant_id" class="form-control" id="applicantSelect" required>
              <option value="">-- Pilih Pemohon --</option>
              <?php foreach ($applicants_data as $a): ?>
                <option value="<?= $a['id'] ?>"
                        data-passport="<?= htmlspecialchars($a['passport_number'] ?? '') ?>">
                  <?= htmlspecialchars($a['name']) ?> - <?= htmlspecialchars($a['phone']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small>Pemohon belum ada? <a href="../applicants/create.php">Tambah pemohon baru</a></small>
          </div>

          <div class="form-group">
            <label>No. Passport</label>
            <input type="text" id="passportDisplay" class="form-control"
                   placeholder="Otomatis terisi saat memilih pemohon" readonly
                   style="background:#f8f9fa; cursor:default;">
          </div>

          <div class="form-group">
            <label>Negara Tujuan</label>
            <input type="text" name="country" class="form-control" placeholder="Contoh: Jepang" required>
          </div>

          <div class="form-group">
            <label>Jenis Visa</label>
            <select name="visa_type" class="form-control" required>
              <option value="">-- Pilih Jenis Visa --</option>
              <option value="Tourist">Tourist</option>
              <option value="Business">Business</option>
              <option value="Student">Student</option>
              <option value="Work">Work</option>
              <option value="Transit">Transit</option>
            </select>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="in_process">Diproses</option>
              <option value="completed">Selesai</option>
              <option value="rejected">Ditolak</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
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
  var display = document.getElementById('passportDisplay');
  display.value = passport || 'Tidak ada data passport';
});
</script>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>