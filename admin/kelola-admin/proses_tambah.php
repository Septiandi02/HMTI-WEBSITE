<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
hanya_super_admin();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF
csrf_protect();

$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'admin';

// Batasi panjang username (anti input raksasa)
if (strlen($username) > 100) {
    header('Location: tambah.php?status=error');
    exit;
}

if ($nama === '' || $username === '' || $password === '') {
    header('Location: tambah.php?status=error');
    exit;
}

// Password wajib memenuhi kebijakan kuat (min 8, huruf + angka, beda dari username)
if (!password_kuat($password, $username)) {
    header('Location: tambah.php?status=error_password');
    exit;
}

if (!in_array($role, ['admin', 'super_admin'])) {
    $role = 'admin';
}

// Cek duplikasi username (prepared statement)
$check = db_query("SELECT id FROM users WHERE username = ? LIMIT 1", [$username]);
if ($check && mysqli_num_rows($check) > 0) {
    header('Location: tambah.php?status=error');
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$ok = db_query(
    "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)",
    [$nama, $username, $password_hash, $role]
);

if ($ok) {
    header('Location: index.php?status=tambah_sukses');
} else {
    header('Location: tambah.php?status=error');
}
exit;
