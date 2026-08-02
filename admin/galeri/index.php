<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Kelola Galeri</h1>
    <p>Daftar foto galeri HMTI</p>
</div>

<?php
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'tambah_sukses':
            $n = isset($_GET['n']) ? (int)$_GET['n'] : 0;
            $msg = $n > 1 ? $n . ' foto berhasil ditambahkan.' : 'Foto berhasil ditambahkan.';
            break;
        case 'hapus_sukses':  $msg = 'Foto berhasil dihapus.'; break;
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
$per_halaman = 12;
$hal = isset($_GET['hal']) ? max(1, (int)$_GET['hal']) : 1;
$offset = ($hal - 1) * $per_halaman;

$total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM galeri");
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_hal = ceil($total_data / $per_halaman);

$query = "SELECT id, gambar, caption, tanggal_upload FROM galeri ORDER BY tanggal_upload DESC LIMIT $per_halaman OFFSET $offset";
$result = mysqli_query($koneksi, $query);
?>

<div class="admin-toolbar">
    <a href="tambah.php" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-plus"></i> Upload Foto
    </a>
    <span style="color:#8a8f98;font-size:0.9rem;">Total: <?= $total_data ?> foto</span>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th class="th-img">Foto</th>
                <th>Caption</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $gambar = BASE_URL . '/assets/img/galeri/' . $row['gambar'];
                    $tanggal = date('d M Y, H:i', strtotime($row['tanggal_upload']));
                ?>
                    <tr>
                        <td class="td-img">
                            <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($row['caption']) ?>">
                        </td>
                        <td><?= htmlspecialchars($row['caption']) ?></td>
                        <td><?= $tanggal ?></td>
                        <td class="td-actions">
                            <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm" title="Hapus">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:#8a8f98;">
                        Belum ada foto di galeri.
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
