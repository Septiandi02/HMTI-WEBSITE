<?php
require_once __DIR__ . '/../../includes/cek_login.php'; // memuat security + session hardening
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/db.php';        // helper prepared statement (anti SQLi)
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Kelola Kegiatan</h1>
    <p>Daftar semua kegiatan HMTI</p>
</div>

<?php
// Notifikasi sukses/error dari proses tambah/edit/hapus
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'tambah_sukses': $msg = 'Kegiatan berhasil ditambahkan.'; break;
        case 'edit_sukses':   $msg = 'Kegiatan berhasil diperbarui.'; break;
        case 'hapus_sukses':  $msg = 'Kegiatan berhasil dihapus.'; break;
        case 'error':         $msg = 'Terjadi kesalahan. Silakan coba lagi.'; $tipe = 'error'; break;
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

// ---------- PENCARIAN ----------
$cari = trim($_GET['cari'] ?? '');
$where = '';
$search_params = [];
if ($cari !== '') {
    // Prepared statement: pola LIKE disisipkan sebagai parameter (anti SQLi)
    $where = "WHERE k.judul LIKE ? OR k.isi LIKE ?";
    $like = '%' . $cari . '%';
    $search_params = [$like, $like];
}

// ---------- PAGINATION ----------
$per_halaman = 10;
$hal = isset($_GET['hal']) ? max(1, (int)$_GET['hal']) : 1;
$offset = ($hal - 1) * $per_halaman;

$total_result = db_query("SELECT COUNT(*) as total FROM kegiatan k $where", $search_params);
$total_data = $total_result ? mysqli_fetch_assoc($total_result)['total'] : 0;
$total_hal = ceil($total_data / $per_halaman);

$query = "SELECT k.id, k.judul, k.gambar, k.tanggal_dibuat, u.nama AS penulis
          FROM kegiatan k
          LEFT JOIN users u ON k.penulis_id = u.id
          $where
          ORDER BY k.tanggal_dibuat DESC
          LIMIT $per_halaman OFFSET $offset";
$result = db_query($query, $search_params);
?>

<div class="admin-toolbar">
    <a href="<?= BASE_URL ?>/admin/kegiatan/tambah.php" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-plus"></i> Tambah Kegiatan
    </a>

    <form method="GET" class="admin-search">
        <input type="text" name="cari" placeholder="Cari judul atau isi..." value="<?= htmlspecialchars($cari) ?>">
        <button type="submit" class="btn-admin btn-admin-primary btn-admin-sm">
            <i class="fa-solid fa-search"></i>
        </button>
        <?php if ($cari !== ''): ?>
            <a href="index.php" class="btn-admin btn-admin-sm" style="background:#e0e3e7;color:#4a4d54;">
                <i class="fa-solid fa-times"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $gambar = !empty($row['gambar']) ? BASE_URL . '/assets/img/kegiatan/' . $row['gambar'] : BASE_URL . '/assets/img/kegiatan/default.svg';
                    $tanggal = date('d M Y, H:i', strtotime($row['tanggal_dibuat']));
                ?>
                    <tr>
                        <td class="td-img">
                            <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                        </td>
                        <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                        <td><?= htmlspecialchars($row['penulis'] ?? 'Admin') ?></td>
                        <td><?= $tanggal ?></td>
                        <td class="td-actions">
                            <a href="<?= BASE_URL ?>/kegiatan-detail.php?id=<?= $row['id'] ?>" target="_blank" class="btn-admin btn-admin-success btn-admin-sm" title="Lihat">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-admin btn-admin-warning btn-admin-sm" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
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
                    <td colspan="5" style="text-align:center;padding:40px;color:#8a8f98;">
                        <?= $cari ? 'Kegiatan tidak ditemukan.' : 'Belum ada kegiatan. Silakan tambah kegiatan baru.' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_hal > 1): ?>
    <div class="pagination" style="margin-top:24px;justify-content:center;">
        <?php if ($hal > 1): ?>
            <a href="?hal=<?= $hal - 1 ?>&cari=<?= urlencode($cari) ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_hal; $i++): ?>
            <a href="?hal=<?= $i ?>&cari=<?= urlencode($cari) ?>" class="page-btn <?= $i === $hal ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($hal < $total_hal): ?>
            <a href="?hal=<?= $hal + 1 ?>&cari=<?= urlencode($cari) ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
