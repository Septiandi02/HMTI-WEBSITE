<?php
// ============================================================
// DB.PHP, helper query dengan prepared statement
// Mencegah SQL Injection (data tidak pernah dicampur langsung
// ke dalam SQL, selalu lewat placeholder ?).
// ============================================================

if (!function_exists('db_query')) {

    require_once __DIR__ . '/../config/koneksi.php';

    /**
     * Jalankan query aman pakai prepared statement.
     *
     * Contoh SELECT:
     *   $res = db_query("SELECT * FROM users WHERE id = ?", [$id]);
     *   $row = mysqli_fetch_assoc($res);
     *
     * Contoh INSERT/UPDATE/DELETE:
     *   db_query("INSERT INTO kegiatan (judul, isi) VALUES (?, ?)", [$judul, $isi]);
     *
     * @return mysqli_result|bool  mysqli_result untuk SELECT, true untuk lainnya, false jika gagal
     */
    function db_query(string $sql, array $params = []) {
        global $koneksi;
        $stmt = @mysqli_prepare($koneksi, $sql);
        if (!$stmt) {
            log_error('DB prepare gagal: ' . mysqli_error($koneksi) . ' | SQL: ' . $sql);
            return false;
        }

        if ($params) {
            $types = '';
            foreach ($params as $p) {
                if (is_int($p)) {
                    $types .= 'i';
                } elseif (is_double($p)) {
                    $types .= 'd';
                } else {
                    $types .= 's'; // string & null (null tetap dikirim sebagai NULL)
                }
            }
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            log_error('DB execute gagal: ' . mysqli_stmt_error($stmt) . ' | SQL: ' . $sql);
            mysqli_stmt_close($stmt);
            return false;
        }

        // Kalau query menghasilkan result set (SELECT/SHOW), kembalikan mysqli_result
        $meta = mysqli_stmt_result_metadata($stmt);
        if ($meta) {
            mysqli_free_result($meta);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Ambil satu baris (assoc array) atau null.
     */
    function db_fetch_one(string $sql, array $params = []) {
        $result = db_query($sql, $params);
        if ($result instanceof mysqli_result) {
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            return $row ?: null;
        }
        return null;
    }

    /**
     * Ambil semua baris (array of assoc array).
     */
    function db_fetch_all(string $sql, array $params = []): array {
        $result = db_query($sql, $params);
        $rows = [];
        if ($result instanceof mysqli_result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            mysqli_free_result($result);
        }
        return $rows;
    }

    /**
     * ID terakhir yang di-INSERT (untuk cek keberhasilan insert).
     */
    function db_last_id(): int {
        global $koneksi;
        return (int)mysqli_insert_id($koneksi);
    }
}
