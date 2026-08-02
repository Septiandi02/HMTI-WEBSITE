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

$caption = trim($_POST['caption'] ?? '');
$is_ajax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
        || (($_POST['ajax'] ?? '') === '1');

// Normalisasi input file: dukung nama 'gambar' (satu) dan 'gambar[]' (banyak)
$files = $_FILES['gambar'] ?? null;
if (!$files || !isset($files['error'])) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Tidak ada file.']);
        exit;
    }
    header('Location: tambah.php?status=error');
    exit;
}

// Bungkus jadi array kalau cuma satu file (nama lama 'gambar')
if (!is_array($files['name'])) {
    $files = [
        'name'     => [$files['name']],
        'type'     => [$files['type']],
        'tmp_name' => [$files['tmp_name']],
        'error'    => [$files['error']],
        'size'     => [$files['size']],
    ];
}

$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$max_size = 20 * 1024 * 1024; // 20MB per file (anti DoS via file raksasa)
$target_dir = __DIR__ . '/../../assets/img/galeri/';

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$sukses = 0;
$gagal = 0;

for ($i = 0, $n = count($files['name']); $i < $n; $i++) {
    if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $gagal++;
        continue;
    }

    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $gagal++;
        continue;
    }

    if ($files['size'][$i] > $max_size) {
        $gagal++;
        continue;
    }

    // Generate nama unik
    $gambar_nama = 'galeri_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (kompres_gambar($files['tmp_name'][$i], $target_dir . $gambar_nama)) {
        $ok = db_query(
            "INSERT INTO galeri (gambar, caption, tanggal_upload) VALUES (?, ?, NOW())",
            [$gambar_nama, $caption]
        );

        if ($ok) {
            $sukses++;
        } else {
            // Hapus file kalau query gagal
            if (file_exists($target_dir . $gambar_nama)) unlink($target_dir . $gambar_nama);
            $gagal++;
        }
    } else {
        $gagal++;
    }
}

// Respon JSON untuk upload AJAX (multi-foto + progress bar)
if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $sukses > 0,
        'sukses'  => $sukses,
        'gagal'   => $gagal,
    ]);
    exit;
}

if ($sukses > 0) {
    header('Location: index.php?status=tambah_sukses');
} else {
    header('Location: tambah.php?status=error');
}
exit;

