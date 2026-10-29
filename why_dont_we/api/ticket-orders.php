<?php
/* ============================================================================
   API PESANAN TIKET
   POST api/ticket-orders.php                 → simpan pesanan baru
        body JSON: { category_id, full_name, email, phone, quantity }
        balasan:   { invoice_no }
   GET  api/ticket-orders.php?invoice=WDW-... → data invoice untuk invoice.html

   Harga dan total selalu dihitung di server dari tabel ticket_categories,
   jadi tidak bisa diubah dari browser.
   ============================================================================ */

require __DIR__ . "/helpers.php";

const MAX_TICKETS_PER_ORDER = 10;

$method = $_SERVER["REQUEST_METHOD"];

// ---- ambil data invoice -----------------------------------------------------
if ($method === "GET") {
    $invoice_no = trim($_GET["invoice"] ?? "");
    if ($invoice_no === "") {
        send_error("Nomor invoice wajib diisi.", 400);
    }

    $result = run_query($conn,
        "SELECT o.invoice_no, o.created_at, o.full_name, o.email, o.phone,
                o.quantity, o.unit_price, o.total, o.status,
                c.name AS category_name, k.venue_name, k.event_date
         FROM ticket_orders o
         JOIN ticket_categories c ON c.id = o.ticket_category_id
         JOIN concerts k ON k.id = c.concert_id
         WHERE o.invoice_no = ?",
        "s", [$invoice_no]);

    $order = mysqli_fetch_assoc($result);
    if (!$order) {
        send_error("Invoice tidak ditemukan.", 404);
    }

    // nama field mengikuti yang dipakai di invoice.html
    send_json([
        "invoiceNo"    => $order["invoice_no"],
        "date"         => str_replace(" ", "T", $order["created_at"]),
        "concert"      => $order["venue_name"] . " - " . date("j F Y", strtotime($order["event_date"])),
        "fullname"     => $order["full_name"],
        "email"        => $order["email"],
        "phone"        => $order["phone"],
        "categoryName" => $order["category_name"],
        "unitPrice"    => (float) $order["unit_price"],
        "quantity"     => (int) $order["quantity"],
        "total"        => (float) $order["total"],
        "status"       => $order["status"],
    ]);
}

// ---- simpan pesanan baru ----------------------------------------------------
if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!is_array($input)) {
        send_error("Data pesanan tidak valid.", 400);
    }

    $category_id = (int) ($input["category_id"] ?? 0);
    $full_name   = trim($input["full_name"] ?? "");
    $email       = trim($input["email"] ?? "");
    $phone       = trim($input["phone"] ?? "");
    $quantity    = (int) ($input["quantity"] ?? 0);

    // validasi input
    if ($full_name === "" || mb_strlen($full_name) > 100) {
        send_error("Nama lengkap wajib diisi (maksimal 100 karakter).", 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        send_error("Alamat email tidak valid.", 422);
    }
    if (!preg_match('/^[0-9+\-\s()]{6,20}$/', $phone)) {
        send_error("Nomor HP tidak valid.", 422);
    }
    if ($quantity < 1 || $quantity > MAX_TICKETS_PER_ORDER) {
        send_error("Jumlah tiket harus 1 sampai " . MAX_TICKETS_PER_ORDER . ".", 422);
    }

    // transaksi + FOR UPDATE: dua pembeli bersamaan tidak bisa melebihi kuota
    mysqli_begin_transaction($conn);

    $result = run_query($conn,
        "SELECT c.price, c.quota, k.status, (k.event_date < NOW()) AS is_past
         FROM ticket_categories c
         JOIN concerts k ON k.id = c.concert_id
         WHERE c.id = ?
         FOR UPDATE",
        "i", [$category_id]);
    $category = mysqli_fetch_assoc($result);

    if (!$category) {
        mysqli_rollback($conn);
        send_error("Kategori tiket tidak ditemukan.", 404);
    }
    if ($category["status"] !== "available" || $category["is_past"]) {
        mysqli_rollback($conn);
        send_error("Tiket untuk konser ini sedang tidak dijual.", 409);
    }

    $result = run_query($conn,
        "SELECT COALESCE(SUM(quantity), 0) AS sold
         FROM ticket_orders
         WHERE ticket_category_id = ? AND status = 'paid'",
        "i", [$category_id]);
    $remaining = (int) $category["quota"] - (int) mysqli_fetch_assoc($result)["sold"];

    if ($quantity > $remaining) {
        mysqli_rollback($conn);
        send_error($remaining > 0
            ? "Sisa tiket untuk kategori ini tinggal $remaining."
            : "Tiket untuk kategori ini sudah habis.", 409);
    }

    $unit_price = (float) $category["price"];
    $total      = round($unit_price * $quantity, 2);

    // contoh: WDW-T3K9ZQ-A1B2C3 (waktu + kode acak, supaya sulit ditebak)
    $invoice_no = "WDW-" . strtoupper(base_convert((string) time(), 10, 36))
                . "-" . strtoupper(bin2hex(random_bytes(3)));

    run_query($conn,
        "INSERT INTO ticket_orders
            (invoice_no, ticket_category_id, full_name, email, phone, quantity, unit_price, total)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        "sisssidd",
        [$invoice_no, $category_id, $full_name, $email, $phone, $quantity, $unit_price, $total]);

    mysqli_commit($conn);

    send_json(["invoice_no" => $invoice_no], 201);
}

send_error("Metode tidak didukung.", 405);
