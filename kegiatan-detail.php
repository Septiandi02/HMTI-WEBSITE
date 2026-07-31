<?php
require_once 'config/koneksi.php';
include 'includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kegiatan.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/kegiatan.css') ?>">

<?php
// Ambil ID dari URL, pastikan berupa angka (mencegah SQL Injection)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT k.id, k.judul, k.gambar, k.isi, k.tanggal_dibuat, u.nama AS penulis
          FROM kegiatan k
          LEFT JOIN users u ON k.penulis_id = u.id
          WHERE k.id = $id";
$result = mysqli_query($koneksi, $query);
$kegiatan = $result ? mysqli_fetch_assoc($result) : null;
?>

<?php if ($kegiatan): ?>

    <?php
    $gambar = !empty($kegiatan['gambar']) ? BASE_URL . '/assets/img/kegiatan/' . $kegiatan['gambar'] : BASE_URL . '/assets/img/kegiatan/default.svg';
    $tanggal = date('d F Y', strtotime($kegiatan['tanggal_dibuat']));
    $penulis = $kegiatan['penulis'] ?? 'Admin HMTI';
    ?>

    <section class="detail-hero" style="background-image: url('<?= $gambar ?>');">
        <div class="detail-hero-overlay">
            <div class="detail-hero-content">
                <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / <a href="<?= BASE_URL ?>/kegiatan.php">Kegiatan</a> / <?= htmlspecialchars($kegiatan['judul']) ?></div>
                <h1><?= htmlspecialchars($kegiatan['judul']) ?></h1>
                <div class="detail-meta">
                    <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($penulis) ?></span>
                    <span><i class="fa-solid fa-calendar"></i> <?= $tanggal ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="section detail-content">
        <?= bersihkan_html($kegiatan['isi'] ?? '') ?>

        <a href="<?= BASE_URL ?>/kegiatan.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Kegiatan
        </a>
    </section>

<?php else: ?>

    <section class="section" style="text-align:center; padding: 100px 20px;">
        <h2>Kegiatan tidak ditemukan</h2>
        <p style="margin: 16px 0; color:var(--text-secondary);">Artikel yang kamu cari mungkin sudah dihapus atau link-nya salah.</p>
        <a href="<?= BASE_URL ?>/kegiatan.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Kegiatan
        </a>
    </section>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>