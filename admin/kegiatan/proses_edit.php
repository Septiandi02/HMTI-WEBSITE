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

$id    = (int)($_POST['id'] ?? 0);
$judul = trim($_POST['judul'] ?? '');
$isi   = bersihkan_html($_POST['isi'] ?? '');

if ($id <= 0 || $judul === '' || $isi === '') {
    header('Location: edit.php?id=' . $id . '&status=error');
    exit;
}

// Cek apakah data lama masih ada (prepared statement)
$check = db_query("SELECT id, gambar FROM kegiatan WHERE id = ?", [$id]);
if (!$check || mysqli_num_rows($check) === 0) {
    header('Location: index.php');
    exit;
}
$old = mysqli_fetch_assoc($check);

// Proses upload gambar baru (jika ada)
$gambar_nama_baru = null;
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $max_size = 20 * 1024 * 1024; // 20MB (anti DoS via file raksasa)
        if ($_FILES['gambar']['size'] <= $max_size) {
            $gambar_nama = 'kegiatan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = __DIR__ . '/../../assets/img/kegiatan/' . $gambar_nama;

            if (kompres_gambar($_FILES['gambar']['tmp_name'], $target)) {
                // Hapus gambar lama
                if (!empty($old['gambar'])) {
                    $old_path = __DIR__ . '/../../assets/img/kegiatan/' . $old['gambar'];
                    if (file_exists($old_path)) unlink($old_path);
                }
                $gambar_nama_baru = $gambar_nama;
            }
        }
    }
}

// UPDATE pakai prepared statement
if ($gambar_nama_baru !== null) {
    $ok = db_query(
        "UPDATE kegiatan SET judul = ?, isi = ?, gambar = ? WHERE id = ?",
        [$judul, $isi, $gambar_nama_baru, $id]
    );
} else {
    $ok = db_query(
        "UPDATE kegiatan SET judul = ?, isi = ? WHERE id = ?",
        [$judul, $isi, $id]
    );
}

if ($ok) {
    header('Location: index.php?status=edit_sukses');
} else {
    header('Location: edit.php?id=' . $id . '&status=error');
}
exit;
