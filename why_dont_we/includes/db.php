<?php
/* ============================================================================
   KONEKSI DATABASE — mysqli prosedural.
   Dipakai di setiap halaman yang butuh data:
     require __DIR__ . '/../../includes/db.php';   // dari src/<folder>/
   Setelah itu tersedia $conn (koneksi) dan fungsi e() untuk mencetak teks.
   ============================================================================ */

// config.php berisi data login database; kalau belum dibuat (mis. di CI),
// pakai nilai bawaan dari config.example.php
require is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.example.php';

// error query dicek manual lewat nilai kembalian, bukan exception
mysqli_report(MYSQLI_REPORT_OFF);

$conn = function_exists('mysqli_connect') ? @mysqli_connect($db_host, $db_user, $db_pass, $db_name) : false;

if (!$conn) {
    http_response_code(500);
    exit('Database connection failed. Check includes/config.php.');
}

mysqli_set_charset($conn, 'utf8mb4');

// PHP dan MySQL sama-sama memakai WIB, apa pun pengaturan servernya,
// supaya jam di created_at / joined_at / invoice tidak meleset
date_default_timezone_set('Asia/Jakarta');
mysqli_query($conn, "SET time_zone = '+07:00'");

// cetak teks dari database ke HTML dengan aman (mencegah XSS)
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// cek koneksi: buka file ini langsung di browser (includes/db.php).
// Saat di-require oleh halaman lain, bagian ini dilewati supaya tidak
// mengganggu tampilan, redirect, atau JSON dari api/.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $tables = mysqli_num_rows(mysqli_query($conn, 'SHOW TABLES'));
    echo 'Database connection successful: ' . e($db_name) . ' (' . $tables . ' tables).';
}
