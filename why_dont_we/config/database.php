<?php
/* ============================================================================
   KONEKSI DATABASE (mysqli, gaya prosedural)
   Panggil file ini di setiap halaman PHP atau endpoint API yang butuh MySQL.
   File ini membuat koneksi di variabel $conn:

       require __DIR__ . '/../config/database.php';

       $result = mysqli_query($conn, "SELECT * FROM products");
       while ($row = mysqli_fetch_assoc($result)) {
           echo $row['name'];
       }

   Untuk nilai yang berasal dari form atau URL, gunakan prepared statement
   supaya input pengguna tidak bisa mengubah perintah SQL (SQL injection):

       $stmt = mysqli_prepare($conn, "SELECT * FROM members WHERE slug = ?");
       mysqli_stmt_bind_param($stmt, "s", $slug);
       mysqli_stmt_execute($stmt);
       $member = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

   Nilai di bawah sesuai instalasi bawaan XAMPP (user "root", tanpa password).
   ============================================================================ */

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "wdw_db";

// error dilaporkan lewat nilai kembalian, supaya bisa dicek dengan if di bawah
mysqli_report(MYSQLI_REPORT_OFF);

// "@" menyembunyikan warning mentah PHP; pesan di bawah yang menjelaskan errornya
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    http_response_code(500);
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// utf8mb4 supaya nama dan path dengan karakter khusus (misalnya "—") tidak rusak
mysqli_set_charset($conn, "utf8mb4");
