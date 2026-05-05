<?php
/**
 * AKUVISA — serve_file.php
 * Letakkan file ini di root project: visaapp/serve_file.php
 *
 * Melayani file dari folder uploads/ hanya untuk admin yang sudah login.
 * Gunakan URL: serve_file.php?file=uploads/documents/doc_xxx.pdf
 */

session_start();
require_once __DIR__ . '/helper/auth.php'; // redirect ke login jika belum login

$file_param = $_GET['file'] ?? '';

// Sanitasi path — cegah directory traversal
$file_param = ltrim($file_param, '/');
$real_base  = realpath(__DIR__ . '/uploads');
$real_file  = realpath(__DIR__ . '/' . $file_param);

// Pastikan file ada di dalam folder uploads/ dan tidak keluar dari sana
if (!$real_file || strpos($real_file, $real_base) !== 0 || !file_exists($real_file)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

// Hanya izinkan ekstensi yang aman
$ext = strtolower(pathinfo($real_file, PATHINFO_EXTENSION));
$allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

if (!in_array($ext, $allowed_ext)) {
    http_response_code(403);
    exit('Tipe file tidak diizinkan.');
}

// Validasi MIME type sesungguhnya (bukan hanya ekstensi)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $real_file);
finfo_close($finfo);

$mime_map = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'pdf'  => 'application/pdf',
];

if ($mime !== ($mime_map[$ext] ?? '')) {
    http_response_code(403);
    exit('File tidak valid.');
}

// Kirim file ke browser
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($real_file));
header('Content-Disposition: inline; filename="' . basename($real_file) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=3600');

readfile($real_file);
exit;