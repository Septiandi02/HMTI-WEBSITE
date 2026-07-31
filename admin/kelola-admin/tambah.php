<?php
require_once __DIR__ . '/../../includes/cek_login.php';
hanya_super_admin();
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Tambah Admin</h1>
    <p>Buat akun admin atau super admin baru</p>
</div>

<form action="proses_tambah.php" method="POST" class="admin-form">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="nama">Nama Lengkap <span style="color:#e05252;">*</span></label>
        <input type="text" name="nama" id="nama" placeholder="Masukkan nama lengkap" required>
    </div>

    <div class="form-group">
        <label for="username">Username <span style="color:#e05252;">*</span></label>
        <input type="text" name="username" id="username" placeholder="Username untuk login" required>
    </div>

    <div class="form-group">
        <label for="password">Password <span style="color:#e05252;">*</span></label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required minlength="6">
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan password">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label for="role">Role <span style="color:#e05252;">*</span></label>
        <select name="role" id="role" required>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
        </select>
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
