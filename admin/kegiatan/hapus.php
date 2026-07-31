<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF (hapus wajib lewat form POST yang punya token)
csrf_protect();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data untuk hapus file gambar (prepared statement)
$result = db_query("SELECT gambar FROM kegiatan WHERE id = ?", [$id]);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Hapus file gambar dari folder
    if (!empty($row['gambar'])) {
        $file_path = __DIR__ . '/../../assets/img/kegiatan/' . $row['gambar'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

$ok = db_query("DELETE FROM kegiatan WHERE id = ?", [$id]);

if ($ok) {
    header('Location: index.php?status=hapus_sukses');
} else {
    header('Location: index.php?status=error');
}
exit;
