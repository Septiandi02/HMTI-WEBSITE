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

// Hapus juga anggota organisasi yang terkait dengan departemen ini
$ok1 = db_query("DELETE FROM anggota_organisasi WHERE departemen_id = ?", [$id]);
$ok2 = db_query("DELETE FROM departemen WHERE id = ?", [$id]);

$result = ($ok1 !== false && $ok2) ? 'hapus_sukses' : 'error';
header('Location: index.php?status=' . $result);
exit;
