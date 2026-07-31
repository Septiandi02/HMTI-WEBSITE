<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';

// Ambil daftar departemen untuk dropdown
$dept_result = mysqli_query($koneksi, "SELECT id, nama_departemen FROM departemen ORDER BY id ASC");
?>

<div class="page-header">
    <h1>Tambah Anggota</h1>
    <p>Tambahkan anggota baru ke struktur organisasi</p>
</div>

<form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="nama">Nama Lengkap <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama" id="nama" placeholder="Masukkan nama lengkap" required>
    </div>

    <div class="form-group">
        <label for="jabatan">Jabatan <span style="color:#e05252;">*</span></label>
        <input type="text" name="jabatan" id="jabatan" placeholder="Contoh: Ketua HMTI, Sekretaris, dll" required>
    </div>

    <div class="form-group">
        <label for="kategori">Kategori <span style="color:#e05252;">*</span></label>
        <select name="kategori" id="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="pejabat_teras">Pejabat Teras</option>
            <option value="departemen">Anggota Departemen</option>
        </select>
    </div>

    <div class="form-group" id="departemenGroup" style="display:none;">
        <label for="departemen_id">Departemen <span style="color:#e05252;">*</span></label>
        <select name="departemen_id" id="departemen_id">
            <option value="">-- Pilih Departemen --</option>
            <?php if ($dept_result): while ($d = mysqli_fetch_assoc($dept_result)): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama_departemen']) ?></option>
            <?php endwhile; endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="urutan">Urutan</label>
        <input type="number" name="urutan" id="urutan" value="0" min="0" style="max-width:120px;">
        <small style="color:#8a8f98;">Semakin kecil angkanya, semakin tampil di awal.</small>
    </div>

    <div class="form-group">
        <label for="foto">Foto</label>
        <input type="file" name="foto" id="foto" accept="image/*">
        <small style="color:#8a8f98;">Format: JPG, PNG, WebP. Maks: 20MB.</small>
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
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
