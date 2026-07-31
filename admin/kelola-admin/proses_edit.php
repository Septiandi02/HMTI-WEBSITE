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

$id       = (int)($_POST['id'] ?? 0);
$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'admin';

if ($id <= 0 || $nama === '' || $username === '') {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

if (!in_array($role, ['admin', 'super_admin'])) {
    $role = 'admin';
}

// Cek duplikasi username (kecuali user ini sendiri), pakai prepared statement
$check = db_query("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1", [$username, $id]);
if ($check && mysqli_num_rows($check) > 0) {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

// Ganti password kalau diisi
if ($password !== '' && strlen($password) >= 6) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $ok = db_query(
        "UPDATE users SET nama = ?, username = ?, role = ?, password = ? WHERE id = ?",
        [$nama, $username, $role, $password_hash, $id]
    );
} else {
    $ok = db_query(
        "UPDATE users SET nama = ?, username = ?, role = ? WHERE id = ?",
        [$nama, $username, $role, $id]
    );
}

if ($ok) {
    header('Location: index.php?status=edit_sukses');
} else {
    header('Location: edit.php?id=' . $id . '&status=error');
}
exit;
