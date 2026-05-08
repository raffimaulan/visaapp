<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Application.php';

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

$applicationModel = new Application($connection);
$result = $applicationModel->delete($id);

if ($result) {
    $_SESSION['success'] = 'Pengajuan berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus pengajuan.';
}

header('Location: index.php');
exit;
