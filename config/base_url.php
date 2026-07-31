<?php
// ==========================================
// BASE_URL.PHP
// Auto-detect environment (localhost vs domain)
// ==========================================

$host = $_SERVER['HTTP_HOST'] ?? '';

// Deteksi localhost: cocokkan nama host lokal
$is_local = in_array($host, ['localhost', '127.0.0.1', '::1']);

define('BASE_URL', $is_local ? '/himti' : '');

/*
 * Cara pakai di file PHP:
 *   <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css">
 *   <a href="<?= BASE_URL ?>/kegiatan.php">Kegiatan</a>
 *   header('Location: ' . BASE_URL . '/admin/dashboard.php');
 *
 * Di localhost   → BASE_URL = '/himti'   → jadinya /himti/assets/css/...
 * Di domain asli → BASE_URL = ''          → jadinya /assets/css/...
 */
