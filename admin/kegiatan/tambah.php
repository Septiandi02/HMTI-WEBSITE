<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Tambah Kegiatan</h1>
    <p>Buat kegiatan baru untuk dipublikasikan</p>
</div>

<form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="admin-form tinymce-full">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="judul">Judul Kegiatan <span style="color:#e05252;">*</span></label>
        <input type="text" name="judul" id="judul" placeholder="Masukkan judul kegiatan" required>
    </div>

    <div class="form-group">
        <label for="gambar">Gambar Sampul</label>
        <input type="file" name="gambar" id="gambar" accept="image/*">
        <small style="color:#8a8f98;">Format: JPG, PNG, WebP. Maks: 20MB. Biarkan kosong jika tidak ada.</small>
    </div>

    <div class="form-group">
        <label for="isi">Isi Kegiatan <span style="color:#e05252;">*</span></label>
        <textarea name="isi" id="isi" rows="12" placeholder="Tulis isi kegiatan di sini..."></textarea>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#isi',
    height: 400,
    menubar: false,
    plugins: 'lists link image preview code',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
    branding: false,
    automatic_uploads: true,
    paste_data_images: true,
    relative_urls: false,
    remove_script_host: false,
    convert_urls: false,
    images_upload_handler: function (blobInfo, success, failure) {
        var fd = new FormData();
        fd.append('file', blobInfo.blob(), blobInfo.filename());
        var tokenEl = document.querySelector('input[name="csrf_token"]');
        if (tokenEl) fd.append('csrf_token', tokenEl.value);
        fetch('upload_editor.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json().catch(function () { return null; }); })
            .then(function (data) {
                if (data && data.location) success(data.location);
                else failure('Gagal upload gambar' + (data && data.error ? ': ' + data.error : ''));
            })
            .catch(function () { failure('Gagal upload gambar'); });
    },
    setup: function (editor) {
        editor.on('change', function () {
            editor.save(); // sync ke textarea asli untuk dikirim via form
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
