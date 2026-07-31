<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM anggota_organisasi WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$anggota = $result ? mysqli_fetch_assoc($result) : null;

if (!$anggota):
?>
    <div class="page-header"><h1>Anggota Tidak Ditemukan</h1></div>
    <p style="color:#8a8f98;">Data yang dimaksud tidak ada.</p>
    <a href="index.php" class="btn-admin btn-admin-primary" style="margin-top:16px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
<?php
    include __DIR__ . '/../includes/footer.php';
    exit;
endif;

$dept_result = mysqli_query($koneksi, "SELECT id, nama_departemen FROM departemen ORDER BY id ASC");
?>

<div class="page-header">
    <h1>Edit Anggota</h1>
    <p>Perbarui data anggota organisasi</p>
</div>

<form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $anggota['id'] ?>">

    <div class="form-group">
        <label for="nama">Nama Lengkap <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($anggota['nama']) ?>" required>
    </div>

    <div class="form-group">
        <label for="jabatan">Jabatan <span style="color:#e05252;">*</span></label>
        <input type="text" name="jabatan" id="jabatan" value="<?= htmlspecialchars($anggota['jabatan']) ?>" required>
    </div>

    <div class="form-group">
        <label for="kategori">Kategori <span style="color:#e05252;">*</span></label>
        <select name="kategori" id="kategori" required>
            <option value="pejabat_teras" <?= $anggota['kategori'] === 'pejabat_teras' ? 'selected' : '' ?>>Pejabat Teras</option>
            <option value="departemen" <?= $anggota['kategori'] === 'departemen' ? 'selected' : '' ?>>Anggota Departemen</option>
        </select>
    </div>

    <div class="form-group" id="departemenGroup" style="<?= $anggota['kategori'] === 'departemen' ? '' : 'display:none;' ?>">
        <label for="departemen_id">Departemen <span style="color:#e05252;">*</span></label>
        <select name="departemen_id" id="departemen_id">
            <option value="">-- Pilih Departemen --</option>
            <?php if ($dept_result): while ($d = mysqli_fetch_assoc($dept_result)): ?>
                <option value="<?= $d['id'] ?>" <?= $anggota['departemen_id'] == $d['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['nama_departemen']) ?>
                </option>
            <?php endwhile; endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="urutan">Urutan</label>
        <input type="number" name="urutan" id="urutan" value="<?= (int)$anggota['urutan'] ?>" min="0" style="max-width:120px;">
    </div>

    <div class="form-group">
        <label for="foto">Foto</label>
        <?php if (!empty($anggota['foto'])): ?>
            <div style="margin-bottom:10px;">
                <img src="<?= BASE_URL ?>/assets/img/struktur/<?= $anggota['foto'] ?>" alt="Current" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                <br><small style="color:#8a8f98;">Kosongkan jika tidak ingin mengganti foto.</small>
            </div>
        <?php endif; ?>
        <input type="file" name="foto" id="foto" accept="image/*">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const kategori = document.getElementById('kategori');
    const deptGroup = document.getElementById('departemenGroup');
    const deptSelect = document.getElementById('departemen_id');

    kategori.addEventListener('change', function () {
        if (this.value === 'departemen') {
            deptGroup.style.display = 'block';
            deptSelect.setAttribute('required', '');
        } else {
            deptGroup.style.display = 'none';
            deptSelect.removeAttribute('required');
            deptSelect.value = '';
        }
    });

    // Trigger on load
    if (kategori.value === 'departemen') {
        deptSelect.setAttribute('required', '');
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
