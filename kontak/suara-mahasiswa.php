<?php
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kontak.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/kontak.css') ?>">

<section class="page-banner">
    <span>Kontak</span>
    <h1>Suara Mahasiswa</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Suara Mahasiswa</div>
</section>

<section class="section suara-section">

    <div class="suara-card">

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> Aspirasimu berhasil dikirim. Terima kasih sudah bersuara!
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> Aspirasi gagal dikirim, isi dulu kolom aspirasinya ya.
            </div>
        <?php endif; ?>

        <p class="suara-guarantee">
            <i class="fa-solid fa-shield-halved"></i>
            Aspirasimu dikelola oleh Divisi Advokasi & Kesejahteraan Mahasiswa Departemen PSDM
            serta dijaga kerahasiaannya.
        </p>

        <form action="proses_suara.php" method="POST" id="formSuara">
            <?= csrf_field() ?>

            <div class="form-group" id="groupNama">
                <label for="nama">Nama</label>
                <input type="text" name="nama" id="nama" placeholder="Nama lengkap kamu">
            </div>

            <div class="form-group" id="groupNim">
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" placeholder="Nomor Induk Mahasiswa">
            </div>

            <div class="form-checkbox">
                <input type="checkbox" name="anonim" id="anonim" value="1">
                <label for="anonim">Kirim secara Anonim (nama & NIM tidak akan disimpan)</label>
            </div>

            <div class="form-group">
                <label for="isi_aspirasi">Aspirasi / Kritik / Masukan <span class="required">*</span></label>
                <textarea name="isi_aspirasi" id="isi_aspirasi" rows="6" placeholder="Sampaikan kritik terhadap kebijakan fakultas, dosen, fasilitas kampus yang rusak, atau kinerja internal HMTI..." required></textarea>
            </div>

            <button type="submit" class="btn-cta full-width">
                <i class="fa-solid fa-paper-plane"></i> Kirim Aspirasi
            </button>

        </form>
    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxAnonim = document.getElementById('anonim');
    const groupNama = document.getElementById('groupNama');
    const groupNim = document.getElementById('groupNim');
    const inputNama = document.getElementById('nama');
    const inputNim = document.getElementById('nim');

    checkboxAnonim.addEventListener('change', function () {
        const isAnonim = this.checked;

        // Sembunyikan field nama & NIM kalau anonim dicentang, dan kosongkan isinya
        groupNama.style.display = isAnonim ? 'none' : 'block';
        groupNim.style.display = isAnonim ? 'none' : 'block';

        if (isAnonim) {
            inputNama.value = '';
            inputNim.value = '';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>