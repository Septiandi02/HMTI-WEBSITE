<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
hanya_super_admin();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF (hapus wajib lewat form POST yang punya token)
csrf_protect();

$id = (int)($_POST['id'] ?? 0);

// Cegah super admin menghapus dirinya sendiri
if ($id <= 0 || $id === (int)$_SESSION['user_id']) {
    header('Location: index.php?status=error');
    exit;
}

// Cegah menghapus super admin TERAKHIR (kalau cuma tinggal satu, jangan dihapus)
$target = db_fetch_one("SELECT role FROM users WHERE id = ?", [$id]);
if ($target && $target['role'] === 'super_admin') {
    $jumlah_sa = db_fetch_one("SELECT COUNT(*) AS total FROM users WHERE role = 'super_admin'");
    if ($jumlah_sa && (int)$jumlah_sa['total'] <= 1) {
        header('Location: index.php?status=error');
        exit;
    }
}

$ok = db_query("DELETE FROM users WHERE id = ?", [$id]);
$result = $ok ? 'hapus_sukses' : 'error';

header('Location: index.php?status=' . $result);
exit;
