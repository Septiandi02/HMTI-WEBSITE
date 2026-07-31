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

// Ambil data untuk hapus file foto (prepared statement)
$result = db_query("SELECT foto FROM anggota_organisasi WHERE id = ?", [$id]);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if (!empty($row['foto'])) {
        $file_path = __DIR__ . '/../../assets/img/struktur/' . $row['foto'];
        if (file_exists($file_path)) unlink($file_path);
    }
}

$ok = db_query("DELETE FROM anggota_organisasi WHERE id = ?", [$id]);
$result = $ok ? 'hapus_sukses' : 'error';
header('Location: index.php?status=' . $result);
exit;
