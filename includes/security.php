<?php
// ============================================================
// SECURITY.PHP, inti keamanan aplikasi HMTI
// Dipanggil otomatis via includes/header.php, cek_login.php,
// dan auth/login.php. Aman dipanggil berulang (idempoten).
// ============================================================

if (defined('HMTI_SECURITY_LOADED')) {
    return;
}
define('HMTI_SECURITY_LOADED', true);

// ------------------------------------------------------------
// 1) INISIALISASI: session + cookie hardening + timeout
// ------------------------------------------------------------
function security_init(): void {
    // Pastikan koneksi DB tersedia (dibutuhkan fungsi brute-force)
    // Dipanggil lazy, jadi tidak mengganggu halaman yang belum butuh DB.

    // Konfigurasi cookie session yang aman
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,                 // sampai browser ditutup
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,           // hanya via HTTPS (otomatis di localhost HTTP = false)
            'httponly' => true,              // tidak bisa dibaca JavaScript (anti XSS cookie theft)
            'samesite' => 'Lax',             // anti CSRF tambahan
        ]);
        session_name('HMTISESSID');
        session_start();
    }

    // Timeout session (30 menit tidak aktif → paksa logout)
    $timeout = 1800; // detik
    if (isset($_SESSION['last_activity']) && (time() - (int)$_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    $_SESSION['last_activity'] = time();

    // Kirim security headers (kalau belum ada output HTML)
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header("Content-Security-Policy: default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
            "img-src 'self' data:; " .
            "connect-src 'self'; " .
            "frame-src 'self' https://www.google.com https://maps.google.com; " .
            "frame-ancestors 'self'; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "object-src 'none'");
        if ($secure) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

// ------------------------------------------------------------
// 2) CSRF PROTECTION
// ------------------------------------------------------------

/**
 * Ambil (atau buat) token CSRF untuk sesi ini.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Cetak hidden input CSRF, taruh di dalam setiap form POST.
 * Contoh: <?= csrf_field() ?>
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verifikasi token CSRF dari POST.
 * @return bool true jika valid
 */
function csrf_verify(): bool {
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verifikasi CSRF dan hentikan request kalau tidak valid.
 */
function csrf_protect(): void {
    if (!csrf_verify()) {
        http_response_code(403);
        die('Permintaan ditolak (token keamanan tidak valid). Silakan muat ulang halaman dan coba lagi.');
    }
}

// ------------------------------------------------------------
// 3) OUTPUT ESCAPING (anti XSS)
// ------------------------------------------------------------

/**
 * Escape semua output dinamis sebelum dicetak ke HTML.
 * Contoh: <?= e($row['nama']) ?>
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitasi HTML "kaya" (dipakai untuk konten kegiatan yang memang HTML).
 * Pendekatan whitelist: hapus tag berbahaya + atribut event + URL javascript.
 */
function bersihkan_html(?string $html): string {
    if ($html === null || $html === '') {
        return '';
    }

    // 1) Hanya izinkan tag "aman" (whitelist)
    $allowed = '<p><br><b><strong><i><em><u><s><h1><h2><h3><h4><h5><h6>'
        . '<ul><ol><li><a><img><blockquote><code><pre><hr>'
        . '<table><thead><tbody><tr><td><th><span><div><figure><figcaption>';
    $html = strip_tags($html, $allowed);

    // 2) Hapus semua atribut event handler on* (onclick, onerror, onload, ...)
    $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;

    // 3) Netralkan URL javascript: di href / src / style
    $html = preg_replace('/href\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*/i', 'href="#"', $html) ?? $html;
    $html = preg_replace('/src\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*/i', 'src=""', $html) ?? $html;
    $html = preg_replace('/src\s*=\s*(["\']?)\s*data:image\/svg[^"\'>\s]*/i', 'src=""', $html) ?? $html;
    $html = preg_replace('/style\s*=\s*("[^"]*url\s*\(\s*javascript:[^"]*"|\'[^\']*url\s*\(\s*javascript:[^\']*\')/i', 'style=""', $html) ?? $html;

    // 4) Buang tag <script>/<iframe>/<object> yang mungkin lolos strip_tags (case-insensitive)
    $html = preg_replace('/<\s*(script|iframe|object|embed|form|meta|link|style)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html) ?? $html;
    $html = preg_replace('/<\s*(script|iframe|object|embed|form|meta|link|style)[^>]*>/i', '', $html) ?? $html;

    return $html;
}

// ------------------------------------------------------------
// 4) ERROR LOGGING (tanpa membocorkan detail ke user)
// ------------------------------------------------------------
function log_error(string $message): void {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0775, true);
    }
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents($log_dir . '/error.log', $line, FILE_APPEND | LOCK_EX);
}

// ------------------------------------------------------------
// 5) PROTEKSI BRUTE-FORCE LOGIN
// Tabel login_attempts dibuat otomatis kalau belum ada.
// ------------------------------------------------------------

function _security_db(): mysqli {
    global $koneksi;
    if (!$koneksi instanceof mysqli) {
        require_once __DIR__ . '/../config/koneksi.php';
    }
    return $koneksi;
}

function _pastikan_tabel_login_attempts(): void {
    $db = _security_db();
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL DEFAULT '',
        ip VARCHAR(45) NOT NULL DEFAULT '',
        sukses TINYINT(1) NOT NULL DEFAULT 0,
        attempted_at DATETIME NOT NULL,
        KEY idx_user_ip (username, ip, attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

/**
 * true = user sudah terkunci (maks 5 gagal dalam 15 menit per user+IP).
 */
function login_terkunci(string $username): bool {
    _pastikan_tabel_login_attempts();
    $db = _security_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = mysqli_prepare($db,
        "SELECT COUNT(*) FROM login_attempts
         WHERE username = ? AND ip = ? AND sukses = 0
           AND attempted_at > (NOW() - INTERVAL 15 MINUTE)");
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 'ss', $username, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $cnt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return (int)$cnt >= 5;
}

function login_gagal(string $username): void {
    _pastikan_tabel_login_attempts();
    $db = _security_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = mysqli_prepare($db,
        "INSERT INTO login_attempts (username, ip, sukses, attempted_at) VALUES (?, ?, 0, NOW())");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $username, $ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // Bersihkan data lama (lebih dari 1 hari)
    mysqli_query($db, "DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)");
}

function login_berhasil(string $username): void {
    _pastikan_tabel_login_attempts();
    $db = _security_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = mysqli_prepare($db, "DELETE FROM login_attempts WHERE username = ? AND ip = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $username, $ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
