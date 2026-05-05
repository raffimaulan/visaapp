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

$deleted = 0;

foreach ($ids as $id) {
    $proofs = mysqli_query($connection,
        "SELECT proof FROM payments WHERE application_id = $id AND proof IS NOT NULL"
    );
    $docs = mysqli_query($connection,
        "SELECT file_path FROM documents WHERE application_id = $id"
    );

    mysqli_query($connection, "DELETE FROM payments WHERE application_id = $id");
    mysqli_query($connection, "DELETE FROM documents WHERE application_id = $id");
    $res = mysqli_query($connection, "DELETE FROM applications WHERE id = $id");

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

$_SESSION['success'] = "$deleted pengajuan berhasil dihapus.";
header('Location: index.php');
exit;
