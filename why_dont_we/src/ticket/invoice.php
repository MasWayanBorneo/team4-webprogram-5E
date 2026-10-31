<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/tour.css">
    <link rel="stylesheet" href="../css/invoice.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
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
                        <p class="invoice-status">Paid</p>
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
                    <span>Total Paid</span>
                    <span id="inv-total"></span>
                </div>

                <p class="invoice-note">Please bring this invoice and a valid ID to the venue entrance. E-tickets will also be sent to your email.</p>
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
        <ul><li><a href="../music/music.php">Albums</a></li><li><a href="../music/music.php">Singles</a></li><li><a href="../article/article-video.php">Videos</a></li></ul>
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

        const order = JSON.parse(sessionStorage.getItem('wdwTicketOrder') || 'null');

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
                doc.setTextColor(76, 175, 80);
                doc.text('PAID', rightX, 34, { align: 'right' });

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
                const col = { desc: marginX, cat: 90, qty: 125, unit: 145, sub: 175 };
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.setTextColor(110, 110, 110);
                doc.text('DESCRIPTION', col.desc, y);
                doc.text('CATEGORY', col.cat, y);
                doc.text('QTY', col.qty, y);
                doc.text('UNIT PRICE', col.unit, y);
                doc.text('SUBTOTAL', col.sub, y, { align: 'right' });

                y += 3;
                doc.setDrawColor(220, 220, 220);
                doc.line(marginX, y, rightX, y);

                y += 8;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10.5);
                doc.setTextColor(30, 30, 30);
                doc.text(order.concert || '-', col.desc, y, { maxWidth: col.cat - col.desc - 4 });
                doc.text(order.categoryName || '-', col.cat, y);
                doc.text(String(order.quantity ?? '-'), col.qty, y);
                doc.text(formatPrice(order.unitPrice), col.unit, y);
                doc.text(formatPrice(order.total), col.sub, y, { align: 'right' });

                y += 8;
                doc.setDrawColor(220, 220, 220);
                doc.line(marginX, y, rightX, y);

                y += 14;
                doc.setFillColor(230, 245, 255);
                doc.roundedRect(marginX, y - 8, rightX - marginX, 14, 3, 3, 'F');
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(13);
                doc.setTextColor(20, 30, 45);
                doc.text('Total Paid', marginX + 6, y + 1);
                doc.text(formatPrice(order.total), rightX - 6, y + 1, { align: 'right' });

                y += 20;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.setTextColor(130, 130, 130);
                doc.text(
                    'Please bring this invoice and a valid ID to the venue entrance. E-tickets will also be sent to your email.',
                    marginX, y, { maxWidth: rightX - marginX }
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
