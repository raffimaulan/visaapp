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

$application_id = (int)($_POST['application_id'] ?? 0);

if (!$application_id) {
    $_SESSION['error'] = 'Pilih pengajuan terlebih dahulu.';
    header('Location: create.php');
    exit;
}

$files = $_FILES['documents'] ?? null;

if (!$files || empty($files['name'][0])) {
    $_SESSION['error'] = 'Pilih minimal satu file untuk diupload.';
    header("Location: create.php?application_id=$application_id");
    exit;
}

$allowed_mime = ['image/jpeg', 'image/png', 'application/pdf'];
$ext_map      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
$upload_dir   = __DIR__ . '/../uploads/documents/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$docModel      = new Document($connection);
$success_count = 0;
$errors        = [];

$total = count($files['name']);

for ($i = 0; $i < $total; $i++) {

    if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

    $name  = $files['name'][$i];
    $tmp   = $files['tmp_name'][$i];
    $size  = $files['size'][$i];
    $error = $files['error'][$i];

    if ($error !== UPLOAD_ERR_OK) {
        $errors[] = "$name: Gagal diterima server (kode error: $error).";
        continue;
    }

    if (empty($tmp) || !is_uploaded_file($tmp)) {
        $errors[] = "$name: File tidak valid atau tidak diterima server.";
        continue;
    }

    if ($size > 2 * 1024 * 1024) {
        $errors[] = "$name: Ukuran file melebihi 2MB.";
        continue;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mime)) {
        $errors[] = "$name: Format tidak didukung (gunakan JPG, PNG, atau PDF).";
        continue;
    }

    $ext      = $ext_map[$mime];
    $filename = 'doc_' . $application_id . '_' . time() . '_' . uniqid() . '.' . $ext;
    $file_path = 'uploads/documents/' . $filename;

    if (move_uploaded_file($tmp, $upload_dir . $filename)) {
        $docModel->create($application_id, $file_path);
        $success_count++;
    } else {
        $errors[] = "$name: Gagal menyimpan ke server. Periksa permission folder uploads/.";
    }
}

if ($success_count > 0 && empty($errors)) {
    $_SESSION['success'] = "$success_count file berhasil diupload.";
} elseif ($success_count > 0 && !empty($errors)) {
    $_SESSION['success'] = "$success_count file berhasil diupload. " . count($errors) . " file gagal: " . implode(', ', $errors);
} else {
    $_SESSION['error'] = 'Semua file gagal diupload. ' . implode(' ', $errors);
}

// Support fetch (XHR) request — kembalikan JSON dengan URL redirect
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['redirect' => 'index.php']);
    exit;
}

header('Location: index.php');
exit;