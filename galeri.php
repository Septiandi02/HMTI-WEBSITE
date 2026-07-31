<?php
require_once 'config/koneksi.php';
include 'includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/galeri.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/galeri.css') ?>">

<section class="page-banner">
    <span>Dokumentasi</span>
    <h1>Galeri</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Galeri</div>
</section>

<section class="section">

    <?php
    // Pagination - pola sama seperti halaman Kegiatan, biar konsisten
    $per_halaman = 12;
    $halaman_sekarang = isset($_GET['halaman']) ? max(1, (int)$_GET['halaman']) : 1;
    $offset = ($halaman_sekarang - 1) * $per_halaman;

    $total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM galeri");
    $total_data = mysqli_fetch_assoc($total_result)['total'];
    $total_halaman = ceil($total_data / $per_halaman);

    $query = "SELECT id, gambar, caption FROM galeri ORDER BY tanggal_upload DESC LIMIT $per_halaman OFFSET $offset";
    $result = mysqli_query($koneksi, $query);
    ?>

    <div class="galeri-grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $gambar = BASE_URL . '/assets/img/galeri/' . $row['gambar'];
                $caption = htmlspecialchars($row['caption']);
            ?>
                <div class="galeri-item" data-img="<?= $gambar ?>" data-caption="<?= $caption ?>">
                    <img src="<?= $gambar ?>" alt="<?= $caption ?>" loading="lazy">
                    <div class="galeri-caption"><?= $caption ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-state">Belum ada foto di galeri.</p>
        <?php endif; ?>
    </div>

    <?php if ($total_halaman > 1): ?>
        <div class="pagination">
            <?php if ($halaman_sekarang > 1): ?>
                <a href="?halaman=<?= $halaman_sekarang - 1 ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                <a href="?halaman=<?= $i ?>" class="page-btn <?= $i === $halaman_sekarang ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($halaman_sekarang < $total_halaman): ?>
                <a href="?halaman=<?= $halaman_sekarang + 1 ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</section>

<!-- ================= LIGHTBOX (MODAL ZOOM) ================= -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    <img src="" alt="" id="lightboxImg">
    <p class="lightbox-caption" id="lightboxCaption"></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = document.querySelectorAll('.galeri-item');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const closeBtn = document.getElementById('lightboxClose');

    function openLightbox(item) {
        lightboxImg.src = item.dataset.img;
        lightboxCaption.textContent = item.dataset.caption;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // cegah scroll halaman belakang saat lightbox terbuka
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    items.forEach(function (item) {
        item.addEventListener('click', () => openLightbox(item));
    });

    closeBtn.addEventListener('click', closeLightbox);

    // Klik di area luar foto (background gelap) juga menutup
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });

    // Tombol Escape juga menutup
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) closeLightbox();
    });
});
</script>

<?php include 'includes/footer.php'; ?>