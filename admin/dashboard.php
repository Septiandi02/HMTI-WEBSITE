<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong> 👋</p>
</div>

<?php
// Ambil statistik ringkas dari tiap tabel untuk ditampilkan di kartu
$stat_kegiatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan"))['total'];
$stat_galeri = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM galeri"))['total'];
$stat_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota_organisasi"))['total'];
$stat_suara = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM suara_mahasiswa"))['total'];
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background-color:var(--hmti-bg); color:var(--hmti-dark);">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <span class="stat-number" data-count="<?= (int)$stat_kegiatan ?>">0</span>
            <span class="stat-label">Kegiatan</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background-color:var(--hmti-bg); color:var(--hmti-dark);">
            <i class="fa-solid fa-images"></i>
        </div>
        <div class="stat-info">
            <span class="stat-number" data-count="<?= (int)$stat_galeri ?>">0</span>
            <span class="stat-label">Foto Galeri</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background-color:var(--hmti-bg); color:var(--hmti-dark);">
            <i class="fa-solid fa-people-group"></i>
        </div>
        <div class="stat-info">
            <span class="stat-number" data-count="<?= (int)$stat_anggota ?>">0</span>
            <span class="stat-label">Anggota Organisasi</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background-color:var(--hmti-bg); color:var(--hmti-dark);">
            <i class="fa-solid fa-comment-dots"></i>
        </div>
        <div class="stat-info">
            <span class="stat-number" data-count="<?= (int)$stat_suara ?>">0</span>
            <span class="stat-label">Suara Mahasiswa Masuk</span>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h2>Aksi Cepat</h2>
    <div class="quick-actions-grid">
        <a href="<?= BASE_URL ?>/admin/kegiatan/tambah.php" class="quick-action-btn">
            <i class="fa-solid fa-plus"></i> Tambah Kegiatan
        </a>
        <a href="<?= BASE_URL ?>/admin/galeri/index.php" class="quick-action-btn">
            <i class="fa-solid fa-upload"></i> Upload Foto Galeri
        </a>
        <a href="<?= BASE_URL ?>/admin/struktur-organisasi/index.php" class="quick-action-btn">
            <i class="fa-solid fa-user-plus"></i> Kelola Anggota
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>