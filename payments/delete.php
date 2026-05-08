<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Payment.php';

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

$paymentModel = new Payment($connection);

$payment = $paymentModel->getById($id);
if ($payment) {
    foreach (['proof', 'proof_pelunasan'] as $col) {
        if (!empty($payment[$col])) {
            $filePath = __DIR__ . '/../uploads/proofs/' . basename($payment[$col]);
            if (file_exists($filePath)) unlink($filePath);
        }
    }
}

$result = $paymentModel->delete($id);

if ($result) {
    $_SESSION['success'] = 'Data pembayaran berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus data pembayaran.';
}

header('Location: index.php');
exit;
