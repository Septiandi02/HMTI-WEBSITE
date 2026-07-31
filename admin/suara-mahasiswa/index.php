<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Suara Mahasiswa</h1>
    <p>Daftar aspirasi, kritik, dan masukan yang masuk</p>
</div>

<?php
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'hapus_sukses': $msg = 'Aspirasi berhasil dihapus.'; break;
        case 'error':         $msg = 'Terjadi kesalahan.'; $tipe = 'error'; break;
    }
    if ($msg):
?>
    <div class="admin-alert admin-alert-<?= $tipe ?>">
        <i class="fa-solid <?= $tipe === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <?= $msg ?>
    </div>
<?php
    endif;
endif;

// Pagination
$per_halaman = 15;
$hal = isset($_GET['hal']) ? max(1, (int)$_GET['hal']) : 1;
$offset = ($hal - 1) * $per_halaman;

$total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM suara_mahasiswa");
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_hal = ceil($total_data / $per_halaman);

$query = "SELECT id, nama, nim, anonim, isi_aspirasi, tanggal_kirim 
          FROM suara_mahasiswa 
          ORDER BY tanggal_kirim DESC 
          LIMIT $per_halaman OFFSET $offset";
$result = mysqli_query($koneksi, $query);
?>

<div class="admin-toolbar">
    <span style="color:#8a8f98;font-size:0.9rem;">Total: <?= $total_data ?> aspirasi</span>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Status</th>
                <th>Isi Aspirasi</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $tanggal = date('d M Y, H:i', strtotime($row['tanggal_kirim']));
                    $nama_tampil = $row['anonim'] ? '<em>Anonim</em>' : htmlspecialchars($row['nama'] ?? '-');
                    $nim_tampil = $row['anonim'] ? '-' : htmlspecialchars($row['nim'] ?? '-');
                    $status_badge = $row['anonim']
                        ? '<span style="background:#9aa0a8;color:#fff;padding:2px 10px;border-radius:12px;font-size:0.75rem;font-weight:600;">Anonim</span>'
                        : '<span style="background:#1e7e42;color:#fff;padding:2px 10px;border-radius:12px;font-size:0.75rem;font-weight:600;">Teridentifikasi</span>';
                    $cuplikan = htmlspecialchars(substr($row['isi_aspirasi'], 0, 120));
                    if (strlen($row['isi_aspirasi']) > 120) $cuplikan .= '...';
                ?>
                    <tr>
                        <td><?= $nama_tampil ?></td>
                        <td><?= $nim_tampil ?></td>
                        <td><?= $status_badge ?></td>
                        <td style="max-width:350px;word-break:break-word;"><?= nl2br($cuplikan) ?></td>
                        <td><?= $tanggal ?></td>
                        <td class="td-actions">
                            <button type="button" class="btn-admin btn-admin-primary btn-admin-sm" title="Lihat Detail" onclick="lihatAspirasi(<?= $row['id'] ?>)">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus aspirasi ini?')">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#8a8f98;">
                        Belum ada aspirasi yang masuk.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_hal > 1): ?>
    <div class="pagination" style="margin-top:24px;justify-content:center;">
        <?php if ($hal > 1): ?>
            <a href="?hal=<?= $hal - 1 ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_hal; $i++): ?>
            <a href="?hal=<?= $i ?>" class="page-btn <?= $i === $hal ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($hal < $total_hal): ?>
            <a href="?hal=<?= $hal + 1 ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- ================= MODAL DETAIL ================= -->
<div class="modal-overlay" id="modalAspirasi">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Detail Aspirasi</h3>
            <button class="modal-close" onclick="tutupModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <p><strong>Nama:</strong> <span id="mNama"></span></p>
            <p><strong>NIM:</strong> <span id="mNim"></span></p>
            <p><strong>Status:</strong> <span id="mStatus"></span></p>
            <p><strong>Tanggal:</strong> <span id="mTanggal"></span></p>
            <hr style="margin:16px 0;border:none;border-top:1px solid #eee;">
            <p><strong>Isi Aspirasi:</strong></p>
            <div id="mIsi" style="background:#f8f9fa;padding:14px;border-radius:8px;margin-top:8px;line-height:1.7;white-space:pre-wrap;"></div>
        </div>
    </div>
</div>

<script>
const dataAspirasi = <?php
    // Ambil semua data untuk modal (pakai query terpisah)
    $q_all = mysqli_query($koneksi, "SELECT id, nama, nim, anonim, isi_aspirasi, tanggal_kirim FROM suara_mahasiswa ORDER BY tanggal_kirim DESC");
    $json_data = [];
    if ($q_all) {
        while ($r = mysqli_fetch_assoc($q_all)) {
            $json_data[] = [
                'id' => $r['id'],
                'nama' => $r['anonim'] ? 'Anonim' : ($r['nama'] ?? '-'),
                'nim' => $r['anonim'] ? '-' : ($r['nim'] ?? '-'),
                'anonim' => (bool)$r['anonim'],
                'isi' => $r['isi_aspirasi'],
                'tanggal' => date('d M Y, H:i', strtotime($r['tanggal_kirim']))
            ];
        }
    }
    // JSON_HEX_* penting supaya karakter </script> di dalam data tidak bisa
    // "kabur" keluar dari blok <script> (mencegah stored XSS via aspirasi)
    echo json_encode($json_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>;

function lihatAspirasi(id) {
    const item = dataAspirasi.find(d => d.id === id);
    if (!item) return;

    document.getElementById('mNama').textContent = item.nama;
    document.getElementById('mNim').textContent = item.nim;
    document.getElementById('mStatus').textContent = item.anonim ? 'Anonim' : 'Teridentifikasi';
    document.getElementById('mTanggal').textContent = item.tanggal;
    document.getElementById('mIsi').textContent = item.isi;

    document.getElementById('modalAspirasi').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function tutupModal() {
    document.getElementById('modalAspirasi').classList.remove('active');
    document.body.style.overflow = '';
}

// Tutup modal klik di luar
document.getElementById('modalAspirasi').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

// Tutup modal dengan tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') tutupModal();
});
</script>

<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(20,22,26,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 2000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.25s ease;
}
.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}
.modal-box {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    transform: scale(0.95);
    transition: transform 0.25s ease;
}
.modal-overlay.active .modal-box {
    transform: scale(1);
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
}
.modal-header h3 {
    font-size: 1.1rem;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.modal-close:hover {
    background: var(--hover-bg);
    color: var(--hmti-text);
}
.modal-body {
    padding: 24px;
    line-height: 1.8;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
