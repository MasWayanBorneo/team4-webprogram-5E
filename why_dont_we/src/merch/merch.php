<?php
/* ============================================================================
   HALAMAN MERCH — produk diambil dari tabel `products` di database.
   Keranjang disimpan di session PHP ($_SESSION["cart"] = [id_produk => jumlah]).
   ============================================================================ */
$root = "../../";
$active = "merch";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

session_start();
$_SESSION["cart"] = $_SESSION["cart"] ?? [];

// tombol "Add" mengirim form POST berisi product_id
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = (int) ($_POST["product_id"] ?? 0);

    $result  = db_query($conn, "SELECT name FROM products WHERE id = ? AND is_active = 1", "i", [$product_id]);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        $_SESSION["cart"][$product_id] = ($_SESSION["cart"][$product_id] ?? 0) + 1;
        $_SESSION["cart_message"] = $product["name"] . " added to cart";
    }

    // redirect supaya refresh halaman tidak menambah produk dua kali
    redirect("merch.php#product-" . $product_id);
}

$products = db_fetch_all(db_query($conn,
    "SELECT id, name, collection, price, image, is_featured
     FROM products
     WHERE is_active = 1
     ORDER BY id"));

$featured   = array_filter($products, fn($p) => (int) $p["is_featured"] === 1);
$cart_count = array_sum($_SESSION["cart"]);

$cart_message = $_SESSION["cart_message"] ?? "";
unset($_SESSION["cart_message"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merch Store</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/merch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <main class="container">

        <div class="cta">
            <h2>Wear it loud — grab your official merch!</h2>
        </div>

        <?php if (!$products): ?>
            <p class="merch-status">No products available yet.</p>
        <?php endif; ?>

        <!-- FEATURED PRODUCTS (is_featured = 1) -->
        <?php if ($featured): ?>
        <section class="featured-section">
            <h1 class="section-title">Featured merch</h1>
            <div class="featured-products">
                <?php foreach ($featured as $product): ?>
                <article class="featured-card">
                    <div class="product-image">
                        <img src="<?= $root . e($product["image"]) ?>" alt="<?= e($product["name"]) ?>">
                    </div>

                    <div class="product-info">
                        <h2><?= e($product["name"]) ?></h2>
                        <p><?= e($product["collection"]) ?></p>
                        <span><?= money($product["price"]) ?></span>
                    </div>

                    <form method="post" class="add-form">
                        <input type="hidden" name="product_id" value="<?= (int) $product["id"] ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ALL PRODUCTS -->
        <?php if ($products): ?>
        <section>
            <h1 class="section-title">All merch</h1>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <article class="product-card" id="product-<?= (int) $product["id"] ?>">
                    <div class="card-image">
                        <img src="<?= $root . e($product["image"]) ?>" alt="<?= e($product["name"]) ?>">
                    </div>

                    <div class="price-row">
                        <span><?= money($product["price"]) ?></span>
                        <form method="post" class="add-form">
                            <input type="hidden" name="product_id" value="<?= (int) $product["id"] ?>">
                            <button type="submit">Add</button>
                        </form>
                    </div>

                    <h3><?= e($product["name"]) ?></h3>
                    <p><?= e($product["collection"]) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <!-- CART BAR -->
    <div class="cart-bar">
        <div class="cart-info">
            🛒 Cart - <?= $cart_count ?> <?= $cart_count === 1 ? "Item" : "Items" ?>
            <?php if ($cart_message): ?>
                <span class="cart-message" role="status">· <?= e($cart_message) ?></span>
            <?php endif; ?>
        </div>

        <button type="button" class="checkout-btn">
            Checkout
        </button>
    </div>
<?php include __DIR__ . "/../../includes/footer.php"; ?>

</body>
</html>