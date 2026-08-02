<?php
// ============================================================
// UPLOAD_EDITOR.PHP
// Endpoint AJAX untuk upload gambar dari editor TinyMCE
// (misal: paste / drag gambar langsung ke dalam isi kegiatan).
//
// Sebelum fitur ini, gambar yang di-paste ke TinyMCE disimpan
// sebagai base64 di kolom 'isi', sehingga ukuran POST membengkak
// berlipat-lipat dan "update konten" menjadi sangat lambat.
// Dengan endpoint ini gambar diupload ke server dulu, dan URL-nya
// yang disimpan (bukan base64).
// ============================================================

require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/upload_helper.php';

// Hanya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Proteksi CSRF (token dikirim dari form kegiatan)
if (!csrf_verify()) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token keamanan tidak valid.']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Tidak ada file gambar.']);
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Format tidak didukung (JPG/PNG/WebP).']);
    exit;
}

$max_size = 20 * 1024 * 1024; // 20MB
if ($_FILES['file']['size'] > $max_size) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ukuran file terlalu besar (maks 20MB).']);
    exit;
}

$target_dir = __DIR__ . '/../../assets/img/kegiatan/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$nama = 'kegiatan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

if (!kompres_gambar($_FILES['file']['tmp_name'], $target_dir . $nama)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Gagal memproses gambar.']);
    exit;
}

header('Content-Type: application/json');
$url = BASE_URL . '/assets/img/kegiatan/' . $nama;
echo json_encode(['location' => $url, 'url' => $url]);
exit;
