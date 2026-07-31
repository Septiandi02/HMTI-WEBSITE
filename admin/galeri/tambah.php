<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Upload Foto</h1>
    <p>Tambahkan foto ke galeri HMTI</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'error_size'): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Ukuran file terlalu besar. Maksimal 20MB.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Gagal mengupload foto. Periksa format file (JPG/PNG/WebP).
    </div>
<?php endif; ?>

<form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="gambar">Pilih Foto <span style="color:#e05252;">*</span></label>
        <input type="file" name="gambar" id="gambar" accept="image/*" required>
        <small style="color:#8a8f98;">Format: JPG, PNG, WebP. Maks: 20MB.</small>
    </div>

    <div class="form-group">
        <label for="caption">Caption</label>
        <input type="text" name="caption" id="caption" placeholder="Tulis caption singkat untuk foto ini" maxlength="255">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="fa-solid fa-upload"></i> Upload
        </button>
        <a href="index.php" class="btn-admin" style="background:#e0e3e7;color:#4a4d54;">
            Batal
        </a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
