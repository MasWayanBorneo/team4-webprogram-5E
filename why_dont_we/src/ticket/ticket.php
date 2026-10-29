<?php
/* ============================================================================
   HALAMAN TICKET — form pemesanan tiket.
   ?concert=2 → konser yang dipilih (hanya konser yang sedang dijual)
   Form dikirim (POST) ke halaman ini juga:
   - tombol "Update total" (action=preview) → hanya menghitung ulang total
   - tombol "Proceed to Checkout" (action=order) → data divalidasi, kuota dicek,
     pesanan disimpan ke `ticket_orders`, lalu pindah ke invoice.php.
   Harga dan total selalu dihitung di server dari tabel ticket_categories.
   ============================================================================ */
$root = "../../";
$active = "tour";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

const MAX_TICKETS_PER_ORDER = 10;

// konser yang sedang dijual
$concerts = db_fetch_all(db_query($conn,
    "SELECT id, venue_name, event_date FROM concerts
     WHERE status = 'available' AND event_date >= NOW()
     ORDER BY event_date"));

$concert_ids = array_map("intval", array_column($concerts, "id"));
$concert_id  = (int) ($_POST["concert_id"] ?? $_GET["concert"] ?? 0);
if (!in_array($concert_id, $concert_ids, true)) {
    $concert_id = $concert_ids[0] ?? 0;
}

// kategori tiket + sisa kuota (kuota - tiket yang sudah terjual)
function load_categories($conn, $concert_id)
{
    return db_fetch_all(db_query($conn,
        "SELECT c.id, c.name, c.description, c.price,
                GREATEST(c.quota - COALESCE(SUM(o.quantity), 0), 0) AS remaining
         FROM ticket_categories c
         LEFT JOIN ticket_orders o ON o.ticket_category_id = c.id AND o.status = 'paid'
         WHERE c.concert_id = ?
         GROUP BY c.id
         ORDER BY c.price",
        "i", [$concert_id]));
}

$error = "";
$old = [
    "full_name"   => trim($_POST["full_name"] ?? ""),
    "email"       => trim($_POST["email"] ?? ""),
    "phone"       => trim($_POST["phone"] ?? ""),
    "category_id" => (int) ($_POST["category_id"] ?? 0),
    "quantity"    => (int) ($_POST["quantity"] ?? 1),
];

// ---- simpan pesanan ---------------------------------------------------------
$action = $_POST["action"] ?? "order";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "order") {
    if ($old["full_name"] === "" || mb_strlen($old["full_name"]) > 100) {
        $error = "Please enter your full name (max 100 characters).";
    } elseif (!filter_var($old["email"], FILTER_VALIDATE_EMAIL) || strlen($old["email"]) > 100) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^[0-9+\-\s()]{6,20}$/', $old["phone"])) {
        $error = "Please enter a valid phone number.";
    } elseif ($old["quantity"] < 1 || $old["quantity"] > MAX_TICKETS_PER_ORDER) {
        $error = "You can order 1 to " . MAX_TICKETS_PER_ORDER . " tickets at a time.";
    }

    if ($error === "") {
        // transaksi + FOR UPDATE: dua pembeli bersamaan tidak bisa melebihi kuota
        mysqli_begin_transaction($conn);

        $category = mysqli_fetch_assoc(db_query($conn,
            "SELECT c.price, c.quota
             FROM ticket_categories c
             JOIN concerts k ON k.id = c.concert_id
             WHERE c.id = ? AND k.status = 'available' AND k.event_date >= NOW()
             FOR UPDATE",
            "i", [$old["category_id"]]));

        if (!$category) {
            $error = "This ticket category is not on sale.";
        } else {
            $sold = (int) mysqli_fetch_assoc(db_query($conn,
                "SELECT COALESCE(SUM(quantity), 0) AS sold FROM ticket_orders
                 WHERE ticket_category_id = ? AND status = 'paid'",
                "i", [$old["category_id"]]))["sold"];
            $remaining = (int) $category["quota"] - $sold;

            if ($old["quantity"] > $remaining) {
                $error = $remaining > 0
                    ? "Only $remaining tickets left in this category."
                    : "This ticket category is sold out.";
            }
        }

        if ($error !== "") {
            mysqli_rollback($conn);
        } else {
            $unit_price = (float) $category["price"];
            $total      = round($unit_price * $old["quantity"], 2);

            // contoh: WDW-T3K9ZQ-A1B2C3 (waktu + kode acak, supaya sulit ditebak)
            $invoice_no = "WDW-" . strtoupper(base_convert((string) time(), 10, 36))
                        . "-" . strtoupper(bin2hex(random_bytes(3)));

            db_query($conn,
                "INSERT INTO ticket_orders
                    (invoice_no, ticket_category_id, full_name, email, phone, quantity, unit_price, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                "sisssidd",
                [$invoice_no, $old["category_id"], $old["full_name"], $old["email"], $old["phone"],
                 $old["quantity"], $unit_price, $total]);

            mysqli_commit($conn);
            redirect("invoice.php?invoice=" . urlencode($invoice_no));
        }
    }
}

// ---- data untuk form --------------------------------------------------------
$categories = $concert_id ? load_categories($conn, $concert_id) : [];

// kategori terpilih: dari form sebelumnya, atau kategori pertama yang masih ada
$open_ids = array_map("intval", array_column(array_filter($categories, fn($c) => $c["remaining"] > 0), "id"));
$selected_id = in_array($old["category_id"], $open_ids, true) ? $old["category_id"] : ($open_ids[0] ?? 0);

$selected_price = 0;
foreach ($categories as $category) {
    if ((int) $category["id"] === $selected_id) {
        $selected_price = (float) $category["price"];
    }
}
$quantity = max(1, min(MAX_TICKETS_PER_ORDER, $old["quantity"]));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Tickets</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/tour.css">
    <link rel="stylesheet" href="../css/ticket.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <main>

        <!-- Page Header -->
        <section class="page-header">
            <h2>Get Your Tickets</h2>
            <p class="ticket-sub">Choose a category and grab your spot before they sell out.</p>
        </section>

        <!-- Ticket Form -->
        <section class="ticket-section">
            <?php if (!$concerts): ?>
                <p class="form-message is-error">No concerts are on sale right now. Check the <a href="../tour/tour.php">tour page</a> for upcoming dates.</p>
            <?php else: ?>

            <!-- pilih konser (GET): mengganti konser memuat ulang kategori -->
            <form method="get" class="concert-picker">
                <label for="concert">Concert</label>
                <div class="concert-picker__row">
                    <select id="concert" name="concert">
                        <?php foreach ($concerts as $concert): ?>
                            <option value="<?= (int) $concert["id"] ?>"<?= (int) $concert["id"] === $concert_id ? " selected" : "" ?>><?= e($concert["venue_name"]) ?> — <?= format_date($concert["event_date"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="concert-picker__btn">Change</button>
                </div>
            </form>

            <!-- pesan tiket (POST ke halaman ini) -->
            <form method="post" class="ticket-form" id="ticket-form">
                <input type="hidden" name="concert_id" value="<?= $concert_id ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="full_name" value="<?= e($old["full_name"]) ?>" placeholder="Your full name" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($old["email"]) ?>" placeholder="you@email.com" maxlength="100" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($old["phone"]) ?>" placeholder="08xx xxxx xxxx" maxlength="20" required>
                </div>

                <!-- Ticket Categories -->
                <fieldset class="ticket-categories">
                    <legend>Ticket Category</legend>
                    <?php foreach ($categories as $category): $sold_out = $category["remaining"] <= 0; ?>
                        <label class="category-card">
                            <input type="radio" name="category_id" value="<?= (int) $category["id"] ?>"
                                   data-price="<?= (float) $category["price"] ?>"
                                   data-remaining="<?= (int) $category["remaining"] ?>"
                                   <?= (int) $category["id"] === $selected_id ? "checked" : "" ?>
                                   <?= $sold_out ? "disabled" : "" ?> required>
                            <div class="category-info">
                                <h3><?= e($category["name"]) ?></h3>
                                <p><?= $sold_out ? "Sold out" : e($category["description"]) ?></p>
                            </div>
                            <span class="category-price"><?= money($category["price"]) ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="<?= MAX_TICKETS_PER_ORDER ?>" value="<?= $quantity ?>" required>
                </div>

                <div class="order-summary">
                    <span>Total</span>
                    <span id="total-price"><?= money($selected_price * $quantity) ?></span>
                </div>
                <!-- menghitung ulang total setelah kategori/jumlah diganti (tanpa menyimpan pesanan) -->
                <button type="submit" name="action" value="preview" class="total-btn" formnovalidate>Update total</button>

                <?php if ($error): ?>
                    <p class="ticket-error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>

                <button type="submit" name="action" value="order" class="ticket-submit"<?= $selected_id ? "" : " disabled" ?>>Proceed to Checkout</button>
            </form>

            <?php endif; ?>
        </section>

    </main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>


</body>
</html>
