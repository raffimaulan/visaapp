<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Applicant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrf_verify();

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

$proofs = mysqli_query($connection,
    "SELECT p.proof, p.proof_pelunasan FROM payments p
     INNER JOIN applications a ON p.application_id = a.id
     WHERE a.applicant_id = $id AND (p.proof IS NOT NULL OR p.proof_pelunasan IS NOT NULL)"
);

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

$applicantModel = new Applicant($connection);
$result = $applicantModel->delete($id);

if ($result) {
    while ($row = mysqli_fetch_assoc($proofs)) {
        foreach (['proof', 'proof_pelunasan'] as $col) {
            if (!empty($row[$col])) {
                $path = __DIR__ . '/../uploads/proofs/' . basename($row[$col]);
                if (file_exists($path)) unlink($path);
            }
        }
    }
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
