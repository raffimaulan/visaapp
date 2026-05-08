<?php
/**
 * CSRF Protection Helper
 * Include file ini sebelum render form, dan panggil csrf_verify()
 * di setiap POST handler.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate (atau ambil yang sudah ada) CSRF token untuk session ini.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden input CSRF — sisipkan di dalam setiap <form>.
 * Contoh: <?= csrf_field() ?>
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validasi token CSRF dari $_POST.
 * Jika gagal, kirim 403 dan hentikan eksekusi.
 */
function csrf_verify(): void {
    $token      = $_POST['csrf_token'] ?? '';
    $expected   = $_SESSION['csrf_token'] ?? '';

    if (!$expected || !hash_equals($expected, $token)) {
        http_response_code(403);
        exit('403 Forbidden: CSRF token tidak valid.');
    }
}
