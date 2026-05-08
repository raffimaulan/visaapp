<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';

csrf_verify();

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
        "SELECT proof FROM payments WHERE id = $id"
    ));
    $res = mysqli_query($connection, "DELETE FROM payments WHERE id = $id");
    if ($res && mysqli_affected_rows($connection) > 0) {
        $deleted++;
        if (!empty($row['proof'])) {
            $path = __DIR__ . '/../uploads/proofs/' . $row['proof'];
            if (file_exists($path)) unlink($path);
        }
    }
}

$_SESSION['success'] = "$deleted data pembayaran berhasil dihapus.";
header('Location: index.php');
exit;
