<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../models/Payment.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id             = (int) ($_POST['id'] ?? 0);
$application_id = (int) ($_POST['application_id'] ?? 0);
$payment_type   = trim($_POST['payment_type'] ?? '');
$amount_total   = (float) ($_POST['amount_total'] ?? 0);
$dp_amount      = (float) ($_POST['dp_amount'] ?? 0);
$amount_paid    = (float) ($_POST['amount_paid'] ?? 0);
$status         = $_POST['status'] ?? 'unpaid';
$paid_at        = !empty($_POST['paid_at']) ? $_POST['paid_at'] : null;
$existing_proof = $_POST['existing_proof'] ?? null;

if (!$id || !$application_id || !$payment_type || $amount_total <= 0) {
    $_SESSION['error'] = "Pengajuan, jenis pembayaran, dan total biaya wajib diisi.";
    header("Location: edit.php?id=$id");
    exit;
}

// Handle upload bukti baru (opsional)
$proof = $existing_proof ?: null;
if (!empty($_FILES['proof']['name'])) {
    $allowed  = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext      = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
    $maxSize  = 2 * 1024 * 1024;

    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Format file tidak didukung. Gunakan jpg, png, atau pdf.";
        header("Location: edit.php?id=$id");
        exit;
    }
    if ($_FILES['proof']['size'] > $maxSize) {
        $_SESSION['error'] = "Ukuran file melebihi 2MB.";
        header("Location: edit.php?id=$id");
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/proofs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = 'proof_' . time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($_FILES['proof']['tmp_name'], $uploadDir . $filename)) {
        $_SESSION['error'] = "Gagal mengupload bukti pembayaran.";
        header("Location: edit.php?id=$id");
        exit;
    }

    // Hapus file lama jika ada
    if ($existing_proof && file_exists($uploadDir . $existing_proof)) {
        unlink($uploadDir . $existing_proof);
    }
    $proof = $filename;
}

// Auto-deteksi status berdasarkan jumlah yang dibayar
if ($amount_paid >= $amount_total && $amount_total > 0) {
    $status      = 'paid';
    $amount_paid = $amount_total;
} elseif ($amount_paid > 0) {
    $status = 'partial';
} else {
    $status = 'unpaid';
}

// Update application_id sekaligus via query langsung (tidak ada di model update)
$application_id_safe = (int) $application_id;
$payment_type_safe   = mysqli_real_escape_string($connection, $payment_type);
$status_safe         = mysqli_real_escape_string($connection, $status);
$proof_sql           = $proof   ? "'" . mysqli_real_escape_string($connection, $proof) . "'"   : "NULL";
$paid_at_sql         = $paid_at ? "'" . mysqli_real_escape_string($connection, $paid_at) . "'" : "NULL";

$query = "UPDATE payments
          SET application_id = $application_id_safe,
              payment_type   = '$payment_type_safe',
              amount_total   = $amount_total,
              dp_amount      = $dp_amount,
              amount_paid    = $amount_paid,
              status         = '$status_safe',
              proof          = $proof_sql,
              paid_at        = $paid_at_sql
          WHERE id = $id";

$result = mysqli_query($connection, $query);

if ($result) {
    $_SESSION['success'] = "Data pembayaran berhasil diupdate.";
} else {
    $_SESSION['error'] = "Gagal mengupdate data pembayaran.";
}

header("Location: index.php");
exit;