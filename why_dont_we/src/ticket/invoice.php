<?php
require __DIR__ . '/../../includes/db.php';

// invoice.php?no=WDW-XXXXXXXX — dibuka setelah pesan tiket di ticket.php
$invoice_no = $_GET['no'] ?? '';

$stmt = mysqli_prepare($conn, "
    SELECT o.invoice_no, o.created_at, o.full_name, o.email, o.phone,
           o.quantity, o.unit_price, o.total, o.status, o.checked_in_at,
           c.name AS category, k.venue_name, k.event_date
    FROM ticket_orders o
    JOIN ticket_categories c ON c.id = o.ticket_category_id
    JOIN concerts k ON k.id = c.concert_id
    WHERE o.invoice_no = ?");
mysqli_stmt_bind_param($stmt, 's', $invoice_no);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// diteruskan ke JavaScript di bawah dengan nama field yang sama seperti sebelumnya
$order = $row ? [
    'invoiceNo'    => $row['invoice_no'],
    'date'         => date('c', strtotime($row['created_at'])),
    'concert'      => $row['venue_name'] . ' — ' . date('j F Y', strtotime($row['event_date'])),
    'fullname'     => $row['full_name'],
    'email'        => $row['email'],
    'phone'        => $row['phone'],
    'categoryName' => $row['category'],
    'unitPrice'    => (float) $row['unit_price'],
    'quantity'     => (int) $row['quantity'],
    'total'        => (float) $row['total'],
    'status'       => $row['status'],
] : null;

// tampilan tiap status pesanan: [label status, class tambahan, label total, catatan]
// paid      = tiket biasa / pre-order yang sudah dikonfirmasi → tiket dengan sobekan terbit
// pending   = pre-order yang belum dikonfirmasi panitia → belum ada tiket
// cancelled = dibatalkan → tidak berlaku
$status_views = [
    'paid'      => ['Paid', '', 'Total Paid',
                    'Please bring this invoice and a valid ID to the venue entrance. E-tickets will also be sent to your email.'],
    'pending'   => ['Pre-order · Awaiting confirmation', ' invoice-status--pending', 'Total Due',
                    'This is a pre-order. Your ticket will be issued on this page once the committee confirms your pre-order.'],
    'cancelled' => ['Cancelled', ' invoice-status--cancelled', 'Total',
                    'This order has been cancelled and is no longer valid.'],
];
[$status_label, $status_class, $total_label, $status_note] = $status_views[$order['status'] ?? 'paid'] ?? $status_views['paid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="../css/base.css?v=<?= filemtime(__DIR__ . '/../css/base.css') ?>">
    <link rel="stylesheet" href="../css/tour.css?v=<?= filemtime(__DIR__ . '/../css/tour.css') ?>">
    <link rel="stylesheet" href="../css/invoice.css?v=<?= filemtime(__DIR__ . '/../css/invoice.css') ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- pembuat kode QR untuk tiket di PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js" integrity="sha512-ZDSPMa/JM1D+7kdg2x3BsruQ6T/JpJo3jWDWkCZsP+5yVyp1KfESqLI+7RqB5k24F7p2cV7i2YHh/890y6P6Sw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../css/header.css?v=<?= filemtime(__DIR__ . '/../css/header.css') ?>">
    <link rel="stylesheet" href="../css/footer.css?v=<?= filemtime(__DIR__ . '/../css/footer.css') ?>">
</head>
<body>

<!-- ============================ HEADER ============================ -->
<header class="site-header">
  <nav class="site-nav" aria-label="Main">
    <a class="site-nav__logo" href="../../index.php"><img src="../../assets/image/about-logo.png" alt="Why Don't We"></a>
    <ul class="site-nav__links">
      <li><a href="../../index.php">Home</a></li>
      <li><a href="../member/profile.php">About</a></li>
      <li><a href="../music/music.php">Music</a></li>
      <li><a href="../tour/tour.php" class="is-active" aria-current="page">Tour</a></li>
      <li><a href="../merch/merch.php">Merch</a></li>
      <li><a href="../article/news.php">News</a></li>
    </ul>
  </nav>
</header>

    <main>

        <!-- No order found -->
        <section class="page-header" id="empty-state" hidden>
            <h2>No Invoice Found</h2>
            <p class="ticket-sub">We couldn't find a recent order. Please book a ticket first.</p>
            <a href="ticket.php" class="ticket-link">Get a Ticket</a>
        </section>

        <!-- Invoice -->
        <section class="invoice-section" id="invoice-section" hidden>

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
                        <p><strong>Invoice No.</strong> <span id="inv-number"></span></p>
                        <p><strong>Date</strong> <span id="inv-date"></span></p>
                        <p class="invoice-status<?= $status_class ?>"><?= $status_label ?></p>
                        <?php if ($row && $row['checked_in_at']): ?>
                        <!-- sudah check-in di pintu masuk: terlihat juga oleh panitia yang memindai QR dengan kamera HP -->
                        <p class="invoice-status invoice-status--cancelled">Used · checked in <?= date('j M Y, H:i', strtotime($row['checked_in_at'])) ?></p>
                        <?php endif; ?>
                    </div>
                </header>

                <section class="invoice-billto">
                    <h3>Billed To</h3>
                    <p id="inv-name"></p>
                    <p id="inv-email"></p>
                    <p id="inv-phone"></p>
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
                            <td id="inv-concert"></td>
                            <td id="inv-category"></td>
                            <td id="inv-qty"></td>
                            <td id="inv-unit"></td>
                            <td id="inv-subtotal"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="invoice-total">
                    <span><?= $total_label ?></span>
                    <span id="inv-total"></span>
                </div>

                <p class="invoice-note"><?= $status_note ?></p>
            </div>

            <div class="invoice-actions">
                <button type="button" id="print-btn">Download Invoice (PDF)</button>
                <a href="ticket.php" class="ticket-link">Book Another Ticket</a>
            </div>

        </section>

    </main>

    <!-- ============================ FOOTER ============================ -->
    <footer class="site-footer">
  <div class="shell">
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="brand" href="../../index.php"><img src="../../assets/image/about-logo.png" alt="Why Don't We"></a>
        <p>Official site. Pop's five-piece — music, tour, and more.</p>
      </div>
      <nav class="footer-col" aria-label="Music">
        <h4>Music</h4>
        <ul><li><a href="../music/music.php">Albums</a></li><li><a href="../music/music.php">Singles</a></li><li><a href="../article/article.php?slug=new-music-video-released">Videos</a></li></ul>
      </nav>
      <nav class="footer-col" aria-label="Band">
        <h4>Band</h4>
        <ul><li><a href="../member/profile.php">About</a></li><li><a href="../tour/tour.php">Tour</a></li><li><a href="../article/news.php">News</a></li></ul>
      </nav>
      <nav class="footer-col" aria-label="More">
        <h4>More</h4>
        <ul><li><a href="../merch/merch.php">Merch</a></li><li><a href="ticket.php">Tickets</a></li><li><a href="../../index.php">Home</a></li></ul>
      </nav>
    </div>

    <div class="footer-bottom">
      <p>© 2026 Why Don't We. All rights reserved.</p>
      <ul class="socials" aria-label="Social media">
        <li><a href="https://www.instagram.com/whydontwemusic?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.7 0 3 0 4.1.1 1 .1 1.7.2 2.3.5.6.2 1.1.5 1.6 1 .5.5.8 1 1 1.6.2.6.4 1.3.5 2.3.1 1.1.1 1.4.1 4.1s0 3-.1 4.1c-.1 1-.2 1.7-.5 2.3a4.3 4.3 0 0 1-1 1.6 4.3 4.3 0 0 1-1.6 1c-.6.2-1.3.4-2.3.5-1.1.1-1.4.1-4.1.1s-3 0-4.1-.1c-1-.1-1.7-.2-2.3-.5a4.3 4.3 0 0 1-1.6-1 4.3 4.3 0 0 1-1-1.6c-.2-.6-.4-1.3-.5-2.3C2 15 2 14.7 2 12s0-3 .1-4.1c.1-1 .2-1.7.5-2.3.2-.6.5-1.1 1-1.6.5-.5 1-.8 1.6-1 .6-.2 1.3-.4 2.3-.5C9 2 9.3 2 12 2zm0 1.8c-2.7 0-3 0-4 .1-.9 0-1.4.2-1.7.3-.4.2-.7.4-1 .7-.3.3-.5.6-.7 1-.1.3-.3.8-.3 1.7-.1 1-.1 1.3-.1 4s0 3 .1 4c0 .9.2 1.4.3 1.7.2.4.4.7.7 1 .3.3.6.5 1 .7.3.1.8.3 1.7.3 1 .1 1.3.1 4 .1s3 0 4-.1c.9 0 1.4-.2 1.7-.3.4-.2.7-.4 1-.7.3-.3.5-.6.7-1 .1-.3.3-.8.3-1.7.1-1 .1-1.3.1-4s0-3-.1-4c0-.9-.2-1.4-.3-1.7a2.7 2.7 0 0 0-.7-1 2.7 2.7 0 0 0-1-.7c-.3-.1-.8-.3-1.7-.3-1-.1-1.3-.1-4-.1zm0 3.1a5.1 5.1 0 1 1 0 10.2 5.1 5.1 0 0 1 0-10.2zm0 1.8a3.3 3.3 0 1 0 0 6.6 3.3 3.3 0 0 0 0-6.6zm5.3-3.1a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4z"/></svg></a></li>
        <li><a href="https://web.facebook.com/whydontwemusic/?_rdc=1&_rdr#" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg></a></li>
        <li><a href="https://x.com/whydontwemusic" aria-label="Twitter"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 4.9c-.8.4-1.7.6-2.6.8a4.5 4.5 0 0 0 2-2.5c-.9.5-1.9.9-2.9 1.1a4.5 4.5 0 0 0-7.7 4.1A12.8 12.8 0 0 1 2.5 3.7a4.5 4.5 0 0 0 1.4 6 4.5 4.5 0 0 1-2-.6v.1a4.5 4.5 0 0 0 3.6 4.4 4.5 4.5 0 0 1-2 .1 4.5 4.5 0 0 0 4.2 3.1A9 9 0 0 1 1 18.6a12.8 12.8 0 0 0 6.9 2c8.3 0 12.8-6.9 12.8-12.8v-.6c.9-.6 1.6-1.4 2.3-2.3z"/></svg></a></li>
        <li><a href="https://www.tiktok.com/@whydontwemusic" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.3 2.2 1.6 3.6 3.8 3.8v2.6c-1.3.1-2.6-.3-3.8-1v5.6a5.8 5.8 0 1 1-5-5.7v2.8a3.1 3.1 0 1 0 2.2 3V3h2.8z"/></svg></a></li>
      </ul>
    </div>
  </div>
</footer>

    <script>
        const usd = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 });

        function formatPrice(amount) {
            return usd.format(Number(amount));
        }

        // gambar kode QR langsung sebagai kotak-kotak hitam di PDF (tetap tajam saat di-print);
        // dilewati kalau library QR gagal dimuat
        function drawQr(doc, text, x, y, size) {
            if (!window.qrcode) return;
            const qr = qrcode(0, 'M');
            qr.addData(text);
            qr.make();
            const count = qr.getModuleCount();
            const cell = size / count;
            doc.setFillColor(0, 0, 0);
            for (let row = 0; row < count; row++) {
                for (let colIndex = 0; colIndex < count; colIndex++) {
                    if (qr.isDark(row, colIndex)) {
                        // +0.02 mm supaya tidak ada garis putih tipis di antara kotak
                        doc.rect(x + colIndex * cell, y + row * cell, cell + 0.02, cell + 0.02, 'F');
                    }
                }
            }
        }

        const order = <?= json_encode($order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
        const totalLabel = <?= json_encode($total_label) ?>;
        const statusNote = <?= json_encode($status_note, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        // label status di PDF: [teks, warna RGB]
        const pdfStatus = {
            paid: ['PAID', [76, 175, 80]],
            pending: ['PRE-ORDER - AWAITING CONFIRMATION', [200, 140, 0]],
            cancelled: ['CANCELLED', [200, 50, 40]],
        }[order?.status] || ['PAID', [76, 175, 80]];

        if (!order) {
            document.getElementById('empty-state').hidden = false;
        } else {
            document.getElementById('invoice-section').hidden = false;

            document.getElementById('inv-number').textContent = order.invoiceNo;
            document.getElementById('inv-date').textContent = new Date(order.date).toLocaleString('en-US', {
                dateStyle: 'long',
                timeStyle: 'short'
            });

            document.getElementById('inv-name').textContent = order.fullname;
            document.getElementById('inv-email').textContent = order.email;
            document.getElementById('inv-phone').textContent = order.phone;

            document.getElementById('inv-concert').textContent = order.concert;
            document.getElementById('inv-category').textContent = order.categoryName;
            document.getElementById('inv-qty').textContent = order.quantity;
            document.getElementById('inv-unit').textContent = formatPrice(order.unitPrice);
            document.getElementById('inv-subtotal').textContent = formatPrice(order.total);
            document.getElementById('inv-total').textContent = formatPrice(order.total);
        }

        document.getElementById('print-btn').addEventListener('click', () => {
            if (!order) {
                alert('No invoice data found.');
                return;
            }

            const btn = document.getElementById('print-btn');
            const originalLabel = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Generating PDF...';

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ unit: 'mm', format: 'a4' });

                const marginX = 20;
                const rightX = 190;
                let y = 20;

                doc.setFont('helvetica', 'bold');
                doc.setFontSize(20);
                doc.setTextColor(20, 30, 45);
                doc.text("Why Don't We", marginX, y);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(11);
                doc.setTextColor(110, 110, 110);
                y += 7;
                doc.text('Official Ticket Invoice', marginX, y);

                doc.setFontSize(11);
                doc.setTextColor(40, 40, 40);
                doc.text('Invoice No. ' + order.invoiceNo, rightX, 20, { align: 'right' });
                doc.text('Date: ' + new Date(order.date).toLocaleString('en-US', {
                    dateStyle: 'long',
                    timeStyle: 'short'
                }), rightX, 27, { align: 'right' });

                doc.setFont('helvetica', 'bold');
                doc.setTextColor(...pdfStatus[1]);
                doc.text(pdfStatus[0], rightX, 34, { align: 'right' });

                y += 15;
                doc.setDrawColor(200, 200, 200);
                doc.line(marginX, y, rightX, y);

                y += 10;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.setTextColor(20, 30, 45);
                doc.text('Billed To', marginX, y);

                y += 7;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(11);
                doc.setTextColor(60, 60, 60);
                doc.text(order.fullname || '-', marginX, y);
                y += 6;
                doc.text(order.email || '-', marginX, y);
                y += 6;
                doc.text(order.phone || '-', marginX, y);

                y += 14;
                // posisi kolom (mm). Description & Category rata kiri; Qty, Unit Price,
                // dan Subtotal rata kanan supaya angka sejajar dan judulnya tidak bertumpuk
                const col = { desc: marginX, cat: 92, qty: 128, unit: 158, sub: rightX };
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.setTextColor(110, 110, 110);
                doc.text('DESCRIPTION', col.desc, y);
                doc.text('CATEGORY', col.cat, y);
                doc.text('QTY', col.qty, y, { align: 'right' });
                doc.text('UNIT PRICE', col.unit, y, { align: 'right' });
                doc.text('SUBTOTAL', col.sub, y, { align: 'right' });

                y += 3;
                doc.setDrawColor(220, 220, 220);
                doc.line(marginX, y, rightX, y);

                y += 8;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.setTextColor(30, 30, 30);

                // teks panjang dipecah per baris; tinggi baris tabel mengikuti kolom yang
                // paling banyak barisnya, supaya baris kedua tidak menabrak garis & total.
                // Tanda "—" diganti "-" karena tidak ada di font bawaan jsPDF.
                const lineHeight = 5;
                const descLines = doc.splitTextToSize((order.concert || '-').replace(/—/g, '-'), col.cat - col.desc - 6);
                const catLines = doc.splitTextToSize(order.categoryName || '-', col.qty - col.cat - 14);
                doc.text(descLines, col.desc, y, { lineHeightFactor: 1.15 });
                doc.text(catLines, col.cat, y, { lineHeightFactor: 1.15 });
                doc.text(String(order.quantity ?? '-'), col.qty, y, { align: 'right' });
                doc.text(formatPrice(order.unitPrice), col.unit, y, { align: 'right' });
                doc.text(formatPrice(order.total), col.sub, y, { align: 'right' });

                y += (Math.max(descLines.length, catLines.length) - 1) * lineHeight + 8;
                doc.setDrawColor(220, 220, 220);
                doc.line(marginX, y, rightX, y);

                y += 14;
                doc.setFillColor(230, 245, 255);
                doc.roundedRect(marginX, y - 8, rightX - marginX, 14, 3, 3, 'F');
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(13);
                doc.setTextColor(20, 30, 45);
                doc.text(totalLabel, marginX + 6, y + 1);
                doc.text(formatPrice(order.total), rightX - 6, y + 1, { align: 'right' });

                y += 20;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.setTextColor(130, 130, 130);
                doc.text(statusNote, marginX, y, { maxWidth: rightX - marginX });

                // tiket dengan sobekan hanya untuk pesanan lunas; pre-order (pending) dan
                // pesanan batal hanya mendapat invoice tanpa tiket
                if (order.status !== 'paid') {
                    doc.save(order.invoiceNo + '.pdf');
                    return;
                }

                /* ---- TIKET DENGAN SOBEKAN --------------------------------------------
                   Kiri: bagian pengunjung. Kanan: "Committee Copy" yang disobek panitia di
                   pintu masuk dan disimpan sebagai bukti, lalu pengunjung menerima gelang
                   sebanyak jumlah tiket. Tiket tanpa sobekan berarti sudah dipakai.
                   QR berisi alamat invoice ini, jadi panitia bisa memindainya untuk
                   memastikan tiketnya terdaftar. */
                const qty = Number(order.quantity) || 1;
                const verifyUrl = new URL('invoice.php?no=' + encodeURIComponent(order.invoiceNo), location.href).href;
                const ticketTop = Math.max(y + 16, 160);
                const ticketHeight = 78;
                const stubX = 140; // posisi garis sobekan (mm)

                // satu isian tiket: label kecil abu-abu + isi tebal satu baris
                const field = (label, value, x, fieldY, width) => {
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(7.5);
                    doc.setTextColor(120, 120, 120);
                    doc.text(label, x, fieldY);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(10.5);
                    doc.setTextColor(25, 25, 25);
                    doc.text(doc.splitTextToSize(String(value), width)[0], x, fieldY + 4.5);
                };

                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.setTextColor(20, 30, 45);
                doc.text('Your Ticket', marginX, ticketTop - 5);

                // bingkai tiket
                doc.setDrawColor(40, 40, 40);
                doc.setLineWidth(0.4);
                doc.roundedRect(marginX, ticketTop, rightX - marginX, ticketHeight, 3, 3, 'S');

                // garis sobekan putus-putus + tulisan vertikal di sampingnya
                doc.setLineDashPattern([1.5, 1.5], 0);
                doc.line(stubX, ticketTop, stubX, ticketTop + ticketHeight);
                doc.setLineDashPattern([], 0);
                doc.setLineWidth(0.2);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(6.5);
                doc.setTextColor(140, 140, 140);
                doc.text('CUT HERE  -  TEAR OFF AT ENTRANCE', stubX - 1.3, ticketTop + ticketHeight - 6, { angle: 90 });

                // bagian pengunjung
                const left = marginX + 6;
                const mainQrSize = 30;
                const mainQrX = stubX - 8 - mainQrSize;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(15);
                doc.setTextColor(20, 30, 45);
                doc.text("WHY DON'T WE", left, ticketTop + 10);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8);
                doc.setTextColor(120, 120, 120);
                doc.text('CONCERT TICKET  -  VISITOR COPY', left, ticketTop + 15);

                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.setTextColor(25, 25, 25);
                const concertLines = doc.splitTextToSize((order.concert || '-').replace(/—/g, '-'), mainQrX - left - 4).slice(0, 2);
                doc.text(concertLines, left, ticketTop + 24, { lineHeightFactor: 1.15 });

                field('NAME', order.fullname || '-', left, ticketTop + 41, mainQrX - left - 4);
                field('CATEGORY', order.categoryName || '-', left, ticketTop + 54, 34);
                field('ADMITS', qty + (qty === 1 ? ' person' : ' people'), left + 40, ticketTop + 54, 34);
                field('INVOICE NO.', order.invoiceNo, left, ticketTop + 67, 60);

                drawQr(doc, verifyUrl, mainQrX, ticketTop + 12, mainQrSize);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.setTextColor(120, 120, 120);
                doc.text('Scan to verify', mainQrX + mainQrSize / 2, ticketTop + 47, { align: 'center' });

                // bagian panitia (disobek)
                const stubLeft = stubX + 5;
                const stubCenter = (stubX + rightX) / 2;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(8);
                doc.setTextColor(120, 120, 120);
                doc.text('COMMITTEE COPY', stubLeft, ticketTop + 9);
                doc.setFontSize(10);
                doc.setTextColor(25, 25, 25);
                doc.text(order.invoiceNo, stubLeft, ticketTop + 15);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8.5);
                doc.text(doc.splitTextToSize(order.fullname || '-', rightX - stubLeft - 4).slice(0, 2), stubLeft, ticketTop + 21, { lineHeightFactor: 1.15 });
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(9);
                doc.text((order.categoryName || '-') + '  x ' + qty, stubLeft, ticketTop + 32);

                drawQr(doc, verifyUrl, stubCenter - 12, ticketTop + 36, 24);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(9);
                doc.setTextColor(25, 25, 25);
                doc.text('WRISTBANDS: ' + qty, stubCenter, ticketTop + 67, { align: 'center' });
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.setTextColor(120, 120, 120);
                doc.text('Kept by the committee', stubCenter, ticketTop + 72, { align: 'center' });

                // petunjuk di bawah tiket
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8.5);
                doc.setTextColor(110, 110, 110);
                doc.text(
                    'At the entrance, staff will tear off the committee copy and give you ' + qty +
                    (qty === 1 ? ' wristband' : ' wristbands') +
                    '. A ticket without its committee copy has already been used and is no longer valid.',
                    marginX, ticketTop + ticketHeight + 8, { maxWidth: rightX - marginX }
                );

                doc.save(order.invoiceNo + '.pdf');
            } catch (err) {
                console.error('Failed to generate invoice PDF', err);
                alert('Sorry, something went wrong while generating the PDF. Please try again.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalLabel;
            }
        });
    </script>

</body>
</html>
