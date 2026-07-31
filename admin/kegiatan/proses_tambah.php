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

$judul = trim($_POST['judul'] ?? '');
$isi   = bersihkan_html($_POST['isi'] ?? '');

// Validasi
if ($judul === '' || $isi === '') {
    header('Location: tambah.php?status=error');
    exit;
}

// Proses upload gambar
$gambar_nama = '';
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $max_size = 20 * 1024 * 1024; // 20MB (anti DoS via file raksasa)
        if ($_FILES['gambar']['size'] <= $max_size) {
            $gambar_nama = 'kegiatan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = __DIR__ . '/../../assets/img/kegiatan/' . $gambar_nama;

            if (!is_dir(__DIR__ . '/../../assets/img/kegiatan/')) {
                mkdir(__DIR__ . '/../../assets/img/kegiatan/', 0777, true);
            }

            if (!kompres_gambar($_FILES['gambar']['tmp_name'], $target)) {
                $gambar_nama = '';
            }
        }
    }
}

$penulis_id = (int)$_SESSION['user_id'];
$gambar_db  = $gambar_nama !== '' ? $gambar_nama : null;

$ok = db_query(
    "INSERT INTO kegiatan (judul, gambar, isi, penulis_id, tanggal_dibuat) VALUES (?, ?, ?, ?, NOW())",
    [$judul, $gambar_db, $isi, $penulis_id]
);

if ($ok) {
    header('Location: index.php?status=tambah_sukses');
} else {
    header('Location: tambah.php?status=error');
}
exit;
