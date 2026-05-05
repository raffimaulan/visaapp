<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Applicant.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Ambil semua file fisik yang perlu dihapus sebelum hapus record database
// 1. Bukti bayar dari payments
$proofs = mysqli_query($connection,
    "SELECT p.proof FROM payments p
     INNER JOIN applications a ON p.application_id = a.id
     WHERE a.applicant_id = $id AND p.proof IS NOT NULL"
);

// 2. File dokumen dari documents
$docs = mysqli_query($connection,
    "SELECT d.file_path FROM documents d
     INNER JOIN applications a ON d.application_id = a.id
     WHERE a.applicant_id = $id"
);

// Hapus semua record terkait secara berurutan (payments → documents → applications → applicant)
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

$applicantModel = new Applicant($connection);
$result = $applicantModel->delete($id);

if ($result) {
    // Hapus file fisik bukti bayar
    while ($row = mysqli_fetch_assoc($proofs)) {
        if ($row['proof']) {
            $path = __DIR__ . '/../uploads/proofs/' . $row['proof'];
            if (file_exists($path)) unlink($path);
        }
    }

    // Hapus file fisik dokumen
    while ($row = mysqli_fetch_assoc($docs)) {
        if ($row['file_path']) {
            $path = __DIR__ . '/../' . $row['file_path'];
            if (file_exists($path)) unlink($path);
        }
    }

    $_SESSION['success'] = 'Pemohon beserta seluruh data terkait berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus pemohon.';
}

header('Location: index.php');
exit;