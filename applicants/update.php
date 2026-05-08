<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Applicant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

csrf_verify();

$id              = (int) ($_POST['id'] ?? 0);
$name            = trim($_POST['name'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$passport_number = trim($_POST['passport_number'] ?? '');

if (!$id || !$name || !$phone) {
    $_SESSION['error'] = "Nama dan No. HP wajib diisi.";
    header("Location: edit.php?id=$id");
    exit;
}

$applicantModel = new Applicant($connection);
$result = $applicantModel->update($id, $name, $phone, $passport_number);

if ($result) {
    $_SESSION['success'] = "Data pemohon berhasil diupdate.";
} else {
    $_SESSION['error'] = "Gagal mengupdate data pemohon.";
}

header("Location: index.php");
exit;