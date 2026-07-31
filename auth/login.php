<?php
require_once __DIR__ . '/../includes/security.php';
security_init();
require_once __DIR__ . '/../config/base_url.php';

// Kalau sudah login, tidak perlu lihat halaman login lagi - langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HMTI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/auth.css') ?>">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">

        <a href="<?= BASE_URL ?>/index.php" class="auth-brand">
            <img src="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png" alt="Logo HMTI">
            <span>HMTI</span>
        </a>

        <h1>Masuk ke Panel Admin</h1>
        <p class="auth-subtitle">Khusus untuk Admin & Super Admin HMTI</p>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> Username atau password salah.
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'locked'): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-lock"></i> Terlalu banyak percobaan gagal. Coba lagi dalam 15 menit.
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'logout'): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> Berhasil logout.
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/proses_login.php" method="POST" autocomplete="off">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-cta full-width">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <a href="<?= BASE_URL ?>/index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= (int)@filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
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

</body>
</html>