<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';

$ids = $_POST['ids'] ?? [];
$ids = array_filter(array_map('intval', (array)$ids));

if (empty($ids)) {
    $_SESSION['error'] = 'Tidak ada data yang dipilih.';
    header('Location: index.php');
    exit;
}

$idList = implode(',', $ids);
$deleted = 0;

foreach ($ids as $id) {
    // Ambil file fisik bukti bayar
    $proofs = mysqli_query($connection,
        "SELECT p.proof FROM payments p
         INNER JOIN applications a ON p.application_id = a.id
         WHERE a.applicant_id = $id AND p.proof IS NOT NULL"
    );
    // Ambil file dokumen
    $docs = mysqli_query($connection,
        "SELECT d.file_path FROM documents d
         INNER JOIN applications a ON d.application_id = a.id
         WHERE a.applicant_id = $id"
    );

    mysqli_query($connection,
        "DELETE p FROM payments p
         INNER JOIN applications a ON p.application_id = a.id
         WHERE a.applicant_id = $id"
    );
    mysqli_query($connection,
        "DELETE d FROM documents d
         INNER JOIN applications a ON d.application_id = a.id
         WHERE a.applicant_id = $id"
    );
    mysqli_query($connection, "DELETE FROM applications WHERE applicant_id = $id");
    $res = mysqli_query($connection, "DELETE FROM applicants WHERE id = $id");

    if ($res && mysqli_affected_rows($connection) > 0) {
        $deleted++;
        while ($row = mysqli_fetch_assoc($proofs)) {
            if ($row['proof']) {
                $path = __DIR__ . '/../uploads/proofs/' . $row['proof'];
                if (file_exists($path)) unlink($path);
            }
        }
        while ($row = mysqli_fetch_assoc($docs)) {
            if ($row['file_path']) {
                $path = __DIR__ . '/../' . $row['file_path'];
                if (file_exists($path)) unlink($path);
            }
        }
    }
}

$_SESSION['success'] = "$deleted pemohon berhasil dihapus.";
header('Location: index.php');
exit;
