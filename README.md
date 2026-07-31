# 🎓 HMTI - Website Himpunan Mahasiswa Teknologi Informasi UBB

![PHP](https://img.shields.io/badge/PHP-8-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL%20%2F%20MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Vanilla JS](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![No Framework](https://img.shields.io/badge/Tanpa%20Framework-100%25%20Native-2f9e6e?style=flat-square)

Website resmi **Himpunan Mahasiswa Teknologi Informasi**, Universitas Bangka Belitung. Dibangun dengan **PHP native + MySQL** tanpa framework. Proyek ini adalah sistem informasi organisasi lengkap: **situs publik**, **panel admin**, dan **keamanan web yang serius**.

---

## ✨ Fitur

### 🌐 Sisi Publik

- Beranda dengan hero interaktif, kegiatan terbaru, dan preview departemen
- Halaman **Kegiatan** (daftar + detail artikel) & **Galeri** (dengan lightbox)
- **Tentang**: profil & sejarah, visi-misi, struktur organisasi (carousel), departemen (tab)
- **Kontak**: informasi sekretariat (termasuk peta Google), media partnership, dan **Suara Mahasiswa** (aspirasi, bisa anonim)
- Mode **gelap/terang**, sepenuhnya responsif

### 🔐 Panel Admin (role Admin & Super Admin)

- Kelola **Kegiatan, Galeri, Struktur Organisasi, Departemen, Suara Mahasiswa, dan Admin**
- Upload gambar dengan **kompresi otomatis** (GD)
- Pencarian & pagination

### 🎨 Interaksi & Animasi

- Sapaan yang menyesuaikan **waktu WIB** + tangan melambai 👋
- Garis **"coretan tangan"** yang menggambar sendiri di bawah judul section
- Bilah **progres scroll**, tombol kembali ke atas dengan **cincin progres + roket** 🚀
- **Sparkle** saat klik ✨, **easter egg** (ketik `hmti` → konfeti 🎉 + suara pop)
- **Jam digital WIB** di footer, judul tab berubah saat ditinggalkan
- **Tilt 3D halus** pada kartu, animasi reveal, dan mikro-interaksi lainnya
- Semua animasi menghormati `prefers-reduced-motion`

### 🔒 Keamanan

- **SQL Injection** → semua query memakai **prepared statements** (`includes/db.php`)
- **XSS** → output di-escape (`e()`), konten HTML "kaya" disanitasi (`bersihkan_html()`)
- **CSRF** → token di semua form POST (termasuk tombol hapus)
- **Session** → cookie HttpOnly + SameSite, regenerate ID, timeout 30 menit
- **Brute-force** → terkunci setelah 5× gagal dalam 15 menit
- **Upload** → verifikasi MIME + re-kompresi, eksekusi script diblokir di folder upload
- **Security headers** (CSP, X-Frame-Options, nosniff, dll) + proteksi folder internal lewat `.htaccess`
- Error detail tidak bocor ke user (di-log ke `logs/`)

---

## 🛠️ Tech Stack

| Bagian   | Teknologi                                    |
| -------- | -------------------------------------------- |
| Backend  | PHP 8 (native, `mysqli`)                     |
| Database | MySQL / MariaDB                              |
| Frontend | HTML5, CSS3 (custom + dark mode), Vanilla JS |
| Server   | Apache (XAMPP lokal / cPanel)                |

---

## 📁 Struktur Folder

```
himti/
├── index.php  kegiatan.php  galeri.php  kegiatan-detail.php  404.php
├── admin/            # Panel admin (dashboard + CRUD semua modul)
├── auth/             # Login, proses login, logout
├── config/           # koneksi database, base_url
├── kontak/           # informasi sekretariat, media partnership, suara mahasiswa
├── tentang/          # profil-sejarah, visi-misi, struktur organisasi, departemen
├── includes/         # header, footer, security.php, db.php, upload_helper.php, cek_login.php
├── database/         # Dump SQL (skema + data awal)
└── assets/           # css, js, img
```

---

## 🚀 Cara Menjalankan

**Persyaratan:** PHP 8+, MySQL/MariaDB, Apache (misal XAMPP).

1. Salin folder project ke `htdocs/himti` (XAMPP) atau `public_html` (cPanel).
2. Buat database (nama bebas, contoh `db_himti`).
3. Import `database/tiubbac_hmti.sql` via phpMyAdmin.
4. Sesuaikan kredensial DB di **`config/koneksi.php`**.
5. Buka `http://localhost/himti/` (lokal) atau domain kamu.

> ⚠️ **Nama database**: dump default memakai `tiubbac_hmti`. Kalau DB kamu beda nama, cukup ubah `$dbname` di `config/koneksi.php`.

### Akun Default

| Username     | Role        |
| ------------ | ----------- |
| `superadmin` | Super Admin |

> 🔑 **PENTING**: password dihash (bcrypt) di dalam file SQL. **Wajib ganti password** setelah login pertama lewat menu **Kelola Admin → Edit**. Jangan pernah memakai password default di produksi!

---

## 🧰 Catatan Pengembangan

- Tanpa dependency (Composer/npm), tinggal taruh di server.
- CSS/JS **cache-busting otomatis** (`filemtime`), tidak perlu hard refresh.
- Semua file PHP lolos `php -l` (syntax check).

---

## 🗺️ Ide Pengembangan Lanjutan

- [ ] Halaman detail per anggota struktur organisasi
- [ ] Komentar / reaksi pada artikel kegiatan
- [ ] Export laporan Suara Mahasiswa (CSV/PDF)
- [ ] Multi-bahasa (ID/EN)
- [ ] Unit test & deployment otomatis (CI/CD)

---

## 📄 Lisensi

[MIT](LICENSE), silakan dipelajari, dikembangkan, dan dipakai untuk belajar.

Dibuat dengan 💛 oleh **[Nama Kamu]**, Himpunan Mahasiswa Teknologi Informasi UBB
