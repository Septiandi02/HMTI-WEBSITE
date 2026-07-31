<?php
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kontak.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/kontak.css') ?>">

<section class="page-banner">
    <span>Kontak</span>
    <h1>Informasi & Sekretariat</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Informasi & Sekretariat</div>
</section>

<section class="section kontak-info-section">

    <div class="kontak-info-grid">

        <div class="kontak-info-card">
            <i class="fa-solid fa-location-dot"></i>
            <h3>Alamat Sekretariat</h3>
            <p>Gedung Dharma Pengabdian Lantai 1, Kampus Terpadu Universitas Bangka Belitung</p>
        </div>

        <div class="kontak-info-card">
            <i class="fa-solid fa-envelope"></i>
            <h3>Email Resmi</h3>
            <p><a href="mailto:hmti@ubb.ac.id">hmti@ubb.ac.id</a></p>
        </div>

        <div class="kontak-info-card">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>WhatsApp Admin</h3>
            <p><a href="https://wa.me/6280000000000" target="_blank">+62 800-0000-0000</a></p>
        </div>

    </div>

    <div class="kontak-social-row">
        <a href="#" target="_blank" class="social-btn"><i class="fa-brands fa-instagram"></i> Instagram</a>
        <a href="#" target="_blank" class="social-btn"><i class="fa-brands fa-tiktok"></i> TikTok</a>
        <a href="#" target="_blank" class="social-btn"><i class="fa-brands fa-youtube"></i> YouTube</a>
    </div>

    <div class="maps-wrapper">
        <iframe
            src="https://www.google.com/maps?q=Universitas+Bangka+Belitung&output=embed"
            width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

</section>

<?php include '../includes/footer.php'; ?>