<?php
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kontak.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/kontak.css') ?>">

<section class="page-banner">
    <span>Kontak</span>
    <h1>Media Partnership & Kerja Sama</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Media Partnership</div>
</section>

<section class="section medpar-section">

    <div class="medpar-card">
        <i class="fa-solid fa-handshake medpar-icon"></i>
        <h2>Ajukan Kerja Sama dengan HMTI</h2>
        <p>
            HMTI terbuka untuk kerja sama media partnership dan kolaborasi kegiatan.
            Sebelum mengajukan, pastikan kamu sudah membaca SOP dan syarat & ketentuan yang berlaku.
        </p>

        <a href="#" target="_blank" class="btn-outline">
            <i class="fa-solid fa-file-lines"></i> Lihat SOP & Syarat Ketentuan
        </a>

        <a href="#" target="_blank" class="btn-cta">
            AJUKAN MEDIA PARTNERSHIP <i class="fa-solid fa-arrow-right"></i>
        </a>

        <div class="medpar-contact">
            <p>Ada pertanyaan lebih lanjut?</p>
            <a href="https://wa.me/6280000000001" target="_blank">
                <i class="fa-brands fa-whatsapp"></i> Hubungi Kepala Divisi Humas & Eksternal
            </a>
        </div>
    </div>

</section>

<?php include '../includes/footer.php'; ?>