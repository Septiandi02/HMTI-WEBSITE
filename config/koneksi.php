<?php

// Load BASE_URL otomatis (local vs domain)
require_once __DIR__ . '/base_url.php';

// ==========================================
// WAKTU: selalu pakai zona WIB (Indonesia Barat)
// Supaya sapaan, jam, dan tanggal konsisten di seluruh aplikasi
// ==========================================
date_default_timezone_set('Asia/Jakarta');

// ==========================================
// SESUAIKAN dengan credential database di server!
// ==========================================
// Localhost (XAMPP):
//   host = localhost, user = root, pass = "", dbname = db_himti
//
// Domain (cPanel):
//   host = localhost (atau 127.0.0.1)
//   user = (dari cPanel → Databases → MySQL)
//   pass = (password database dari cPanel)
//   dbname = (nama database dari cPanel, biasanya tiubbac_hmti)
// ==========================================

$host = "localhost";
$user = "root";       
$pass = "";           
$dbname = "db_himti";

// ------------------------------------------------------------
// KONFIGURASI ERROR: tampilkan error hanya di localhost (dev),
// sembunyikan di server produksi supaya detail tidak bocor ke user.
// ------------------------------------------------------------
$_server_host = $_SERVER['HTTP_HOST'] ?? '';
$is_local = in_array($_server_host, ['localhost', '127.0.0.1', '::1']);

error_reporting(E_ALL);
if ($is_local) {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    if (!is_dir(__DIR__ . '/../logs')) {
        @mkdir(__DIR__ . '/../logs', 0775, true);
    }
    ini_set('error_log', __DIR__ . '/../logs/php-error.log');
}
unset($_server_host, $is_local);

// Menggunakan mysqli (procedural sederhana, cocok untuk PHP native)
$koneksi = mysqli_connect($host, $user, $pass, $dbname);

// Cek koneksi, pesan generik (detail dicatat ke log, TIDAK ditampilkan ke user)
if (!$koneksi) {
    $err = mysqli_connect_error();
    // Catat ke log
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) { @mkdir($log_dir, 0775, true); }
    @file_put_contents($log_dir . '/error.log', '[' . date('Y-m-d H:i:s') . '] Koneksi DB gagal: ' . $err . PHP_EOL, FILE_APPEND | LOCK_EX);
    die("Terjadi kesalahan pada sistem. Silakan coba lagi nanti.");
}

// Set charset supaya karakter (misal huruf, emoji, simbol) tidak rusak
mysqli_set_charset($koneksi, "utf8mb4");
?>