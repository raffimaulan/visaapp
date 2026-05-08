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

$application_id = (int)($_POST['application_id'] ?? 0);
$payment_id     = (int)($_POST['payment_id']     ?? 0);  // 0=INSERT, >0=UPDATE
$payment_type   = trim($_POST['payment_type']    ?? '');
$amount_total   = (float)($_POST['amount_total'] ?? 0);
$dp_amount      = (float)($_POST['dp_amount']    ?? 0);
$amount_paid    = (float)($_POST['amount_paid']  ?? 0);
$status         = trim($_POST['status']          ?? 'unpaid');
$paid_at        = !empty($_POST['paid_at']) ? $_POST['paid_at'] : null;

// Pastikan status valid
if (!in_array($status, ['unpaid', 'partial', 'paid'])) {
    $status = 'unpaid';
}

// Validasi wajib
if (!$application_id || !$payment_type || $amount_total <= 0) {
    $_SESSION['error'] = 'Pengajuan, jenis pembayaran, dan total biaya wajib diisi.';
    header('Location: create.php');
    exit;
}

// Validasi logika nominal
if ($dp_amount > $amount_total) {
    $_SESSION['error'] = 'Jumlah DP tidak boleh melebihi total biaya.';
    header('Location: create.php');
    exit;
}
if ($amount_paid > $amount_total) {
    $_SESSION['error'] = 'Jumlah dibayar tidak boleh melebihi total biaya.';
    header('Location: create.php');
    exit;
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

// ----------------------------------------------------------------
// Handle upload bukti pembayaran
// ----------------------------------------------------------------
$proof = null;
if (!empty($_FILES['proof']['name']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
    $allowed   = ['image/jpeg', 'image/png', 'application/pdf'];
    $ext_map   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
    $max_size  = 2 * 1024 * 1024;
    $file      = $_FILES['proof'];

    if ($file['size'] > $max_size) {
        $_SESSION['error'] = 'File bukti melebihi 2MB.';
        header('Location: create.php');
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        $_SESSION['error'] = 'Format bukti tidak didukung (JPG, PNG, PDF).';
        header('Location: create.php');
        exit;
    }

    $upload_dir = __DIR__ . '/../uploads/proofs/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $filename = 'proof_' . $application_id . '_' . time() . '_' . uniqid() . '.' . $ext_map[$mime];
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $_SESSION['error'] = 'Gagal menyimpan file bukti.';
        header('Location: create.php');
        exit;
    }
    $proof = $filename;
}

// ----------------------------------------------------------------
// Handle upload bukti pelunasan
// ----------------------------------------------------------------
$proof_pelunasan = null;
if (!empty($_FILES['proof_pelunasan']['name']) && $_FILES['proof_pelunasan']['error'] === UPLOAD_ERR_OK) {
    $allowed   = ['image/jpeg', 'image/png', 'application/pdf'];
    $ext_map   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
    $max_size  = 2 * 1024 * 1024;
    $file      = $_FILES['proof_pelunasan'];

    if ($file['size'] > $max_size) {
        $_SESSION['error'] = 'File bukti pelunasan melebihi 2MB.';
        header('Location: create.php');
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        $_SESSION['error'] = 'Format bukti pelunasan tidak didukung (JPG, PNG, PDF).';
        header('Location: create.php');
        exit;
    }

    $upload_dir = __DIR__ . '/../uploads/proofs/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $filename = 'pelunasan_' . $application_id . '_' . time() . '_' . uniqid() . '.' . $ext_map[$mime];
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $_SESSION['error'] = 'Gagal menyimpan file bukti pelunasan.';
        header('Location: create.php');
        exit;
    }
    $proof_pelunasan = $filename;
}

$paymentModel = new Payment($connection);

