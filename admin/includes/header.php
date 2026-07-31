<?php
// Wajib paling atas: cek apakah user sudah login, kalau belum langsung ditendang ke login
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';

$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - HMTI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css?v=<?= (int)@filemtime(__DIR__ . '/../../assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css?v=<?= (int)@filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-layout">

    <!-- ================= SIDEBAR ================= -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <img src="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png" alt="Logo HMTI">
            <span>HMTI Admin</span>
        </div>

        <nav class="admin-nav">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="<?= BASE_URL ?>/admin/kegiatan/index.php" class="<?= $current_folder == 'kegiatan' ? 'active' : '' ?>">
                <i class="fa-solid fa-newspaper"></i> Kegiatan
            </a>
            <a href="<?= BASE_URL ?>/admin/galeri/index.php" class="<?= $current_folder == 'galeri' ? 'active' : '' ?>">
                <i class="fa-solid fa-images"></i> Galeri
            </a>
            <a href="<?= BASE_URL ?>/admin/struktur-organisasi/index.php" class="<?= $current_folder == 'struktur-organisasi' ? 'active' : '' ?>">
                <i class="fa-solid fa-sitemap"></i> Struktur Organisasi
            </a>
            <a href="<?= BASE_URL ?>/admin/departemen/index.php" class="<?= $current_folder == 'departemen' ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i> Departemen
            </a>
            <a href="<?= BASE_URL ?>/admin/suara-mahasiswa/index.php" class="<?= $current_folder == 'suara-mahasiswa' ? 'active' : '' ?>">
                <i class="fa-solid fa-comment-dots"></i> Suara Mahasiswa
            </a>

            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                <div class="admin-nav-divider">Super Admin</div>
                <a href="<?= BASE_URL ?>/admin/kelola-admin/index.php" class="<?= $current_folder == 'kelola-admin' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-shield"></i> Kelola Admin
                </a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- ================= KONTEN UTAMA ================= -->
    <div class="admin-main">

        <!-- ================= TOPBAR ================= -->
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="admin-topbar-right">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Ganti tema" style="border-color:var(--border-color);">
                    <i class="fa-solid fa-moon"></i>
                    <i class="fa-solid fa-sun"></i>
                </button>

                <a href="<?= BASE_URL ?>/index.php" target="_blank" class="topbar-link">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>Lihat Website</span>
                </a>

                <div class="admin-user-menu">
                    <span class="admin-user-name">
                        <?= htmlspecialchars($_SESSION['nama']) ?>
                        <small><?= $_SESSION['role'] === 'super_admin' ? 'Super Admin' : 'Admin' ?></small>
                    </span>
                    <a href="<?= BASE_URL ?>/auth/logout.php" class="btn-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <main class="admin-content">