<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)
require_once __DIR__ . '/../../includes/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Proteksi CSRF
csrf_protect();

$nama     = trim($_POST['nama'] ?? '');
$jabatan  = trim($_POST['jabatan'] ?? '');
$kategori = $_POST['kategori'] ?? '';
$urutan   = (int)($_POST['urutan'] ?? 0);
$departemen_id = ($kategori === 'departemen') ? (int)($_POST['departemen_id'] ?? 0) : 0;

if ($nama === '' || $jabatan === '' || $kategori === '') {
    header('Location: tambah.php?status=error');
    exit;
}

if ($kategori === 'departemen' && $departemen_id <= 0) {
    header('Location: tambah.php?status=error');
    exit;
}

// Proses foto
$foto_nama = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $max_size = 20 * 1024 * 1024; // 20MB (anti DoS via file raksasa)
        if ($_FILES['foto']['size'] <= $max_size) {
            $foto_nama = 'struktur_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target_dir = __DIR__ . '/../../assets/img/struktur/';

            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            if (!kompres_gambar($_FILES['foto']['tmp_name'], $target_dir . $foto_nama)) {
                $foto_nama = '';
            }
        }
    }
}

$foto_db = $foto_nama !== '' ? $foto_nama : null;
$dept_db = $departemen_id > 0 ? $departemen_id : null;

$ok = db_query(
    "INSERT INTO anggota_organisasi (nama, jabatan, foto, kategori, departemen_id, urutan)
     VALUES (?, ?, ?, ?, ?, ?)",
    [$nama, $jabatan, $foto_db, $kategori, $dept_db, $urutan]
);

if ($ok) {
    header('Location: index.php?status=tambah_sukses');
} else {
    header('Location: tambah.php?status=error');
}
exit;
