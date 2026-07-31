<?php
require_once __DIR__ . '/../../includes/cek_login.php';
hanya_super_admin();
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Kelola Admin</h1>
    <p>Daftar Admin & Super Admin, khusus untuk Super Admin</p>
</div>

<?php
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'tambah_sukses': $msg = 'Admin baru berhasil ditambahkan.'; break;
        case 'edit_sukses':   $msg = 'Data admin berhasil diperbarui.'; break;
        case 'hapus_sukses':  $msg = 'Admin berhasil dihapus.'; break;
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

$query = "SELECT id, nama, username, role FROM users ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="admin-toolbar">
    <a href="tambah.php" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-plus"></i> Tambah Admin
    </a>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $role_label = $row['role'] === 'super_admin' ? 'Super Admin' : 'Admin';
                    $role_badge = $row['role'] === 'super_admin'
                        ? '<span style="background:#e8a317;color:#fff;padding:2px 10px;border-radius:12px;font-size:0.75rem;font-weight:600;">Super Admin</span>'
                        : '<span style="background:#5d6470;color:#fff;padding:2px 10px;border-radius:12px;font-size:0.75rem;font-weight:600;">Admin</span>';
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= $role_badge ?></td>
                        <td class="td-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-admin btn-admin-warning btn-admin-sm" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <?php if ($row['id'] !== (int)$_SESSION['user_id']): ?>
                                <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#8a8f98;">
                        Belum ada admin.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
