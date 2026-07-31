<?php
// ==========================================
// CEK_LOGIN.PHP
// WAJIB di-include PALING ATAS setiap halaman di folder admin/,
// SEBELUM ada output HTML apapun.
//
// Contoh pemakaian di admin/dashboard.php:
//     require_once '../includes/cek_login.php';
// ==========================================

// Muat lapisan keamanan: session hardening, security headers, CSRF, dll
require_once __DIR__ . '/security.php';
security_init();

// Load BASE_URL supaya tau path-nya localhost (/himti) atau domain (/)
require_once __DIR__ . '/../config/base_url.php';

// Kalau belum login sama sekali, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Fungsi bantu untuk membatasi halaman tertentu hanya untuk Super Admin
// Contoh pemakaian di admin/kelola-admin/index.php:
//     require_once '../../includes/cek_login.php';
//     hanya_super_admin();
function hanya_super_admin() {
    if ($_SESSION['role'] !== 'super_admin') {
        header('Location: ' . BASE_URL . '/admin/dashboard.php?status=forbidden');
        exit;
    }
}