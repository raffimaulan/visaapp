<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Applicant.php';
require_once __DIR__ . '/../layout/_top.php';

$applicantModel = new Applicant($connection);
$applicants     = $applicantModel->getAll();

function avatarInitials($name) {
    $parts = explode(' ', trim($name));
    $init  = strtoupper(substr($parts[0], 0, 1));
    if (isset($parts[1])) $init .= strtoupper(substr($parts[1], 0, 1));
    return $init;
}
$avatarColors = ['#6777ef','#f59e0b','#10b981','#ef4444','#8b5cf6','#3b82f6','#ec4899','#14b8a6'];
function avatarColor($name) {
    global $avatarColors;
    return $avatarColors[abs(crc32($name)) % count($avatarColors)];
}
?>

<link rel="stylesheet" href="../assets/css/filter-bar.css">
<link rel="stylesheet" href="../assets/css/applicant-index.css">
<link rel="stylesheet" href="../assets/css/bulk-select.css">

<section class="section">
  <div class="section-header">
    <h1>Data Pemohon</h1>
    <div class="section-header-button">
      <a href="create.php" class="btn btn-primary">+ Tambah Pemohon</a>
    </div>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- BULK TOOLBAR -->
        <div class="bulk-toolbar" id="bulkToolbar">
          <span class="bulk-count" id="bulkCount">0 item dipilih</span>
          <button type="button" class="btn-bulk-cancel" id="btnBulkCancel">
            <i class="fas fa-times"></i> Batal
          </button>
          <button type="button" class="btn-bulk-delete" id="btnBulkDelete">
            <i class="fas fa-trash"></i> Hapus Terpilih
          </button>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
          <div class="filter-search">
            <i class="fas fa-search filter-search-icon"></i>
            <input type="text" id="searchInput" class="filter-search-input" placeholder="Cari pemohon...">
          </div>
        </div>

        <form id="formBulkDelete" action="delete_bulk.php" method="POST" style="display:none">
          <?= csrf_field() ?>
          <div id="bulkInputs"></div>
        </form>

        <div class="table-responsive">
          <table class="table" id="table-applicants">
            <thead>
              <tr>
                <th class="cb-col"><input type="checkbox" id="cb-all" title="Pilih Semua"></th>
                <th>No</th>
                <th>Pemohon</th>
                <th>No. Passport</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; while ($row = mysqli_fetch_assoc($applicants)):
                $initials = avatarInitials($row['name'] ?? 'U');
                $bgColor  = avatarColor($row['name'] ?? '');
              ?>
              <tr>
                <td class="cb-row"><input type="checkbox" class="row-cb" value="<?= $row['id'] ?>"></td>
                <td><?= $no++ ?></td>
                <td>
                  <div class="apl-applicant">
                    <div class="apl-avatar" style="background:<?= $bgColor ?>"><?= htmlspecialchars($initials) ?></div>
                    <div>
                      <div class="apl-name"><?= htmlspecialchars($row['name'] ?? '-') ?></div>
                      <div class="apl-sub">
                        <i class="fas fa-phone" style="font-size:10px"></i>
                        <?= htmlspecialchars($row['phone'] ?? '-') ?>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="apl-info">
                    <?= htmlspecialchars($row['passport_number'] ?? '-') ?>
                    <span><i class="fas fa-passport"></i> No. Passport</span>
                  </div>
                </td>
                <td>
                  <div class="apl-date">
                    <span><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['created_at'])) ?></span>
                    <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($row['created_at'])) ?></span>
                  </div>
                </td>
                <td>
                  <div class="aksi-wrap">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-aksi">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn-more" title="Opsi lainnya">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="aksi-dropdown">
                      <a href="../applications/index.php?applicant_id=<?= $row['id'] ?>">
                        <i class="fas fa-file-alt"></i> Lihat Pengajuan
                      </a>
                      <a href="../documents/index.php?applicant_id=<?= $row['id'] ?>">
                        <i class="fas fa-folder-open"></i> Lihat Dokumen
                      </a>
                      <div class="divider"></div>
                      <form method="POST" action="delete.php" style="display:inline" onsubmit="return confirm('Yakin hapus pemohon ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>"><button type="submit" class="btn-link text-danger" style="background:none;border:none;padding:0;cursor:pointer"><i class="fas fa-trash"></i> Hapus</button></form>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>
