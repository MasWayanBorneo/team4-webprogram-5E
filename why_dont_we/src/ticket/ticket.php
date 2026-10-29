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

        <!-- Page Header -->
        <section class="page-header">
            <h2>Get Your Tickets</h2>
            <p class="ticket-sub">Choose a category and grab your spot before they sell out.</p>
        </section>

        <!-- Ticket Form -->
        <section class="ticket-section">
            <form class="ticket-form" id="ticket-form">

                <div class="form-group">
                    <label for="concert">Concert</label>
                    <select id="concert" name="concert" required>
                        <option value="Jakarta Convention Center (JCC) - 12 August 2026">Jakarta Convention Center (JCC) — 12 August 2026</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" placeholder="Your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="you@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="08xx xxxx xxxx" required>
                </div>

                <!-- Ticket Categories -->
                <fieldset class="ticket-categories">
                    <legend>Ticket Category</legend>

                    <label class="category-card" for="regular">
                        <input type="radio" id="regular" name="category" value="50" data-name="Regular" checked>
                        <div class="category-info">
                            <h3>Regular</h3>
                            <p>Standing area access, official merch discount voucher.</p>
                        </div>
                        <span class="category-price">$50</span>
                    </label>

                    <label class="category-card" for="vip">
                        <input type="radio" id="vip" name="category" value="100" data-name="VIP">
                        <div class="category-info">
                            <h3>VIP</h3>
                            <p>Front-row standing area, exclusive merch pack, meet &amp; greet pass.</p>
                        </div>
                        <span class="category-price">$100</span>
                    </label>
                </fieldset>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required>
                </div>

                <div class="order-summary">
                    <span>Total</span>
                    <span id="total-price">$50</span>
                </div>

                <button type="submit" class="ticket-submit">Proceed to Checkout</button>
            </form>
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
        const form = document.getElementById('ticket-form');
        const quantityInput = document.getElementById('quantity');
        const totalEl = document.getElementById('total-price');

        const usd = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 });

        function formatPrice(amount) {
            return usd.format(Number(amount));
        }

        function updateTotal() {
            const selected = form.querySelector('input[name="category"]:checked');
            const price = Number(selected.value);
            const quantity = Number(quantityInput.value) || 1;
            totalEl.textContent = formatPrice(price * quantity);
        }

        form.querySelectorAll('input[name="category"]').forEach((input) => {
            input.addEventListener('change', updateTotal);
        });
        quantityInput.addEventListener('input', updateTotal);

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const selected = form.querySelector('input[name="category"]:checked');
            const unitPrice = Number(selected.value);
            const quantity = Number(quantityInput.value) || 1;

            const order = {
                invoiceNo: 'WDW-' + Date.now().toString(36).toUpperCase(),
                date: new Date().toISOString(),
                concert: document.getElementById('concert').value,
                fullname: document.getElementById('fullname').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                categoryName: selected.dataset.name,
                unitPrice: unitPrice,
                quantity: quantity,
                total: unitPrice * quantity
            };

            sessionStorage.setItem('wdwTicketOrder', JSON.stringify(order));
            window.location.href = 'invoice.php';
        });

        updateTotal();
    </script>

</body>
</html>
