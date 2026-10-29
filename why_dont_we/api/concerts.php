<?php
/* ============================================================================
   API KONSER
   GET api/concerts.php          → semua konser (untuk halaman Tour dan Ticket)
   GET api/concerts.php?id=1     → satu konser + kategori tiket dan sisa kuotanya
                                   (untuk halaman Ticket)
   ============================================================================ */

require __DIR__ . "/helpers.php";

// ubah satu baris konser ke tipe aslinya
function format_concert($row)
{
    $row["id"]      = (int) $row["id"];
    $row["is_past"] = (bool) $row["is_past"];
    return $row;
}

$columns = "id, venue_name, city, country, region, event_date, image, status,
            (event_date < NOW()) AS is_past";

// ---- satu konser + kategori tiket ------------------------------------------
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    $result  = run_query($conn, "SELECT $columns FROM concerts WHERE id = ?", "i", [$id]);
    $concert = mysqli_fetch_assoc($result);

    if (!$concert) {
        send_error("Konser tidak ditemukan.", 404);
    }

    // sisa kuota = kuota - jumlah tiket yang sudah terjual (status paid)
    $result = run_query($conn,
        "SELECT c.id, c.name, c.description, c.price,
                c.quota - COALESCE(SUM(o.quantity), 0) AS remaining
         FROM ticket_categories c
         LEFT JOIN ticket_orders o
                ON o.ticket_category_id = c.id AND o.status = 'paid'
         WHERE c.concert_id = ?
         GROUP BY c.id
         ORDER BY c.price",
        "i", [$id]);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row["id"]        = (int) $row["id"];
        $row["price"]     = (float) $row["price"];
        $row["remaining"] = max(0, (int) $row["remaining"]);
        $categories[] = $row;
    }

    $concert = format_concert($concert);
    $concert["categories"] = $categories;
    send_json($concert);
}

// ---- semua konser -----------------------------------------------------------
$result = run_query($conn, "SELECT $columns FROM concerts ORDER BY event_date");

$concerts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $concerts[] = format_concert($row);
}

send_json($concerts);
