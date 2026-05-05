<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Application.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$applicant_id = $_POST['applicant_id'] ?? '';
$country      = trim($_POST['country'] ?? '');
$visa_type    = $_POST['visa_type'] ?? '';
$status       = $_POST['status'] ?? 'in_process';

if (!$applicant_id || !$country || !$visa_type) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: create.php");
    exit;
}

$applicationModel = new Application($connection);
$id = $applicationModel->create($applicant_id, $country, $visa_type, $status);

if ($id) {
    $_SESSION['success'] = "Pengajuan berhasil ditambahkan.";
} else {
    $_SESSION['error'] = "Gagal menambahkan pengajuan.";
}

header("Location: index.php");
exit;