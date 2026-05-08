<?php
/**
 * Koneksi database — membaca kredensial dari file .env
 * JANGAN menyimpan username/password langsung di file ini.
 *
 * Setup:
 *   1. Salin .env.example menjadi .env
 *   2. Isi DB_USER, DB_PASS, dst. di .env
 *   3. Pastikan .env ada di .gitignore
 */

$env_path = __DIR__ . '/../.env';

if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($val);
        }
    }
}

$dbhost     = $_ENV['DB_HOST'] ?? 'localhost';
$dbusername = $_ENV['DB_USER'] ?? '';
$dbpassword = $_ENV['DB_PASS'] ?? '';
$dbname     = $_ENV['DB_NAME'] ?? '';

$connection = @mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname);

if (!$connection) {
    // Log detail error ke server, JANGAN tampilkan ke browser
    error_log('DB connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.');
}
