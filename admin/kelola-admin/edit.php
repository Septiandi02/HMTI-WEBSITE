<?php
require_once __DIR__ . '/../../includes/cek_login.php';
hanya_super_admin();
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT id, nama, username, role FROM users WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$user = $result ? mysqli_fetch_assoc($result) : null;

if (!$user):
?>
    <div class="page-header"><h1>User Tidak Ditemukan</h1></div>
    <a href="index.php" class="btn-admin btn-admin-primary" style="margin-top:16px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
<?php
    include __DIR__ . '/../includes/footer.php';
    exit;
endif;
?>

<div class="page-header">
    <h1>Edit Admin</h1>
    <p>Perbarui data admin</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'error_role'): ?>
    <div class="admin-alert admin-alert-error">
        <i class="fa-solid fa-shield-halved"></i> Role super admin tidak bisa diturunkan / dihapus untuk akun super admin terakhir.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error_password'): ?>
    <div class="admin-alert admin-alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Password harus minimal 8 karakter, mengandung huruf & angka, dan tidak sama dengan username.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <div class="admin-alert admin-alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Gagal menyimpan. Periksa kembali data yang diisi.
    </div>
<?php endif; ?>

<form action="proses_edit.php" method="POST" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $user['id'] ?>">

    <div class="form-group">
        <label for="nama">Nama Lengkap <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
    </div>

    <div class="form-group">
        <label for="username">Username <span style="color:#e05252;">*</span></label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    </div>

    <div class="form-group">
        <label for="password">Password Baru</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="8">
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan password">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
        <small style="color:#8a8f98;">Minimal 8 karakter, harus ada huruf & angka. Kosongkan jika tidak ingin mengganti password.</small>
    </div>

    <div class="form-group">
        <label for="role">Role</label>
        <select name="role" id="role">
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
        </select>
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
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    const isHidden = passwordInput.type === 'password';
    passwordInput.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
