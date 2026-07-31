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

$id        = (int)($_POST['id'] ?? 0);
$nama      = trim($_POST['nama_departemen'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

if ($id <= 0 || $nama === '' || $deskripsi === '') {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

$ok = db_query(
    "UPDATE departemen SET nama_departemen = ?, deskripsi = ? WHERE id = ?",
    [$nama, $deskripsi, $id]
);

if ($ok) {
    header('Location: index.php?status=edit_sukses');
} else {
    header('Location: edit.php?id=' . $id . '&status=error');
}
exit;
