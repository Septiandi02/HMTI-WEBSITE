<?php
// ==========================================
// UPLOAD_HELPER.PHP
// Fungsi bantu untuk upload & kompresi gambar
// ==========================================

/**
 * Kompres & resize gambar setelah diupload.
 * 
 * @param string $source      Path file asli (tmp_name)
 * @param string $destination Path tujuan file hasil kompresi
 * @param int    $max_width   Lebar maksimal (pixels), 0 = tidak di-resize
 * @param int    $quality     Kualitas JPEG/WebP (1-100)
 * @return bool               true jika berhasil, false jika gagal
 */
function kompres_gambar($source, $destination, $max_width = 1920, $quality = 80) {
    // Cek GD library, kalau tidak ada jangan langsung pindahkan file.
    // Tetap verifikasi bahwa ini file gambar ASLI (MIME dicek) supaya file
    // berbahaya (PHP, polyglot) tidak lolos tersimpan.
    if (!extension_loaded('gd') && !function_exists('imagecreatefromjpeg')) {
        $info = @getimagesize($source);
        if (!$info) return false;
        $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($info['mime'], $allowed_mime, true)) {
            return false;
        }
        return move_uploaded_file($source, $destination);
    }

    // Baca info gambar
    $info = @getimagesize($source);
    if (!$info) return false;

    list($width, $height) = $info;
    $mime = $info['mime'];

    // Buat resource dari file asli
    switch ($mime) {
        case 'image/jpeg':
            $src_img = @imagecreatefromjpeg($source);
            $ext = 'jpg';
            break;
        case 'image/png':
            $src_img = @imagecreatefrompng($source);
            $ext = 'png';
            // PNG: pertahankan alpha channel
            break;
        case 'image/webp':
            $src_img = @imagecreatefromwebp($source);
            $ext = 'webp';
            break;
        default:
            return false;
    }

    if (!$src_img) return false;

    // Hitung dimensi baru (resize proporsional)
    $new_width = $width;
    $new_height = $height;

    if ($max_width > 0 && $width > $max_width) {
        $ratio = $max_width / $width;
        $new_width = $max_width;
        $new_height = (int)($height * $ratio);
    }

    // Buat canvas baru
    $dst_img = imagecreatetruecolor($new_width, $new_height);

    // Pertahankan transparansi PNG
    if ($mime === 'image/png') {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
    }

    // Resize pakai bilinear interpolation (kualitas bagus)
    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Simpan hasil kompresi
    $result = false;
    switch ($mime) {
        case 'image/jpeg':
            $result = imagejpeg($dst_img, $destination, $quality);
            break;
        case 'image/png':
            // PNG: kompresi level 6-9 (9 = maksimal, tapi lambat)
            $result = imagepng($dst_img, $destination, 6);
            break;
        case 'image/webp':
            $result = imagewebp($dst_img, $destination, $quality);
            break;
    }

    // Bersihkan memory
    imagedestroy($src_img);
    imagedestroy($dst_img);

    return $result;
}
