<?php
/* ============================================================================
   PRODUK MERCH — daftar produk aktif dalam bentuk JSON untuk src/js/merch.js.
   ============================================================================ */
require __DIR__ . '/../includes/db.php';

$result = mysqli_query($conn, 'SELECT id, name, collection, price, image, is_featured FROM products WHERE is_active = 1 ORDER BY id');
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
