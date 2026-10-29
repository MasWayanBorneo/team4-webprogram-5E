<?php
/* ============================================================================
   API PRODUK MERCH
   Mengembalikan semua produk aktif dari tabel `products` sebagai array JSON.
   Dipakai oleh src/js/merch.js untuk mengisi halaman merch.

   Buka di browser untuk mengecek hasilnya:
   http://localhost/team4-webdesign-4E/why_dont_we/api/products.php
   ============================================================================ */

require __DIR__ . "/../config/database.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT id, name, collection, price, image, is_featured
        FROM products
        WHERE is_active = 1
        ORDER BY id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal mengambil data produk."]);
    exit;
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    // mysqli mengembalikan semua nilai sebagai teks; ubah ke tipe aslinya
    $row["id"]          = (int) $row["id"];
    $row["price"]       = (float) $row["price"];
    $row["is_featured"] = (int) $row["is_featured"];
    $products[] = $row;
}

mysqli_close($conn);

echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
