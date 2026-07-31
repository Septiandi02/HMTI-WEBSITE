<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Tambah Departemen</h1>
    <p>Buat departemen baru</p>
</div>

<form action="proses_tambah.php" method="POST" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="nama_departemen">Nama Departemen <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama_departemen" id="nama_departemen" placeholder="Contoh: RISTEK, PSDM, KOMINFO" required>
    </div>

    <div class="form-group">
        <label for="deskripsi">Deskripsi <span style="color:#e05252;">*</span></label>
        <textarea name="deskripsi" id="deskripsi" rows="6" placeholder="Jelaskan tentang departemen ini..." required></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="fa-solid fa-save"></i> Simpan
        </button>
        <a href="index.php" class="btn-admin" style="background:#e0e3e7;color:#4a4d54;">
            Batal
        </a>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
