<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM departemen WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$dept = $result ? mysqli_fetch_assoc($result) : null;

if (!$dept):
?>
    <div class="page-header"><h1>Departemen Tidak Ditemukan</h1></div>
    <a href="index.php" class="btn-admin btn-admin-primary" style="margin-top:16px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
<?php
    include __DIR__ . '/../includes/footer.php';
    exit;
endif;
?>

<div class="page-header">
    <h1>Edit Departemen</h1>
    <p>Perbarui informasi departemen</p>
</div>

<form action="proses_edit.php" method="POST" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $dept['id'] ?>">

    <div class="form-group">
        <label for="nama_departemen">Nama Departemen <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama_departemen" id="nama_departemen" value="<?= htmlspecialchars($dept['nama_departemen']) ?>" required>
    </div>

    <div class="form-group">
        <label for="deskripsi">Deskripsi <span style="color:#e05252;">*</span></label>
        <textarea name="deskripsi" id="deskripsi" rows="6" required><?= htmlspecialchars($dept['deskripsi']) ?></textarea>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
