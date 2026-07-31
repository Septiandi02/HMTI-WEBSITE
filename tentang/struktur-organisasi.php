<?php
require_once '../config/koneksi.php';
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tentang.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/tentang.css') ?>">

<section class="page-banner">
    <span>Tentang Kami</span>
    <h1>Struktur Organisasi</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Struktur Organisasi</div>
</section>

<section class="section">

    <!-- ================= PEJABAT TERAS ================= -->
    <div class="struktur-group">
        <span class="struktur-group-title">Pejabat Teras</span>

        <div class="carousel" data-carousel>
            <button class="carousel-arrow prev" aria-label="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>

            <div class="carousel-viewport">
                <div class="carousel-track">
                <?php
                $query_pejabat = "SELECT nama, jabatan, foto 
                                   FROM anggota_organisasi 
                                   WHERE kategori = 'pejabat_teras' 
                                   ORDER BY urutan ASC";
                $result_pejabat = mysqli_query($koneksi, $query_pejabat);

                if ($result_pejabat && mysqli_num_rows($result_pejabat) > 0):
                    while ($row = mysqli_fetch_assoc($result_pejabat)):
                        $foto = !empty($row['foto']) ? BASE_URL . '/assets/img/struktur/' . $row['foto'] : BASE_URL . '/assets/img/struktur/default.svg';
                ?>
                    <div class="carousel-card">
                        <img src="<?= $foto ?>" alt="<?= htmlspecialchars($row['nama']) ?>" loading="lazy">
                        <div class="carousel-card-body">
                            <h4><?= htmlspecialchars($row['nama']) ?></h4>
                            <p><?= htmlspecialchars($row['jabatan']) ?></p>
                        </div>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="empty-state">Data Pejabat Teras belum tersedia.</p>
                <?php endif; ?>
                </div>
            </div>

            <button class="carousel-arrow next" aria-label="Selanjutnya"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    <!-- ================= TIAP DEPARTEMEN ================= -->
    <?php
    $query_departemen = "SELECT id, nama_departemen FROM departemen ORDER BY id ASC";
    $result_departemen = mysqli_query($koneksi, $query_departemen);

    if ($result_departemen && mysqli_num_rows($result_departemen) > 0):
        while ($dept = mysqli_fetch_assoc($result_departemen)):
    ?>
        <div class="struktur-group">
            <span class="struktur-group-title">Departemen <?= htmlspecialchars($dept['nama_departemen']) ?></span>

            <div class="carousel" data-carousel>
                <button class="carousel-arrow prev" aria-label="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>

                <div class="carousel-viewport">
                    <div class="carousel-track">
                    <?php
                    $query_anggota = "SELECT nama, jabatan, foto 
                                       FROM anggota_organisasi 
                                       WHERE kategori = 'departemen' AND departemen_id = " . (int)$dept['id'] . "
                                       ORDER BY urutan ASC";
                    $result_anggota = mysqli_query($koneksi, $query_anggota);

                    if ($result_anggota && mysqli_num_rows($result_anggota) > 0):
                        while ($row = mysqli_fetch_assoc($result_anggota)):
                            $foto = !empty($row['foto']) ? BASE_URL . '/assets/img/struktur/' . $row['foto'] : BASE_URL . '/assets/img/struktur/default.svg';
                    ?>
                        <div class="carousel-card">
                            <img src="<?= $foto ?>" alt="<?= htmlspecialchars($row['nama']) ?>" loading="lazy">
                            <div class="carousel-card-body">
                                <h4><?= htmlspecialchars($row['nama']) ?></h4>
                                <p><?= htmlspecialchars($row['jabatan']) ?></p>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <p class="empty-state">Data anggota belum tersedia.</p>
                    <?php endif; ?>
                    </div>
                </div>

                <button class="carousel-arrow next" aria-label="Selanjutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    <?php
        endwhile;
    endif;
    ?>

</section>

<script src="<?= BASE_URL ?>/assets/js/slider.js?v=<?= (int)@filemtime(__DIR__ . '/../assets/js/slider.js') ?>"></script>
<?php include '../includes/footer.php'; ?>