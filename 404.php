<?php
// Jika diakses dari folder admin, pakai layout admin
$is_admin = strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false;

if ($is_admin):
    require_once __DIR__ . '/includes/cek_login.php';
    require_once __DIR__ . '/config/koneksi.php';
    include __DIR__ . '/admin/includes/header.php';
?>
    <div style="text-align:center;padding:80px 20px;">
        <i class="fa-solid fa-map-signs" style="font-size:4rem;color:var(--text-muted);margin-bottom:20px;"></i>
        <h1 style="font-size:2rem;margin-bottom:10px;color:var(--hmti-text);">404</h1>
        <p style="color:var(--text-muted);font-size:1.1rem;">Halaman tidak ditemukan</p>
        <p style="color:var(--text-muted);margin-top:8px;">Halaman yang kamu cari mungkin sudah dipindah atau tidak tersedia.</p>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn-admin btn-admin-primary" style="margin-top:24px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
<?php
    include __DIR__ . '/admin/includes/footer.php';
else:
    include __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/global.css') ?>">

<section class="notfound">
    <div class="notfound-scene" aria-hidden="true">
        <span class="nf-star" style="--d:0s">&#10022;</span>
        <span class="nf-star" style="--d:0.8s">&#10038;</span>
        <span class="nf-star" style="--d:1.6s">&#10022;</span>
        <div class="nf-ship">&#128760;</div>
        <div class="nf-astro">&#128125;</div>
    </div>

    <h1 class="nf-title">Waduh, halamannya kesasar! <span aria-hidden="true">&#128760;</span></h1>
    <p class="nf-desc">
        Sepertinya halaman yang kamu cari udah kabur ke luar angkasa &mdash; atau memang nggak pernah ada.
        Tenang, kami bantu balik ke jalan yang benar.
    </p>

    <div class="notfound-actions">
        <a href="<?= BASE_URL ?>/index.php" class="btn-cta">
            <i class="fa-solid fa-house"></i> Kembali ke Beranda
        </a>
        <a href="<?= BASE_URL ?>/kegiatan.php" class="btn-ghost">
            <i class="fa-solid fa-calendar-days"></i> Lihat Kegiatan
        </a>
    </div>
</section>
<?php
    include __DIR__ . '/includes/footer.php';
endif;
?>
