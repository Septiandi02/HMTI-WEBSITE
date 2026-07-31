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

$id       = (int)($_POST['id'] ?? 0);
$nama     = trim($_POST['nama'] ?? '');
$jabatan  = trim($_POST['jabatan'] ?? '');
$kategori = $_POST['kategori'] ?? '';
$urutan   = (int)($_POST['urutan'] ?? 0);
$departemen_id = ($kategori === 'departemen') ? (int)($_POST['departemen_id'] ?? 0) : 0;

if ($id <= 0 || $nama === '' || $jabatan === '' || $kategori === '') {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

if ($kategori === 'departemen' && $departemen_id <= 0) {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

// Cek data lama (prepared statement)
$check = db_query("SELECT foto FROM anggota_organisasi WHERE id = ?", [$id]);
if (!$check || mysqli_num_rows($check) === 0) {
    header('Location: index.php');
    exit;
}
$old = mysqli_fetch_assoc($check);

$dept_db = $departemen_id > 0 ? $departemen_id : null;

// Proses foto baru
$foto_nama_baru = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $max_size = 20 * 1024 * 1024; // 20MB (anti DoS via file raksasa)
        if ($_FILES['foto']['size'] <= $max_size) {
            $foto_nama = 'struktur_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = __DIR__ . '/../../assets/img/struktur/' . $foto_nama;

            if (kompres_gambar($_FILES['foto']['tmp_name'], $target)) {
                // Hapus foto lama
                if (!empty($old['foto'])) {
                    $old_path = __DIR__ . '/../../assets/img/struktur/' . $old['foto'];
                    if (file_exists($old_path)) unlink($old_path);
                }
                $foto_nama_baru = $foto_nama;
            }
        }
    }
}

// UPDATE pakai prepared statement
if ($foto_nama_baru !== null) {
    $ok = db_query(
        "UPDATE anggota_organisasi SET nama = ?, jabatan = ?, kategori = ?, departemen_id = ?, urutan = ?, foto = ? WHERE id = ?",
        [$nama, $jabatan, $kategori, $dept_db, $urutan, $foto_nama_baru, $id]
    );
} else {
    $ok = db_query(
        "UPDATE anggota_organisasi SET nama = ?, jabatan = ?, kategori = ?, departemen_id = ?, urutan = ? WHERE id = ?",
        [$nama, $jabatan, $kategori, $dept_db, $urutan, $id]
    );
}

if ($ok) {
    header('Location: index.php?status=edit_sukses');
} else {
    header('Location: edit.php?id=' . $id . '&status=error');
}
exit;
