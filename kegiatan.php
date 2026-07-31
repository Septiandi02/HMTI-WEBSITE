<?php
require_once 'config/koneksi.php';
include 'includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kegiatan.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/kegiatan.css') ?>">

<section class="page-banner">
    <h1>Kegiatan</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Kegiatan</div>
</section>

<section class="section">

    <?php
    // ---------- PAGINATION ----------
    $per_halaman = 6;
    $halaman_sekarang = isset($_GET['halaman']) ? max(1, (int)$_GET['halaman']) : 1;
    $offset = ($halaman_sekarang - 1) * $per_halaman;

    // Hitung total data untuk menentukan jumlah halaman
    $total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan");
    $total_data = mysqli_fetch_assoc($total_result)['total'];
    $total_halaman = ceil($total_data / $per_halaman);

    $query = "SELECT id, judul, gambar, isi, tanggal_dibuat 
              FROM kegiatan 
              ORDER BY tanggal_dibuat DESC 
              LIMIT $per_halaman OFFSET $offset";
    $result = mysqli_query($koneksi, $query);
    ?>

    <div class="kegiatan-grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $cuplikan = substr(strip_tags($row['isi']), 0, 110) . '...';
                $tanggal = date('d M Y', strtotime($row['tanggal_dibuat']));
                $gambar = !empty($row['gambar']) ? BASE_URL . '/assets/img/kegiatan/' . $row['gambar'] : BASE_URL . '/assets/img/kegiatan/default.svg';
            ?>
                <a href="<?= BASE_URL ?>/kegiatan-detail.php?id=<?= $row['id'] ?>" class="kegiatan-card">
                    <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($row['judul']) ?>" loading="lazy">
                    <div class="kegiatan-card-body">
                        <span class="tanggal"><?= $tanggal ?></span>
                        <h3><?= htmlspecialchars($row['judul']) ?></h3>
                        <p><?= e($cuplikan) ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-state">Belum ada kegiatan yang dipublikasikan.</p>
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

<?php include 'includes/footer.php'; ?>