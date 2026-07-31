<?php
require_once '../config/koneksi.php';
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tentang.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/tentang.css') ?>">

<section class="page-banner">
    <span>Tentang Kami</span>
    <h1>Visi & Misi</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Visi & Misi</div>
</section>

<!-- ================= VISI ================= -->
<section class="section">
    <div class="visi-card">
        <h2>Visi</h2>
        <p>
            Menjadikan Himpunan Mahasiswa Teknologi Informasi sebagai organisasi yang unggul,
            profesional, dan berperan aktif dalam mengembangkan potensi mahasiswa di bidang
            teknologi informasi, serta memberikan kontribusi nyata bagi kampus dan masyarakat.
        </p>
    </div>
</section>

<!-- ================= MISI ================= -->
<section class="section misi-section">
    <div class="misi-wrapper">
        <div class="misi-title">
            <h2>Misi</h2>
        </div>

        <div class="misi-radial">
            <div class="misi-grid">
                <img src="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png" alt="Logo HMTI" class="misi-logo">

                <div class="misi-item">
                    <span class="misi-number">1</span>
                    <p>Memperkuat kolaborasi internal dan eksternal melalui kerja sama strategis dan menjaga budaya organisasi yang inklusif dan suportif.</p>
                </div>
                <div class="misi-item">
                    <span class="misi-number">2</span>
                    <p>Mengembangkan sistem organisasi yang tangguh, berkelanjutan, dan adaptif terhadap perubahan.</p>
                </div>
                <div class="misi-item">
                    <span class="misi-number">3</span>
                    <p>Menerapkan standar kerja dan etika organisasi yang profesional.</p>
                </div>
                <div class="misi-item">
                    <span class="misi-number">4</span>
                    <p>Mendorong inisiatif dan kontribusi positif untuk masyarakat.</p>
                </div>
                <div class="misi-item">
                    <span class="misi-number">5</span>
                    <p>Memberikan ruang untuk pengembangan kapasitas dan kreativitas anggota.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>