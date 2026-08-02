/* ============================================================
   UPLOAD-COMPRESS.JS
   Kompresi & resize gambar DI BROWSER sebelum diupload.

   Kenapa ini dibuat? Saat website di-hosting (cPanel), kecepatan
   upload tergantung koneksi internet admin. Foto dari HP bisa
   5-20MB; mengirimnya ke server butuh waktu sangat lama. Dengan
   kompresi di browser, foto dikecilkan jadi sekitar 150-500KB
   DULU, baru dikirim. Hasilnya upload jadi jauh lebih cepat
   (bisa 10-50x lipat) dan server tidak perlu repot mengompres.

   Cara kerja:
   - Otomatis aktif untuk semua input file gambar di halaman admin
     (kecuali diberi atribut data-no-compress).
   - Foto di-resize ke maks 1920px dan diubah ke JPEG (kualitas 82).
   - File asli DIGANTI di input (via DataTransfer), jadi submit
     form biasa tetap jalan tanpa perlu mengubah alur server.
   - File yang sudah kecil dilewati apa adanya.
   - Halaman galeri memakai API window.HMTIUpload untuk upload
     banyak foto sekaligus dengan progress bar.
   ============================================================ */
(function () {
    "use strict";

    var MAX_DIM = 1920; // dimensi maksimal piksel
    var QUALITY = 0.82; // kualitas JPEG (0-1)
    var SKIP_BYTE = 400 * 1024; // file <= ini tidak diolah ulang
    var RESIZE_MIN = 1500; // baru di-resize kalau >= dimensi ini
    var SIZE_TO_RESIZE = 1200 * 1024; // ...dan ukurannya lebih besar dari ini

    function formatBytes(b) {
        if (b >= 1024 * 1024) return (b / (1024 * 1024)).toFixed(1) + " MB";
        if (b >= 1024) return Math.round(b / 1024) + " KB";
        return b + " B";
    }

    /**
     * Kompres satu file gambar.
     * Selalu resolve (tidak pernah reject) supaya alur tidak putus;
     * kalau gagal atau tidak perlu, file asli dikembalikan.
     * @returns Promise<{file:File, originalSize:number, diproses:boolean}>
     */
    function kompresFile(file) {
        return new Promise(function (resolve) {
            var asli = file.size;
            var gagal = function () {
                resolve({ file: file, originalSize: asli, diproses: false });
            };

            // Hanya format yang bisa di-decode canvas
            if (!/^image\/(jpeg|png|webp)$/i.test(file.type)) return gagal();
            // File kecil tidak perlu diolah
            if (file.size <= SKIP_BYTE) return gagal();

            var img = new Image();
            var url = URL.createObjectURL(file);

            img.onload = function () {
                var w = img.naturalWidth;
                var h = img.naturalHeight;
                if (!w || !h) {
                    URL.revokeObjectURL(url);
                    return gagal();
                }

                // Dimensi sudah pas dan ukurannya tidak gede-gede amat
                if (
                    w <= RESIZE_MIN &&
                    h <= RESIZE_MIN &&
                    file.size <= SIZE_TO_RESIZE
                ) {
                    URL.revokeObjectURL(url);
                    return resolve({
                        file: file,
                        originalSize: asli,
                        diproses: false,
                    });
                }

                var ratio = Math.min(1, MAX_DIM / Math.max(w, h));
                var nw = Math.max(1, Math.round(w * ratio));
                var nh = Math.max(1, Math.round(h * ratio));

                var canvas = document.createElement("canvas");
                canvas.width = nw;
                canvas.height = nh;
                var ctx = canvas.getContext("2d");
                // Latar putih agar PNG transparan tidak menjadi hitam di JPEG
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, nw, nh);
                ctx.drawImage(img, 0, 0, nw, nh);

                try {
                    canvas.toBlob(
                        function (blob) {
                            URL.revokeObjectURL(url);
                            if (blob && blob.size > 0 && blob.size < asli) {
                                var nama =
                                    file.name.replace(/\.[^.]+$/i, "") + ".jpg";
                                var baru = new File([blob], nama, {
                                    type: "image/jpeg",
                                    lastModified: Date.now(),
                                });
                                resolve({
                                    file: baru,
                                    originalSize: asli,
                                    diproses: true,
                                });
                            } else {
                                resolve({
                                    file: file,
                                    originalSize: asli,
                                    diproses: false,
                                });
                            }
                        },
                        "image/jpeg",
                        QUALITY,
                    );
                } catch (err) {
                    URL.revokeObjectURL(url);
                    gagal();
                }
            };

            img.onerror = function () {
                URL.revokeObjectURL(url);
                gagal();
            };
            img.src = url;
        });
    }

    function setInputFiles(input, files) {
        if (typeof DataTransfer === "undefined") return false;
        try {
            var dt = new DataTransfer();
            files.forEach(function (f) {
                dt.items.add(f);
            });
            input.files = dt.files;
            return true;
        } catch (err) {
            return false;
        }
    }

    function buatStatus(input) {
        var el = document.createElement("div");
        el.className = "upload-compress-status";
        el.style.cssText = "font-size:0.8rem;margin-top:6px;color:#8a8f98;";
        if (input.parentNode) input.parentNode.appendChild(el);
        return el;
    }

    function prosesInput(input, status) {
        var files = Array.prototype.slice.call(input.files || []);
        if (!files.length) {
            if (status) status.textContent = "";
            return;
        }

        if (status) status.textContent = "Memproses gambar di perangkat...";

        Promise.all(files.map(kompresFile)).then(function (hasil) {
            var totalDiproses = 0;
            var hemat = 0;
            var fileBaru = hasil.map(function (r) {
                if (r.diproses) {
                    totalDiproses++;
                    hemat += r.originalSize - r.file.size;
                }
                return r.file;
            });

            var tertukar = setInputFiles(input, fileBaru);
            if (!tertukar && totalDiproses > 0) {
                if (status) {
                    status.textContent =
                        "Gambar siap diupload (kompresi otomatis tidak didukung browser ini).";
                }
                return;
            }

            if (status) {
                if (totalDiproses === 0) {
                    status.textContent =
                        "File gambar sudah optimal, siap diupload.";
                } else {
                    status.innerHTML =
                        "&#10003; " +
                        totalDiproses +
                        " foto dikecilkan, hemat ~" +
                        formatBytes(hemat) +
                        " (contoh: " +
                        formatBytes(hasil[0].originalSize) +
                        " → " +
                        formatBytes(fileBaru[0].size) +
                        ").";
                }
            }
        });
    }

    function init() {
        var inputs = document.querySelectorAll(
            'input[type="file"][accept*="image" i]:not([data-no-compress])',
        );

        Array.prototype.forEach.call(inputs, function (input) {
            var status = buatStatus(input);
            input.addEventListener("change", function () {
                prosesInput(input, status);
            });
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    // API publik, dipakai halaman galeri untuk multi-upload + progress
    window.HMTIUpload = {
        kompresFile: kompresFile,
        setInputFiles: setInputFiles,
        formatBytes: formatBytes,
    };
})();
