<?php
session_start();
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Document.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$docModel  = new Document($connection);
$file_path = $docModel->delete($id); // Model mengembalikan file_path atau false

if ($file_path !== false) {
    // Hapus file fisik dari server jika ada
    if ($file_path) {
        $full_path = __DIR__ . '/../' . $file_path;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
    $_SESSION['success'] = 'Dokumen berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus dokumen. Data tidak ditemukan.';
}

header('Location: index.php');
exit;