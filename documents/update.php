<?php
require_once __DIR__ . '/../helper/auth.php';
require_once __DIR__ . '/../helper/connection.php';
require_once __DIR__ . '/../helper/csrf.php';
require_once __DIR__ . '/../models/Document.php';

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

$docModel = new Document($connection);
$doc      = $docModel->getById($id);

if (!$doc) {
    $_SESSION['error'] = 'Dokumen tidak ditemukan.';
    header('Location: index.php');
    exit;
}

// Validasi file baru
$file = $_FILES['document'] ?? null;

if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'Pilih file yang ingin diupload.';
    header("Location: edit.php?id=$id");
    exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Gagal menerima file dari server (kode error: ' . $file['error'] . ').';
    header("Location: edit.php?id=$id");
    exit;
}

// Pastikan tmp_name valid
if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    $_SESSION['error'] = 'File tidak valid atau tidak diterima server.';
    header("Location: edit.php?id=$id");
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    $_SESSION['error'] = 'Ukuran file terlalu besar. Maksimal 2MB.';
    header("Location: edit.php?id=$id");
    exit;
}

// Validasi MIME type di sisi server
$allowed_mime = ['image/jpeg', 'image/png', 'application/pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime)) {
    $_SESSION['error'] = 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.';
    header("Location: edit.php?id=$id");
    exit;
}

// Tentukan ekstensi dari MIME
$ext_map = [
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'application/pdf' => 'pdf',
];
$ext = $ext_map[$mime];

// Buat nama file baru yang unik
$filename   = 'doc_' . $doc['application_id'] . '_' . time() . '_' . uniqid() . '.' . $ext;
$upload_dir = __DIR__ . '/../uploads/documents/';
$new_path   = 'uploads/documents/' . $filename;

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
    // Hapus file lama dari server
    $old_full_path = __DIR__ . '/../' . $doc['file_path'];
    if ($doc['file_path'] && file_exists($old_full_path)) {
        unlink($old_full_path);
    }

    // Update path di database
    $new_path_escaped = mysqli_real_escape_string($connection, $new_path);
    mysqli_query($connection, "UPDATE documents SET file_path='$new_path_escaped' WHERE id=$id");

    $_SESSION['success'] = 'Dokumen berhasil diperbarui.';
    header('Location: index.php');
} else {
    $_SESSION['error'] = 'Gagal menyimpan file ke server. Periksa permission folder uploads/.';
    header("Location: edit.php?id=$id");
}

exit;