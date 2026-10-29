<?php
/* ============================================================================
   PROSES FORM SUBSCRIBE — dipakai form newsletter di home, news, dan tour.
   Form mengirim POST berisi:
       email  → alamat email
       source → asal form: home, news, atau tour
   Email disimpan ke tabel `subscribers`, lalu pengunjung dikembalikan ke
   halaman asal dengan ?subscribe=ok | exists | invalid
   ============================================================================ */

require __DIR__ . "/../config/database.php";
require __DIR__ . "/../includes/functions.php";

// halaman tujuan per sumber (ditulis di sini supaya tidak bisa diarahkan ke situs lain)
$return_pages = [
    "home" => "../index.php",
    "news" => "../src/article/news.php",
    "tour" => "../src/tour/tour.php",
];

$source = $_POST["source"] ?? "home";
if (!isset($return_pages[$source])) {
    $source = "home";
}
$back = $return_pages[$source];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect($back);
}

$email = trim($_POST["email"] ?? "");

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    redirect($back . "?subscribe=invalid#newsletter");
}

// email sudah terdaftar? (kolom email bersifat UNIQUE)
$result = db_query($conn, "SELECT id FROM subscribers WHERE email = ?", "s", [$email]);
if (mysqli_fetch_assoc($result)) {
    redirect($back . "?subscribe=exists#newsletter");
}

db_query($conn, "INSERT INTO subscribers (email, source) VALUES (?, ?)", "ss", [$email, $source]);

redirect($back . "?subscribe=ok#newsletter");
