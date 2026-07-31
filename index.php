<?php
require_once 'config/koneksi.php';
include 'includes/header.php';

// Sapaan ramah yang menyesuaikan jam (sentuhan manusiawi di hero)
$jam = (int)date('G');
if ($jam >= 5 && $jam < 11)      { $sapaan = 'Selamat pagi'; }
elseif ($jam >= 11 && $jam < 15) { $sapaan = 'Selamat siang'; }
elseif ($jam >= 15 && $jam < 18) { $sapaan = 'Selamat sore'; }
else                             { $sapaan = 'Selamat malam'; }
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/beranda.css?v=<?= (int)@filemtime(__DIR__ . '/assets/css/beranda.css') ?>">

<!-- ================= HERO SECTION ================= -->
<section class="hero">
    <div class="hero-content">
        <h1>Himpunan Mahasiswa Teknologi Informasi</h1>
        <p><span class="hero-greeting"><?= $sapaan ?>! <span class="wave-hand" aria-hidden="true">&#128075;</span></span> Selamat datang di website resmi HMTI Universitas Bangka Belitung. Di sini kamu bisa melihat kegiatan, mengenal organisasi, dan menyampaikan aspirasi.</p>
        <div class="hero-actions">
            <a href="<?= BASE_URL ?>/kegiatan.php" class="btn-hero">
                <i class="fa-solid fa-arrow-right"></i> Kegiatan Kami
            </a>
            <a href="<?= BASE_URL ?>/tentang/profil-sejarah.php" class="btn-hero-outline">
                Kenali HMTI
            </a>
        </div>
    </div>
    <a href="#kegiatan-terbaru" class="scroll-indicator" aria-label="Gulir ke bawah">
        <i class="fa-solid fa-chevron-down"></i>
    </a>
</section>

<!-- ================= KEGIATAN TERBARU ================= -->
<section class="section" id="kegiatan-terbaru">
    <div class="section-heading">
        <h2>Kegiatan Terbaru</h2>
    </div>

    <div class="kegiatan-grid">
        <?php
        // Ambil 3 kegiatan terbaru dari database
        $query = "SELECT id, judul, gambar, isi, tanggal_dibuat 
                  FROM kegiatan 
                  ORDER BY tanggal_dibuat DESC 
                  LIMIT 3";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
                // Ambil cuplikan isi (100 karakter pertama) untuk preview
                $cuplikan = substr(strip_tags($row['isi']), 0, 100) . '...';
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
        <?php
            endwhile;
        else:
        ?>
            <p class="empty-state">Belum ada kegiatan yang dipublikasikan.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ================= DEPARTEMEN PREVIEW ================= -->
<section class="section" style="background-color: var(--section-alt-bg);">
    <div class="section-heading">
        <h2>Departemen HMTI</h2>
    </div>

    <div class="departemen-grid">
        <a href="<?= BASE_URL ?>/tentang/departemen.php?dept=hldo" class="departemen-card">
            <i class="fa-solid fa-people-group"></i>
            <h4>HLDO</h4>
        </a>
        <a href="<?= BASE_URL ?>/tentang/departemen.php?dept=ristek" class="departemen-card">
            <i class="fa-solid fa-laptop-code"></i>
            <h4>RISTEK</h4>
        </a>
        <a href="<?= BASE_URL ?>/tentang/departemen.php?dept=psdm" class="departemen-card">
            <i class="fa-solid fa-user-graduate"></i>
            <h4>PSDM</h4>
        </a>
        <a href="<?= BASE_URL ?>/tentang/departemen.php?dept=kominfo" class="departemen-card">
            <i class="fa-solid fa-bullhorn"></i>
            <h4>KOMINFO</h4>
        </a>
        <a href="<?= BASE_URL ?>/tentang/departemen.php?dept=mikat" class="departemen-card">
            <i class="fa-solid fa-handshake"></i>
            <h4>MIKAT</h4>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>