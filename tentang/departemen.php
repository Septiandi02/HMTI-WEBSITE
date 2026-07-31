<?php
require_once '../config/koneksi.php';
include '../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tentang.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/tentang.css') ?>">

<section class="page-banner">
    <span>Tentang Kami</span>
    <h1>Departemen</h1>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/index.php">Beranda</a> / Departemen</div>
</section>

<section class="section">

    <div class="departemen-tabs">
        <?php
        // Icon per departemen - disesuaikan dengan preview card di Beranda
        $icon_map = [
            'hldo'    => 'fa-people-group',
            'ristek'  => 'fa-laptop-code',
            'psdm'    => 'fa-user-graduate',
            'kominfo' => 'fa-bullhorn',
            'mikat'   => 'fa-handshake',
        ];

        $query_dept = "SELECT id, nama_departemen, deskripsi FROM departemen ORDER BY id ASC";
        $result_dept = mysqli_query($koneksi, $query_dept);
        $departemen_list = [];

        if ($result_dept) {
            while ($row = mysqli_fetch_assoc($result_dept)) {
                $departemen_list[] = $row;
            }
        }

        foreach ($departemen_list as $index => $dept):
            $slug = strtolower($dept['nama_departemen']);
            $icon = $icon_map[$slug] ?? 'fa-users';
        ?>
            <button class="tab-btn <?= $index === 0 ? 'active' : '' ?>" data-target="dept-<?= e($slug) ?>" data-slug="<?= e($slug) ?>">
                <i class="fa-solid <?= $icon ?>"></i>
                <span><?= htmlspecialchars($dept['nama_departemen']) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="departemen-content">
        <?php foreach ($departemen_list as $index => $dept):
            $slug = strtolower($dept['nama_departemen']);
        ?>
            <div id="dept-<?= e($slug) ?>" class="dept-panel <?= $index === 0 ? 'active' : '' ?>">
                <h3>Departemen <?= htmlspecialchars($dept['nama_departemen']) ?></h3>
                <p><?= nl2br(htmlspecialchars($dept['deskripsi'])) ?></p>
            </div>
        <?php endforeach; ?>

        <?php if (empty($departemen_list)): ?>
            <p class="empty-state">Data departemen belum tersedia.</p>
        <?php endif; ?>
    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.dept-panel');

    function activateTab(button) {
        tabButtons.forEach(btn => btn.classList.remove('active'));
        panels.forEach(panel => panel.classList.remove('active'));

        button.classList.add('active');
        const target = document.getElementById(button.dataset.target);
        if (target) target.classList.add('active');
    }

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activateTab(button);
        });
    });

    // Buka tab sesuai parameter URL (?dept=ristek) - dipakai dari link preview di Beranda
    const params = new URLSearchParams(window.location.search);
    const deptParam = params.get('dept');

    if (deptParam) {
        const matchedButton = document.querySelector(`.tab-btn[data-slug="${deptParam}"]`);
        if (matchedButton) activateTab(matchedButton);
    }
});
</script>

<?php include '../includes/footer.php'; ?>