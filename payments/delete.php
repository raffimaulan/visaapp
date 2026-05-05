<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Payment.php';

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    header("Location: index.php");
    exit;
}

$paymentModel = new Payment($connection);

// Ambil data dulu untuk hapus file bukti jika ada
$payment = $paymentModel->getById($id);
if ($payment && !empty($payment['proof'])) {
    $filePath = __DIR__ . '/../uploads/proofs/' . $payment['proof'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$result = $paymentModel->delete($id);

if ($result) {
    $_SESSION['success'] = "Data pembayaran berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus data pembayaran.";
}

header("Location: index.php");
exit;