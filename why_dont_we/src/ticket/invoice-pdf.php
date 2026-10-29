<?php
/* ============================================================================
   PDF INVOICE — invoice-pdf.php?invoice=WDW-...
   Membuat file PDF invoice di server dengan library FPDF (lib/fpdf), lalu
   browser langsung mengunduhnya. Dipanggil dari tombol "Download Invoice (PDF)"
   di invoice.php.
   ============================================================================ */

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";
require __DIR__ . "/../../lib/fpdf/fpdf.php";

$invoice_no = trim($_GET["invoice"] ?? "");

$order = mysqli_fetch_assoc(db_query($conn,
    "SELECT o.invoice_no, o.created_at, o.full_name, o.email, o.phone,
            o.quantity, o.unit_price, o.total,
            c.name AS category_name, k.venue_name, k.event_date
     FROM ticket_orders o
     JOIN ticket_categories c ON c.id = o.ticket_category_id
     JOIN concerts k ON k.id = c.concert_id
     WHERE o.invoice_no = ?",
    "s", [$invoice_no]));

if (!$order) {
    http_response_code(404);
    die("Invoice tidak ditemukan.");
}

// font bawaan FPDF memakai encoding Windows-1252, bukan UTF-8
function pdf_text($text)
{
    return iconv("UTF-8", "windows-1252//TRANSLIT", (string) $text);
}

$concert_label = $order["venue_name"] . " - " . format_date($order["event_date"]);
$order_date    = date("F j, Y \a\\t g:i A", strtotime($order["created_at"]));

$pdf = new FPDF("P", "mm", "A4");
$pdf->SetTitle("Invoice " . $order["invoice_no"], true);
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

$left  = 20;
$right = 190;
$width = $right - $left;

// ---- judul + info invoice ---------------------------------------------------
$pdf->SetFont("Helvetica", "B", 20);
$pdf->SetTextColor(20, 30, 45);
$pdf->Cell(100, 9, pdf_text("Why Don't We"));

$pdf->SetFont("Helvetica", "", 11);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 7, pdf_text("Invoice No. " . $order["invoice_no"]), 0, 1, "R");

$pdf->SetTextColor(110, 110, 110);
$pdf->Cell(100, 7, pdf_text("Official Ticket Invoice"));
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 7, pdf_text("Date: " . $order_date), 0, 1, "R");

$pdf->SetFont("Helvetica", "B", 11);
$pdf->SetTextColor(76, 175, 80);
$pdf->Cell(0, 7, "PAID", 0, 1, "R");

$pdf->Ln(4);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line($left, $pdf->GetY(), $right, $pdf->GetY());
$pdf->Ln(8);

// ---- billed to --------------------------------------------------------------
$pdf->SetFont("Helvetica", "B", 12);
$pdf->SetTextColor(20, 30, 45);
$pdf->Cell(0, 7, "Billed To", 0, 1);

$pdf->SetFont("Helvetica", "", 11);
$pdf->SetTextColor(60, 60, 60);
foreach ([$order["full_name"], $order["email"], $order["phone"]] as $line) {
    $pdf->Cell(0, 6, pdf_text($line), 0, 1);
}
$pdf->Ln(8);

// ---- tabel tiket ------------------------------------------------------------
$cols = ["desc" => 70, "cat" => 30, "qty" => 15, "unit" => 27, "sub" => 28]; // total 170 mm

$pdf->SetFont("Helvetica", "B", 9);
$pdf->SetTextColor(110, 110, 110);
$pdf->Cell($cols["desc"], 7, "DESCRIPTION");
$pdf->Cell($cols["cat"], 7, "CATEGORY");
$pdf->Cell($cols["qty"], 7, "QTY");
$pdf->Cell($cols["unit"], 7, "UNIT PRICE");
$pdf->Cell($cols["sub"], 7, "SUBTOTAL", 0, 1, "R");

$pdf->SetDrawColor(220, 220, 220);
$pdf->Line($left, $pdf->GetY(), $right, $pdf->GetY());
$pdf->Ln(3);

// kolom deskripsi bisa lebih dari satu baris; kolom lain sejajar baris pertamanya
$row_y = $pdf->GetY();
$pdf->SetFont("Helvetica", "", 10.5);
$pdf->SetTextColor(30, 30, 30);
$pdf->MultiCell($cols["desc"] - 4, 6, pdf_text($concert_label));
$row_end = $pdf->GetY();

$pdf->SetXY($left + $cols["desc"], $row_y);
$pdf->Cell($cols["cat"], 6, pdf_text($order["category_name"]));
$pdf->Cell($cols["qty"], 6, (string) (int) $order["quantity"]);
$pdf->Cell($cols["unit"], 6, pdf_text(money($order["unit_price"])));
$pdf->Cell($cols["sub"], 6, pdf_text(money($order["total"])), 0, 1, "R");

$pdf->SetY(max($row_end, $pdf->GetY()) + 3);
$pdf->Line($left, $pdf->GetY(), $right, $pdf->GetY());
$pdf->Ln(8);

// ---- total ------------------------------------------------------------------
$pdf->SetFillColor(230, 245, 255);
$pdf->SetFont("Helvetica", "B", 13);
$pdf->SetTextColor(20, 30, 45);
$pdf->Cell($width / 2, 12, "  Total Paid", 0, 0, "L", true);
$pdf->Cell($width / 2, 12, pdf_text(money($order["total"])) . "  ", 0, 1, "R", true);
$pdf->Ln(8);

// ---- catatan ----------------------------------------------------------------
$pdf->SetFont("Helvetica", "", 9);
$pdf->SetTextColor(130, 130, 130);
$pdf->MultiCell(0, 5, pdf_text(
    "Please bring this invoice and a valid ID to the venue entrance. E-tickets will also be sent to your email."
));

// "D" = download dengan nama file WDW-....pdf
$pdf->Output("D", $order["invoice_no"] . ".pdf");
