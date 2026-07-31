<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Kelola Departemen</h1>
    <p>Daftar departemen di lingkungan HMTI</p>
</div>

<?php
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'tambah_sukses': $msg = 'Departemen berhasil ditambahkan.'; break;
        case 'edit_sukses':   $msg = 'Departemen berhasil diperbarui.'; break;
        case 'hapus_sukses':  $msg = 'Departemen berhasil dihapus.'; break;
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

$query = "SELECT id, nama_departemen, deskripsi FROM departemen ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="admin-toolbar">
    <a href="tambah.php" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-plus"></i> Tambah Departemen
    </a>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Departemen</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_departemen']) ?></strong></td>
                        <td style="max-width:400px;"><?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 150))) ?><?= strlen($row['deskripsi']) > 150 ? '...' : '' ?></td>
                        <td class="td-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-admin btn-admin-warning btn-admin-sm" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus departemen ini? Semua anggota di departemen ini juga akan terpengaruh.')">
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
                    <td colspan="4" style="text-align:center;padding:40px;color:#8a8f98;">
                        Belum ada departemen.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
