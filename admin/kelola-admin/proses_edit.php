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

// Ambil role user yang sedang diedit (untuk proteksi super admin)
$sekarang = db_fetch_one("SELECT role FROM users WHERE id = ?", [$id]);

// 1) Super admin tidak boleh menurunkan role dirinya sendiri
//    (mencegah akun super admin "terkunci keluar" gara-gara salah pilih role)
if ($sekarang && $sekarang['role'] === 'super_admin'
    && (int)$id === (int)$_SESSION['user_id']
    && $role !== 'super_admin') {
    header('Location: edit.php?id=' . $id . '&status=error_role');
    exit;
}

// 2) Cegah kehilangan super admin TERAKHIR: kalau yang diedit adalah satu-satunya
//    super admin, role-nya tidak boleh diubah lagi menjadi admin
if ($sekarang && $sekarang['role'] === 'super_admin' && $role !== 'super_admin') {
    $jumlah_sa = db_fetch_one("SELECT COUNT(*) AS total FROM users WHERE role = 'super_admin'");
    if ($jumlah_sa && (int)$jumlah_sa['total'] <= 1) {
        header('Location: edit.php?id=' . $id . '&status=error_role');
        exit;
    }
}

// 3) Kalau password baru diisi, wajib memenuhi kebijakan password yang kuat
if ($password !== '' && !password_kuat($password, $username)) {
    header('Location: edit.php?id=' . $id . '&status=error_password');
    exit;
}

// Ganti password kalau diisi (sudah dipastikan kuat di atas)
if ($password !== '') {
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
