<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Application.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

csrf_verify();

$id           = (int) ($_POST['id'] ?? 0);
$applicant_id = $_POST['applicant_id'] ?? '';
$country      = trim($_POST['country'] ?? '');
$visa_type    = $_POST['visa_type'] ?? '';
$status       = $_POST['status'] ?? 'in_process';

if (!$id || !$applicant_id || !$country || !$visa_type) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: edit.php?id=$id");
    exit;
}

$applicationModel = new Application($connection);

// Update applicant_id juga lewat query langsung
$applicant_id = (int) $applicant_id;
$country      = mysqli_real_escape_string($connection, $country);
$visa_type    = mysqli_real_escape_string($connection, $visa_type);
$status       = mysqli_real_escape_string($connection, $status);

$query = "UPDATE applications SET applicant_id=$applicant_id, country='$country', visa_type='$visa_type', status='$status' WHERE id=$id";
$result = mysqli_query($connection, $query);

if ($result) {
    $_SESSION['success'] = "Pengajuan berhasil diupdate.";
} else {
    $_SESSION['error'] = "Gagal mengupdate pengajuan.";
}

header("Location: index.php");
exit;