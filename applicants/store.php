<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Applicant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$name            = trim($_POST['name'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$passport_number = trim($_POST['passport_number'] ?? '');

if (!$name || !$phone) {
    $_SESSION['error'] = "Nama dan No. HP wajib diisi.";
    header("Location: create.php");
    exit;
}

$applicantModel = new Applicant($connection);
$id = $applicantModel->create($name, $phone, $passport_number);

if ($id) {
    $_SESSION['success'] = "Pemohon berhasil ditambahkan.";
} else {
    $_SESSION['error'] = "Gagal menambahkan pemohon.";
}

header("Location: index.php");
exit;