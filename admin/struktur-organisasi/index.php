<?php
require_once __DIR__ . '/../../includes/cek_login.php';
require_once __DIR__ . '/../../config/koneksi.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Struktur Organisasi</h1>
    <p>Kelola data anggota dan pejabat HMTI</p>
</div>

<?php
if (isset($_GET['status'])):
    $msg = '';
    $tipe = 'success';
    switch ($_GET['status']) {
        case 'tambah_sukses': $msg = 'Anggota berhasil ditambahkan.'; break;
        case 'edit_sukses':   $msg = 'Data anggota berhasil diperbarui.'; break;
        case 'hapus_sukses':  $msg = 'Anggota berhasil dihapus.'; break;
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
?>

<div class="admin-toolbar">
    <a href="tambah.php" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-plus"></i> Tambah Anggota
    </a>
</div>

<?php
// Tampilkan per kategori: Pejabat Teras dulu, lalu per departemen
$kategori_list = [
    'pejabat_teras' => 'Pejabat Teras',
];

// Ambil daftar departemen
$dept_result = mysqli_query($koneksi, "SELECT id, nama_departemen FROM departemen ORDER BY id ASC");
$departemen_list = [];
if ($dept_result) {
    while ($d = mysqli_fetch_assoc($dept_result)) {
        $departemen_list[$d['id']] = $d['nama_departemen'];
    }
}

// Fungsi render tabel anggota
function render_anggota_table($koneksi, $where_label, $where_condition, $departemen_list = []) {
    $query = "SELECT id, nama, jabatan, foto, urutan, departemen_id 
              FROM anggota_organisasi 
              $where_condition
              ORDER BY urutan ASC, nama ASC";
    $result = mysqli_query($koneksi, $query);
?>
    <h3 style="margin: 28px 0 14px; font-size:1.1rem;"><?= $where_label ?></h3>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-img">Foto</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <?php if (!empty($departemen_list)): ?>
                        <th>Departemen</th>
                    <?php endif; ?>
                    <th>Urutan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $foto = !empty($row['foto']) ? BASE_URL . '/assets/img/struktur/' . $row['foto'] : BASE_URL . '/assets/img/struktur/default.svg';
                        $dept_nama = isset($row['departemen_id'], $departemen_list[$row['departemen_id']]) ? $departemen_list[$row['departemen_id']] : '-';
                    ?>
                        <tr>
                            <td class="td-img"><img src="<?= $foto ?>" alt="<?= htmlspecialchars($row['nama']) ?>"></td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                            <?php if (!empty($departemen_list)): ?>
                                <td><?= htmlspecialchars($dept_nama) ?></td>
                            <?php endif; ?>
                            <td><?= (int)$row['urutan'] ?></td>
                            <td class="td-actions">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-admin btn-admin-warning btn-admin-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="hapus.php" class="admin-form-inline" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
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
                        <td colspan="<?= empty($departemen_list) ? 5 : 6 ?>" style="text-align:center;padding:30px;color:#8a8f98;">
                            Belum ada data.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}

// Render Pejabat Teras
render_anggota_table($koneksi, 'Pejabat Teras', "WHERE kategori = 'pejabat_teras'");

// Render per Departemen
foreach ($departemen_list as $dept_id => $dept_nama):
    render_anggota_table($koneksi, 'Departemen ' . $dept_nama, "WHERE kategori = 'departemen' AND departemen_id = $dept_id", $departemen_list);
endforeach;

?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
