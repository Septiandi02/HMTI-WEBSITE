<?php
require_once '../includes/security.php';
security_init();
require_once '../config/koneksi.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Proteksi CSRF
if (!csrf_verify()) {
    http_response_code(403);
    die('Permintaan ditolak (token keamanan tidak valid). Muat ulang halaman login lalu coba lagi.');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Batasi panjang username (mencegah input raksasa / spam ke tabel login_attempts)
if (strlen($username) > 100) {
    header('Location: login.php?status=error');
    exit;
}

if ($username === '' || $password === '') {
    header('Location: login.php?status=error');
    exit;
}

// Proteksi brute-force LAPIS 2: IP yang terlalu sering gagal (berapa pun
// username-nya) ikut diblokir sementara. Menutup celah serangan yang
// menyebar ke banyak username dari satu IP.
if (login_ip_terkunci()) {
    header('Location: login.php?status=locked');
    exit;
}

// Proteksi brute-force LAPIS 1: kalau sudah 5x gagal dalam 15 menit per user+IP, blokir
if (login_terkunci($username)) {
    header('Location: login.php?status=locked');
    exit;
}

// Query pakai prepared statement (anti SQL Injection)
$result = db_query("SELECT id, nama, username, password, role FROM users WHERE username = ? LIMIT 1", [$username]);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    // password_verify() membandingkan password polos dengan hash tersimpan
    // TIDAK PERNAH membandingkan password mentah secara langsung (misal ==), itu tidak aman
    if (password_verify($password, $user['password'])) {

        // Regenerate session ID setelah login berhasil - mencegah session fixation attack
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Hapus catatan percobaan gagal untuk user ini
        login_berhasil($username);

        header('Location: ../admin/dashboard.php');
        exit;
    }
}

// Kalau sampai sini berarti username tidak ketemu ATAU password salah
// Sengaja pesan errornya digeneralisasi ("username atau password salah"),
// bukan dibedakan "username tidak ada" vs "password salah" - supaya orang jahat
// tidak bisa menebak-nebak username mana yang valid
login_gagal($username);
header('Location: login.php?status=error');
exit;