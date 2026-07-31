<?php
// Session dipakai untuk cek status login (Admin/Super Admin)
// session_start() harus di baris paling atas, sebelum ada output HTML apapun
// + lapisan keamanan (session hardening, security headers, CSRF helpers)
require_once __DIR__ . '/security.php';
security_init();

// Load BASE_URL (local vs domain)
require_once __DIR__ . '/../config/base_url.php';

// Deteksi halaman aktif untuk kasih class "active" di menu (opsional, biar rapi)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMTI - Himpunan Mahasiswa Teknologi Informasi UBB</title>

    <!-- Tandai bahwa JS aktif (mencegah flash untuk animasi reveal) -->
    <script>document.documentElement.classList.add("js");</script>

    <!-- Google Fonts: Montserrat + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome (untuk icon: medsos, panah, dll - tanpa perlu file gambar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png">

    <!-- CSS Global (wajib di semua halaman) -->
    <!-- ?v=filemtime = auto cache-busting: saat file berubah, browser otomatis ambil versi baru -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/global.css') ?>">
</head>
<body>

<header class="site-header">
    <div class="header-container">

        <a href="<?= BASE_URL ?>/index.php" class="brand">
            <img src="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png" alt="Logo HMTI">
            <span>HMTI</span>
        </a>

        <div class="header-actions">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Ganti tema">
                <i class="fa-solid fa-moon"></i>
                <i class="fa-solid fa-sun"></i>
            </button>

            <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <ul class="nav-menu" id="navMenu">
            <li class="nav-mobile-theme">
                <a href="javascript:void(0)" id="mobileThemeToggle">
                    <i class="fa-solid fa-moon"></i>
                    <i class="fa-solid fa-sun"></i>
                    <span class="theme-label">Mode Gelap</span>
                </a>
            </li>
            <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/index.php">Beranda</a>
            </li>

            <li class="has-dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    Tentang Kami <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?= BASE_URL ?>/tentang/profil-sejarah.php">Profil & Sejarah</a></li>
                    <li><a href="<?= BASE_URL ?>/tentang/visi-misi.php">Visi & Misi</a></li>
                    <li><a href="<?= BASE_URL ?>/tentang/struktur-organisasi.php">Struktur Organisasi</a></li>
                    <li><a href="<?= BASE_URL ?>/tentang/departemen.php">Departemen</a></li>
                </ul>
            </li>

            <li class="<?= $current_page == 'kegiatan.php' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/kegiatan.php">Kegiatan</a>
            </li>

            <li class="<?= $current_page == 'galeri.php' ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/galeri.php">Galeri</a>
            </li>

            <li class="has-dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    Kontak <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?= BASE_URL ?>/kontak/informasi-sekretariat.php">Informasi & Sekretariat</a></li>
                    <li><a href="<?= BASE_URL ?>/kontak/media-partnership.php">Media Partnership & Kerja Sama</a></li>
                    <li><a href="<?= BASE_URL ?>/kontak/suara-mahasiswa.php">Suara Mahasiswa</a></li>
                </ul>
            </li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a></li>
            <?php endif; ?>
        </ul>

    </div>
</header>