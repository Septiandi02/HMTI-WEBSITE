<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)
require_once __DIR__ . '/../../includes/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF
csrf_protect();

$caption = trim($_POST['caption'] ?? '');

// Validasi file
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    header('Location: tambah.php?status=error');
    exit;
}

$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    header('Location: tambah.php?status=error');
    exit;
}

$max_size = 20 * 1024 * 1024; // 20MB (anti DoS via file raksasa)
if ($_FILES['gambar']['size'] > $max_size) {
    header('Location: tambah.php?status=error_size');
    exit;
}

// Generate nama unik
$gambar_nama = 'galeri_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$target_dir = __DIR__ . '/../../assets/img/galeri/';

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (!kompres_gambar($_FILES['gambar']['tmp_name'], $target_dir . $gambar_nama)) {
    header('Location: tambah.php?status=error');
    exit;
}

$ok = db_query(
    "INSERT INTO galeri (gambar, caption, tanggal_upload) VALUES (?, ?, NOW())",
    [$gambar_nama, $caption]
);

if ($ok) {
    header('Location: index.php?status=tambah_sukses');
} else {
    // Hapus file kalau query gagal
    if (file_exists($target_dir . $gambar_nama)) unlink($target_dir . $gambar_nama);
    header('Location: tambah.php?status=error');
}
exit;
