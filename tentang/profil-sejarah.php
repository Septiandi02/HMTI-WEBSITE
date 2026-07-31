<?php
require_once '../config/koneksi.php';
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tentang.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/tentang.css') ?>">

<section class="page-banner">
    <span>Tentang Kami</span>
    <h1>Profil & Sejarah</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Profil & Sejarah</div>
</section>

<section class="section profil-sejarah">

    <div class="konten-block">
        <h3>Profil</h3>
        <p>
            Himpunan Mahasiswa Teknologi Informasi (HMTI) adalah organisasi kemahasiswaan
            di bawah Program Studi Teknologi Informasi, Universitas Bangka Belitung, yang
            menjadi wadah bagi mahasiswa untuk mengembangkan potensi diri, baik dalam bidang
            akademik, non-akademik, maupun soft skill kepemimpinan dan organisasi.
        </p>
    </div>

    <div class="konten-block">
        <h3>Sejarah</h3>
        <p>
            HMTI dibentuk sebagai respons atas kebutuhan mahasiswa Teknologi Informasi akan
            wadah yang dapat menampung aspirasi, mengembangkan kreativitas, serta menjalin
            silaturahmi antar mahasiswa. Sejak awal berdirinya, HMTI terus berkembang dan
            berperan aktif dalam berbagai kegiatan kampus maupun luar kampus.
        </p>
    </div>

</section>

<?php include '../includes/footer.php'; ?>