<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../layout/_top.php';

$docModel = new Document($connection);
$appModel = new Application($connection);

$application_id = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;
$application    = $application_id ? $appModel->getById($application_id) : null;

if ($application_id) {
    $result = mysqli_query($connection,
        "SELECT d.*, a.country, a.visa_type, ap.name AS applicant_name, ap.email AS applicant_email
         FROM documents d
         LEFT JOIN applications a ON d.application_id = a.id
         LEFT JOIN applicants ap ON a.applicant_id = ap.id
         WHERE d.application_id = $application_id
         ORDER BY d.created_at ASC"
    );
} else {
    $result = mysqli_query($connection,
        "SELECT d.*, a.country, a.visa_type, ap.name AS applicant_name, ap.email AS applicant_email
         FROM documents d
         LEFT JOIN applications a ON d.application_id = a.id
         LEFT JOIN applicants ap ON a.applicant_id = ap.id
         ORDER BY d.application_id ASC, d.created_at ASC"
    );
}

$grouped = [];
while ($row = mysqli_fetch_assoc($result)) {
    $aid = $row['application_id'];
    if (!isset($grouped[$aid])) {
        $grouped[$aid] = [
            'application_id'  => $aid,
            'applicant_name'  => $row['applicant_name'],
            'applicant_email' => $row['applicant_email'] ?? '',
            'country'         => $row['country'],
            'visa_type'       => $row['visa_type'],
            'latest_upload'   => $row['created_at'],
            'files'           => [],
        ];
    }
    $grouped[$aid]['files'][] = [
        'id'         => $row['id'],
        'file_path'  => $row['file_path'],
        'created_at' => $row['created_at'],
    ];
    if ($row['created_at'] > $grouped[$aid]['latest_upload']) {
        $grouped[$aid]['latest_upload'] = $row['created_at'];
    }
}

usort($grouped, function ($a, $b) {
    return strcmp($b['latest_upload'], $a['latest_upload']);
});

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
function visaBadgeClass($type) {
    $map = ['Tourist'=>'tourist','Student'=>'student','Business'=>'business','Work'=>'work','Transit'=>'transit'];
    return $map[$type] ?? 'default';
}
function fileChipInfo($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext === 'pdf') return ['class'=>'pdf', 'icon'=>'fas fa-file-pdf'];
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) return ['class'=>'img', 'icon'=>'fas fa-file-image'];
    return ['class'=>'other', 'icon'=>'fas fa-file'];
}
function docStatus($fileCount) {
    if ($fileCount >= 3) return ['label'=>'Lengkap',  'class'=>'lengkap'];
    if ($fileCount >= 1) return ['label'=>'Pending',  'class'=>'pending'];
    return                       ['label'=>'Kurang',   'class'=>'kurang'];
}
?>

<link rel="stylesheet" href="../assets/css/filter-bar.css">
<link rel="stylesheet" href="../assets/css/document-index.css">
<link rel="stylesheet" href="../assets/css/bulk-select.css">

