<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF
csrf_protect();

$nama = trim($_POST['nama_departemen'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

if ($nama === '' || $deskripsi === '') {
    header('Location: tambah.php?status=error');
    exit;
}

$ok = db_query(
    "INSERT INTO departemen (nama_departemen, deskripsi) VALUES (?, ?)",
    [$nama, $deskripsi]
);

if ($ok) {
    header('Location: index.php?status=tambah_sukses');
} else {
    header('Location: tambah.php?status=error');
}
exit;
