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
    $row = mysqli_fetch_assoc(mysqli_query($connection,
        "SELECT file_path FROM documents WHERE id = $id"
    ));
    $res = mysqli_query($connection, "DELETE FROM documents WHERE id = $id");
    if ($res && mysqli_affected_rows($connection) > 0) {
        $deleted++;
        if (!empty($row['file_path'])) {
            $path = __DIR__ . '/../' . $row['file_path'];
            if (file_exists($path)) unlink($path);
        }
    }
}

$_SESSION['success'] = "$deleted dokumen berhasil dihapus.";
header('Location: index.php');
exit;