// ----------------------------------------------------------------
// CABANG: UPDATE existing  vs  INSERT baru
// ----------------------------------------------------------------
if ($payment_id > 0) {

    // Verifikasi payment_id benar dan milik application ini
    $existing = mysqli_fetch_assoc(mysqli_query($connection,
        "SELECT id, proof, proof_pelunasan, amount_paid AS old_paid FROM payments WHERE id = $payment_id AND application_id = $application_id LIMIT 1"
    ));

    if (!$existing) {
        $_SESSION['error'] = 'Data pembayaran tidak ditemukan atau tidak cocok.';
        header('Location: create.php');
        exit;
    }

    // Pakai bukti lama jika tidak upload baru
    if (!$proof) $proof = $existing['proof'];
    if (!$proof_pelunasan) $proof_pelunasan = $existing['proof_pelunasan'];

    // Akumulasi: total yang sudah dibayar = lama + input baru
    $amount_paid_total = (float)$existing['old_paid'] + $amount_paid;
    if ($amount_paid_total > $amount_total) $amount_paid_total = $amount_total;

    // Recalculate status berdasarkan total akumulasi
    if ($amount_paid_total >= $amount_total && $amount_total > 0) {
        $status      = 'paid';
        $amount_paid_total = $amount_total;
    } elseif ($amount_paid_total > 0) {
        $status = 'partial';
    } else {
        $status = 'unpaid';
    }

    $pt  = mysqli_real_escape_string($connection, $payment_type);
    $st  = mysqli_real_escape_string($connection, $status);
    $pr  = $proof            ? "'" . mysqli_real_escape_string($connection, $proof)            . "'" : "NULL";
    $pr2 = $proof_pelunasan  ? "'" . mysqli_real_escape_string($connection, $proof_pelunasan)  . "'" : "NULL";
    $pa  = $paid_at          ? "'" . mysqli_real_escape_string($connection, $paid_at)          . "'" : "NULL";

    $ok = mysqli_query($connection,
        "UPDATE payments
         SET payment_type    = '$pt',
             amount_total    = $amount_total,
             dp_amount       = $dp_amount,
             amount_paid     = $amount_paid_total,
             status          = '$st',
             proof           = $pr,
             proof_pelunasan = $pr2,
             paid_at         = $pa
         WHERE id = $payment_id"
    );

    $_SESSION[$ok ? 'success' : 'error'] = $ok
        ? 'Pembayaran berhasil diperbarui.'
        : 'Gagal memperbarui data pembayaran.';

} else {

    // Cek apakah sudah ada payment untuk application ini
    $existing = mysqli_fetch_assoc(mysqli_query($connection,
        "SELECT id, status FROM payments WHERE application_id = $application_id LIMIT 1"
    ));

    if ($existing) {
        if ($existing['status'] === 'paid') {
            $_SESSION['error'] = 'Pengajuan ini sudah lunas.';
            header('Location: create.php');
            exit;
        }
        // Seharusnya tidak terjadi (JS sudah handle), tapi fallback: update
        $pt  = mysqli_real_escape_string($connection, $payment_type);
        $st  = mysqli_real_escape_string($connection, $status);
        $pr  = $proof            ? "'" . mysqli_real_escape_string($connection, $proof)            . "'" : "NULL";
        $pr2 = $proof_pelunasan  ? "'" . mysqli_real_escape_string($connection, $proof_pelunasan)  . "'" : "NULL";
        $pa  = $paid_at          ? "'" . mysqli_real_escape_string($connection, $paid_at)          . "'" : "NULL";
        $eid = (int)$existing['id'];

        $ok = mysqli_query($connection,
            "UPDATE payments
             SET payment_type = '$pt', amount_total = $amount_total,
                 dp_amount    = $dp_amount, amount_paid  = $amount_paid,
                 status       = '$st', proof        = $pr,
                 proof_pelunasan = $pr2, paid_at    = $pa
             WHERE id = $eid"
        );
        $_SESSION[$ok ? 'success' : 'error'] = $ok
            ? 'Pembayaran berhasil diperbarui.'
            : 'Gagal memperbarui data pembayaran.';
    } else {
        $id = $paymentModel->create(
            $application_id, $payment_type, $amount_total,
            $dp_amount, $amount_paid, $status, $proof, $paid_at, $proof_pelunasan
        );
        $_SESSION[$id ? 'success' : 'error'] = $id
            ? 'Pembayaran berhasil ditambahkan.'
            : 'Gagal menyimpan data pembayaran.';
    }
}

header('Location: index.php');
exit;