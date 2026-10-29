<?php
/* ============================================================================
   API PRODUK MERCH
   Mengembalikan semua produk aktif dari tabel `products` sebagai array JSON.
   Dipakai oleh src/js/merch.js untuk mengisi halaman merch.

   Buka di browser untuk mengecek hasilnya:
   http://localhost/team4-webdesign-4E/why_dont_we/api/products.php
   ============================================================================ */

require __DIR__ . "/helpers.php";

$result = run_query($conn,
    "SELECT id, name, collection, price, image, is_featured
     FROM products
     WHERE is_active = 1
     ORDER BY id");

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    // ubah ke tipe aslinya (angka), bukan teks
    $row["id"]          = (int) $row["id"];
    $row["price"]       = (float) $row["price"];
    $row["is_featured"] = (int) $row["is_featured"];
    $products[] = $row;
}

send_json($products);