<section class="section">
  <div class="section-header">
    <h1>Data Dokumen</h1>
    <div class="section-header-button">
      <a href="create.php" class="btn btn-primary">+ Upload Dokumen</a>
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

        <?php if ($application): ?>
          <div class="alert alert-info">
            Menampilkan dokumen untuk: <strong><?= htmlspecialchars($application['applicant_name']) ?></strong>
            — <?= htmlspecialchars($application['country']) ?> (<?= htmlspecialchars($application['visa_type']) ?>)
            <a href="index.php" class="ml-2">Lihat Semua</a>
          </div>
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

        <div class="filter-bar">
          <div class="filter-search">
            <i class="fas fa-search filter-search-icon"></i>
            <input type="text" id="searchInput" class="filter-search-input" placeholder="Cari pemohon...">
          </div>
          <select id="filterVisa" class="filter-select">
            <option value="">All Visa Types</option>
            <option value="Tourist">Tourist Visa</option>
            <option value="Business">Business Visa</option>
            <option value="Student">Student Visa</option>
            <option value="Work">Work Visa</option>
            <option value="Transit">Transit Visa</option>
          </select>
        </div>

        <form id="formBulkDelete" action="delete_bulk.php" method="POST" style="display:none">
          <div id="bulkInputs"></div>
        </form>

        <div class="table-responsive">
          <table class="table" id="table-documents">
            <thead>
              <tr>
                <th class="cb-col"><input type="checkbox" id="cb-all" title="Pilih Semua"></th>
                <th>No</th>
                <th>Pemohon</th>
                <th>Pengajuan</th>
                <th>Dokumen</th>
                <th>Tanggal Upload</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; foreach ($grouped as $gid => $group):
                $fileCount  = count($group['files']);
                $status     = docStatus($fileCount);
                $initials   = avatarInitials($group['applicant_name'] ?? 'U');
                $bgColor    = avatarColor($group['applicant_name'] ?? '');
                $visaClass  = visaBadgeClass($group['visa_type'] ?? '');
                $showMax    = 2;
                // Use all file IDs for bulk delete (delete all files of this group)
                $allFileIds = implode(',', array_column($group['files'], 'id'));
              ?>
              <tr>
                <td class="cb-row">
                  <input type="checkbox" class="row-cb" value="<?= $allFileIds ?>">
                </td>
                <td><?= $no++ ?></td>
                <td>
                  <div class="doc-applicant">
                    <div class="doc-avatar" style="background:<?= $bgColor ?>"><?= htmlspecialchars($initials) ?></div>
                    <div>
                      <div class="doc-applicant-name"><?= htmlspecialchars($group['applicant_name'] ?? '-') ?></div>
                      <?php if ($group['applicant_email']): ?>
                        <div class="doc-applicant-email"><?= htmlspecialchars($group['applicant_email']) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td data-visa="<?= htmlspecialchars($group['visa_type'] ?? '') ?>">
                  <div class="doc-pengajuan-country">
                    <?= htmlspecialchars(($group['country'] ?? '') . ' - ' . ($group['visa_type'] ?? '')) ?>
                  </div>
                  <div>
                    <span class="badge-visa <?= $visaClass ?>">
                      <i class="fas fa-suitcase"></i>
                      <?= htmlspecialchars($group['visa_type'] ?? '') ?>
                    </span>
                  </div>
                </td>
                <td>
                  <div class="doc-files">
                    <?php foreach ($group['files'] as $idx => $f):
                      $info   = fileChipInfo($f['file_path']);
                      $fname  = basename($f['file_path']);
                      $hidden = $idx >= $showMax ? 'hidden-chip' : '';
                      $style  = $idx >= $showMax ? 'style="display:none"' : '';
                    ?>
                      <a href="../<?= htmlspecialchars($f['file_path']) ?>"
                         target="_blank"
                         class="file-chip <?= $info['class'] ?> <?= $hidden ?>"
                         <?= $style ?>
                         title="<?= htmlspecialchars($fname) ?>">
                        <i class="<?= $info['icon'] ?>"></i>
                        <?= htmlspecialchars(strlen($fname) > 16 ? substr($fname, 0, 14) . '…' : $fname) ?>
                      </a>
                    <?php endforeach; ?>
                    <?php if ($fileCount > $showMax): ?>
                      <button type="button" class="file-chip-more" title="Tampilkan semua file">
                        +<?= $fileCount - $showMax ?>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <div class="doc-date">
                    <span><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($group['latest_upload'])) ?></span>
                    <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($group['latest_upload'])) ?></span>
                  </div>
                </td>
                <td>
                  <span class="status-badge <?= $status['class'] ?>"><?= $status['label'] ?></span>
                </td>
                <td>
                  <div class="aksi-wrap">
                    <a href="create.php?application_id=<?= $group['application_id'] ?>" class="btn-upload">
                      <i class="fas fa-plus"></i> Upload
                    </a>
                    <button type="button" class="btn-more" title="Opsi lainnya">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="aksi-dropdown">
                      <a href="index.php?application_id=<?= $group['application_id'] ?>">
                        <i class="fas fa-eye"></i> Lihat Detail
                      </a>
                      <?php foreach ($group['files'] as $idx => $f): ?>
                        <a href="edit.php?id=<?= $f['id'] ?>">
                          <i class="fas fa-edit"></i> Edit File <?= $idx + 1 ?>
                        </a>
                      <?php endforeach; ?>
                      <div class="divider"></div>
                      <?php foreach ($group['files'] as $idx => $f): ?>
                        <a href="delete.php?id=<?= $f['id'] ?>" class="text-danger"
                           onclick="return confirm('Yakin hapus File <?= $idx + 1 ?>?')">
                          <i class="fas fa-trash"></i> Hapus File <?= $idx + 1 ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layout/_bottom.php'; ?>
