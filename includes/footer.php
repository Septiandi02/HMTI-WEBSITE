<footer class="site-footer">
    <div class="footer-container">

        <div class="footer-col">
            <div class="footer-brand">
                <img src="<?= BASE_URL ?>/assets/img/logo/logo-hmti.png" alt="Logo HMTI">
                <span>HMTI</span>
            </div>
            <p class="footer-tagline">Kabinet Nawasena</p>
        </div>

        <div class="footer-col">
            <h4>Quick Link</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php">Beranda</a></li>
                <li><a href="<?= BASE_URL ?>/tentang/departemen.php">Departemen</a></li>
                <li><a href="<?= BASE_URL ?>/kegiatan.php">Kegiatan</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Informasi</h4>
            <ul>
                <li>hmti@ubb.ac.id</li>
                <li>Gedung Dharma Pengabdian Lantai 1, Kampus Terpadu Universitas Bangka Belitung</li>
            </ul>
            <div class="footer-social">
                <a href="#" target="_blank" aria-label="Instagram HMTI"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank" aria-label="TikTok HMTI"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#" target="_blank" aria-label="YouTube HMTI"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <span>&copy; 2026 Himpunan Mahasiswa Teknologi Informasi UBB</span>
        <span class="footer-clock" id="footerClock" aria-hidden="true"></span>
    </div>
</footer>

<!-- Bilah progres scroll (di atas halaman) -->
<div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

<!-- Tombol kembali ke atas (dengan cincin progres) -->
<button class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
    <svg class="btt-ring" viewBox="0 0 46 46" aria-hidden="true">
        <circle class="btt-ring-bg" cx="23" cy="23" r="20"></circle>
        <circle class="btt-ring-fg" cx="23" cy="23" r="20"></circle>
    </svg>
    <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- ?v=filemtime = auto cache-busting: saat file berubah, browser otomatis ambil versi baru -->
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= (int)@filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
</body>
</html>