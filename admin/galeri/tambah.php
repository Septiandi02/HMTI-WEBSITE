<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Upload Foto</h1>
    <p>Tambahkan foto ke galeri HMTI</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'error_size'): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Ukuran file terlalu besar. Maksimal 20MB.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i> Gagal mengupload foto. Periksa format file (JPG/PNG/WebP).
    </div>
<?php endif; ?>

<form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="admin-form" id="formUpload">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="gambar">Pilih Foto <span style="color:#e05252;">*</span></label>
        <input type="file" name="gambar[]" id="gambar" accept="image/*" multiple required>
        <small style="color:#8a8f98;">Bisa pilih banyak foto sekaligus. Foto dikecilkan otomatis di perangkat sebelum diupload supaya jauh lebih cepat. Format: JPG, PNG, WebP.</small>
    </div>

    <div class="form-group">
        <label for="caption">Caption</label>
        <input type="text" name="caption" id="caption" placeholder="Caption ini akan dipakai untuk semua foto yang dipilih" maxlength="255">
    </div>

    <!-- Area progres upload banyak foto -->
    <div id="uploadProgressWrap" style="display:none;">
        <div class="upload-queue" id="uploadQueue"></div>
        <div class="upload-progress">
            <div class="upload-progress-bar" id="uploadProgressBar" style="width:0%"></div>
        </div>
        <div class="upload-progress-text" id="uploadProgressText">Menyiapkan...</div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-admin btn-admin-primary" id="btnUpload">
            <i class="fa-solid fa-upload"></i> Upload
        </button>
        <a href="index.php" class="btn-admin" style="background:#e0e3e7;color:#4a4d54;">
            Batal
        </a>
    </div>
</form>

<script>
function initUploadGaleri() {
    var form = document.getElementById('formUpload');
    if (!form) return;
    var input = document.getElementById('gambar');
    var caption = document.getElementById('caption');
    var btn = document.getElementById('btnUpload');
    var wrap = document.getElementById('uploadProgressWrap');
    var queue = document.getElementById('uploadQueue');
    var bar = document.getElementById('uploadProgressBar');
    var txt = document.getElementById('uploadProgressText');
    var tokenEl = form.querySelector('input[name="csrf_token"]');

    // Kalau skrip kompresi belum siap, biarkan submit biasa berjalan
    if (!window.HMTIUpload) return;

    form.addEventListener('submit', function (ev) {
        var files = Array.prototype.slice.call(input.files || []);
        if (!files.length) {
            alert('Pilih minimal satu foto dulu.');
            ev.preventDefault();
            return;
        }

        ev.preventDefault();
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengupload...';
        wrap.style.display = 'block';
        queue.innerHTML = '';

        var token = tokenEl ? tokenEl.value : '';
        var cap = caption.value.trim();
        var total = files.length;
        var rows = [];

        // Tampilkan antrean file
        files.forEach(function (f, i) {
            var row = document.createElement('div');
            row.className = 'upload-queue-item';
            row.innerHTML = '<span class="upload-queue-name"></span>' +
                '<span class="upload-queue-status">Menunggu</span>';
            row.querySelector('.upload-queue-name').textContent = (i + 1) + '. ' + f.name;
            rows.push(row);
            queue.appendChild(row);
        });

        var selesai = 0;
        var sukses = 0;
        var gagal = 0;

        function updateProgress(persen, teks) {
            bar.style.width = persen + '%';
            txt.textContent = teks;
        }

        function kirimSatu(file, idx) {
            return new Promise(function (resolve) {
                rows[idx].querySelector('.upload-queue-status').textContent = 'Mengecilkan...';
                window.HMTIUpload.kompresFile(file).then(function (hasil) {
                    if (hasil.diproses) {
                        rows[idx].querySelector('.upload-queue-status').textContent =
                            'Dikecilkan (' + window.HMTIUpload.formatBytes(hasil.file.size) + ')';
                    }

                    var fd = new FormData();
                    fd.append('gambar[]', hasil.file);
                    if (cap) fd.append('caption', cap);
                    fd.append('csrf_token', token);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'proses_tambah.php');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.upload.onprogress = function (e) {
                        if (e.lengthComputable) {
                            var p = e.loaded / e.total;
                            updateProgress(((selesai + p) / total) * 100,
                                'Mengupload ' + (idx + 1) + '/' + total + ' (' +
                                Math.round(p * 100) + '%)');
                        }
                    };

                    xhr.onload = function () {
                        var ok = false;
                        try {
                            var data = JSON.parse(xhr.responseText);
                            ok = data && data.success;
                        } catch (err) {
                            ok = xhr.status === 200;
                        }
                        if (ok) { sukses++; }
                        else { gagal++; }
                        selesai++;
                        rows[idx].querySelector('.upload-queue-status').textContent =
                            ok ? 'Berhasil' : 'Gagal';
                        updateProgress((selesai / total) * 100, selesai + '/' + total + ' selesai');
                        resolve();
                    };

                    xhr.onerror = function () {
                        gagal++;
                        selesai++;
                        rows[idx].querySelector('.upload-queue-status').textContent = 'Gagal (jaringan)';
                        updateProgress((selesai / total) * 100, 'Terjadi kesalahan jaringan');
                        resolve();
                    };

                    xhr.send(fd);
                });
            });
        }

        // Upload berurutan supaya progress akurat dan tidak membebani server
        var rantai = Promise.resolve();
        files.forEach(function (f, i) {
            rantai = rantai.then(function () { return kirimSatu(f, i); });
        });

        rantai.then(function () {
            if (sukses > 0) {
                window.location.href = 'index.php?status=tambah_sukses&n=' + sukses;
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-upload"></i> Upload';
                txt.textContent = 'Semua foto gagal diupload. Periksa format/ukuran lalu coba lagi.';
                bar.style.width = '100%';
                bar.style.background = '#b23b3b';
            }
        });
    });
}
// Skrip kompresi dimuat di footer; jalankan setelah window load
if (document.readyState === 'complete') {
    initUploadGaleri();
} else {
    window.addEventListener('load', initUploadGaleri);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
