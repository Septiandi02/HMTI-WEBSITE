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

$ok = db_query("DELETE FROM suara_mahasiswa WHERE id = ?", [$id]);
$result = $ok ? 'hapus_sukses' : 'error';

header('Location: index.php?status=' . $result);
exit;
