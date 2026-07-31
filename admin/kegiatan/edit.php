<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT id, judul, gambar, isi FROM kegiatan WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$kegiatan = $result ? mysqli_fetch_assoc($result) : null;

if (!$kegiatan):
?>
    <div class="page-header"><h1>Kegiatan Tidak Ditemukan</h1></div>
    <p style="color:#8a8f98;">Kegiatan yang dimaksud tidak ada atau sudah dihapus.</p>
    <a href="index.php" class="btn-admin btn-admin-primary" style="margin-top:16px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
<?php
    include __DIR__ . '/../includes/footer.php';
    exit;
endif;
?>

<div class="page-header">
    <h1>Edit Kegiatan</h1>
    <p>Perbarui informasi kegiatan</p>
</div>

<form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="admin-form tinymce-full">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $kegiatan['id'] ?>">

    <div class="form-group">
        <label for="judul">Judul Kegiatan <span style="color:#e05252;">*</span></label>
        <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($kegiatan['judul']) ?>" required>
    </div>

    <div class="form-group">
        <label for="gambar">Gambar Sampul</label>
        <?php if (!empty($kegiatan['gambar'])): ?>
            <div style="margin-bottom:10px;">
                <img src="<?= BASE_URL ?>/assets/img/kegiatan/<?= $kegiatan['gambar'] ?>" alt="Current" style="width:120px;height:80px;object-fit:cover;border-radius:8px;">
                <br>
                <small style="color:#8a8f98;">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>
        <?php endif; ?>
        <input type="file" name="gambar" id="gambar" accept="image/*">
        <small style="color:#8a8f98;">Format: JPG, PNG, WebP. Maks: 20MB.</small>
    </div>

    <div class="form-group">
        <label for="isi">Isi Kegiatan <span style="color:#e05252;">*</span></label>
        <textarea name="isi" id="isi" rows="12"><?= htmlspecialchars($kegiatan['isi']) ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="fa-solid fa-save"></i> Simpan Perubahan
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
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
