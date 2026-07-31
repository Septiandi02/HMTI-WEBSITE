<?php
require_once '../includes/security.php';
security_init();
require_once '../config/koneksi.php';
require_once '../includes/db.php';

// Hanya proses kalau request-nya memang POST (bukan diakses langsung lewat URL)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: suara-mahasiswa.php');
    exit;
}

// Proteksi CSRF (form publik tetap butuh token)
if (!csrf_verify()) {
    http_response_code(403);
    die('Permintaan ditolak (token keamanan tidak valid). Muat ulang halaman lalu coba lagi.');
}

$anonim = isset($_POST['anonim']) ? 1 : 0;
$isi_aspirasi = trim($_POST['isi_aspirasi'] ?? '');

// Validasi: aspirasi wajib diisi
if ($isi_aspirasi === '') {
    header('Location: suara-mahasiswa.php?status=error');
    exit;
}

// Kalau anonim dicentang, paksa nama & NIM kosong (walau ada yang coba kirim manual lewat luar form)
if ($anonim) {
    $nama = null;
    $nim = null;
} else {
    $nama_input = trim($_POST['nama'] ?? '');
    $nim_input = trim($_POST['nim'] ?? '');
    $nama = $nama_input !== '' ? $nama_input : null;
    $nim = $nim_input !== '' ? $nim_input : null;
}

// INSERT pakai prepared statement (anti SQL Injection)
$ok = db_query(
    "INSERT INTO suara_mahasiswa (nama, nim, anonim, isi_aspirasi) VALUES (?, ?, ?, ?)",
    [$nama, $nim, $anonim, $isi_aspirasi]
);

if ($ok) {
    // Pola Post/Redirect/Get: redirect setelah insert supaya kalau user refresh halaman,
    // form TIDAK ikut ke-submit ulang secara tidak sengaja
    header('Location: suara-mahasiswa.php?status=success');
} else {
    header('Location: suara-mahasiswa.php?status=error');
}
exit;