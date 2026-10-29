<?php
/* ============================================================================
   HALAMAN INVOICE — invoice.php?invoice=WDW-...
   Data pesanan diambil dari `ticket_orders` (join kategori dan konser).
   Tombol "Download Invoice (PDF)" membuka invoice-pdf.php, yang membuat
   file PDF di server dengan FPDF.
   ============================================================================ */
$root = "../../";
$active = "tour";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$invoice_no = trim($_GET["invoice"] ?? "");
$order = null;

if ($invoice_no !== "") {
    $order = mysqli_fetch_assoc(db_query($conn,
        "SELECT o.invoice_no, o.created_at, o.full_name, o.email, o.phone,
                o.quantity, o.unit_price, o.total,
                c.name AS category_name, k.venue_name, k.event_date
         FROM ticket_orders o
         JOIN ticket_categories c ON c.id = o.ticket_category_id
         JOIN concerts k ON k.id = c.concert_id
         WHERE o.invoice_no = ?",
        "s", [$invoice_no]));
}

if ($order) {
    $concert_label = $order["venue_name"] . " - " . format_date($order["event_date"]);
    $order_date    = date("F j, Y \a\\t g:i A", strtotime($order["created_at"]));

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/tour.css">
    <link rel="stylesheet" href="../css/invoice.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <main>

        <!-- No order found -->
        <?php if (!$order): ?>
        <section class="page-header" id="empty-state">
            <h2>No Invoice Found</h2>
            <p class="ticket-sub">We couldn't find a recent order. Please book a ticket first.</p>
            <a href="ticket.php" class="ticket-link">Get a Ticket</a>
        </section>
        <?php else: ?>

        <!-- Invoice -->
        <section class="invoice-section" id="invoice-section">

            <div class="invoice-card" id="invoice-card">
                <header class="invoice-head">
                    <div class="invoice-brand">
                        <img src="../../assets/image/about-logo.png" alt="WDW Logo" class="invoice-logo">
                        <div>
                            <h2>Why Don't We</h2>
                            <p>Official Ticket Invoice</p>
                        </div>
                    </div>
                    <div class="invoice-meta">
                        <p><strong>Invoice No.</strong> <span id="inv-number"><?= e($order["invoice_no"]) ?></span></p>
                        <p><strong>Date</strong> <span id="inv-date"><?= e($order_date) ?></span></p>
                        <p class="invoice-status">Paid</p>
                    </div>
                </header>

                <section class="invoice-billto">
                    <h3>Billed To</h3>
                    <p id="inv-name"><?= e($order["full_name"]) ?></p>
                    <p id="inv-email"><?= e($order["email"]) ?></p>
                    <p id="inv-phone"><?= e($order["phone"]) ?></p>
                </section>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="inv-concert"><?= e($concert_label) ?></td>
                            <td id="inv-category"><?= e($order["category_name"]) ?></td>
                            <td id="inv-qty"><?= (int) $order["quantity"] ?></td>
                            <td id="inv-unit"><?= money($order["unit_price"]) ?></td>
                            <td id="inv-subtotal"><?= money($order["total"]) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="invoice-total">
                    <span>Total Paid</span>
                    <span id="inv-total"><?= money($order["total"]) ?></span>
                </div>

                <p class="invoice-note">Please bring this invoice and a valid ID to the venue entrance. E-tickets will also be sent to your email.</p>
            </div>

            <div class="invoice-actions">
                <a href="invoice-pdf.php?invoice=<?= urlencode($order["invoice_no"]) ?>" id="print-btn">Download Invoice (PDF)</a>
                <a href="ticket.php" class="ticket-link">Book Another Ticket</a>
            </div>

        </section>
        <?php endif; ?>

    </main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>


</body>
</html>
