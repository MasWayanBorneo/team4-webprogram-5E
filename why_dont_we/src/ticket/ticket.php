<?php
require __DIR__ . '/../../includes/db.php';

// konser yang bisa dipesan: available (tiket biasa) dan coming_soon (pre-order)
$concerts = [];
$result = mysqli_query($conn, "SELECT id, venue_name, event_date, status FROM concerts WHERE status IN ('available', 'coming_soon') ORDER BY event_date");
foreach (mysqli_fetch_all($result, MYSQLI_ASSOC) as $row) {
    $concerts[(int) $row['id']] = $row;
}

// konser terpilih: dari form (POST), dari link "Get Ticket" / "Pre Order" (?concert=), atau konser pertama
$concert_id = (int) ($_POST['concert'] ?? $_GET['concert'] ?? 0);
if (!isset($concerts[$concert_id])) {
    $concert_id = $concerts ? (int) array_key_first($concerts) : 0;
}

// pre-order: konser belum mulai dijual, pesanan disimpan 'pending' sampai dikonfirmasi panitia
$is_preorder = $concert_id && $concerts[$concert_id]['status'] === 'coming_soon';

// kategori tiket konser terpilih + sisa kuota (pesanan yang dibatalkan tidak dihitung)
$stmt = mysqli_prepare($conn, "
    SELECT c.id, c.name, c.description, c.price,
           c.quota - COALESCE(SUM(o.quantity), 0) AS remaining
    FROM ticket_categories c
    LEFT JOIN ticket_orders o ON o.ticket_category_id = c.id AND o.status <> 'cancelled'
    WHERE c.concert_id = ?
    GROUP BY c.id
    ORDER BY c.price");
mysqli_stmt_bind_param($stmt, 'i', $concert_id);
mysqli_stmt_execute($stmt);
$categories = [];
foreach (mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC) as $row) {
    $categories[$row['id']] = $row;
}

function concert_label($concert)
{
    return $concert['venue_name'] . ' — ' . date('j F Y', strtotime($concert['event_date']))
        . ($concert['status'] === 'coming_soon' ? ' (Pre-order)' : '');
}

/* --- form → tabel ticket_orders ------------------------------------------- */
$error = '';
$old = ['fullname' => '', 'email' => '', 'phone' => '', 'category' => '', 'quantity' => '1'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $old = [
        'fullname' => trim($_POST['fullname'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'phone'    => trim($_POST['phone'] ?? ''),
        'category' => $_POST['category'] ?? '',
        'quantity' => $_POST['quantity'] ?? '1',
    ];
    $category = $categories[(int) $old['category']] ?? null;
    $quantity = filter_var($old['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);

    if ($old['fullname'] === '' || strlen($old['fullname']) > 100) {
        $error = 'Please enter your full name.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 100) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9+\s-]{8,20}$/', $old['phone'])) {
        $error = 'Please enter a valid phone number.';
    } elseif (!$category) {
        $error = 'Please choose a ticket category.';
    } elseif ($quantity === false) {
        $error = 'You can buy between 1 and 10 tickets.';
    } elseif ($quantity > $category['remaining']) {
        $error = 'Only ' . max(0, $category['remaining']) . ' ' . $category['name'] . ' tickets are left.';
    } else {
        // harga diambil dari database, bukan dari browser, lalu disimpan sebagai unit_price
        $unit_price = $category['price'];
        $total = $unit_price * $quantity;
        $invoice_no = 'WDW-' . strtoupper(bin2hex(random_bytes(4)));

        // belum ada pembayaran online: tiket biasa langsung dianggap lunas ('paid');
        // pre-order 'pending' sampai panitia mengubahnya jadi 'paid' (tiket baru terbit setelah itu)
        $status = $is_preorder ? 'pending' : 'paid';
        $stmt = mysqli_prepare($conn, "
            INSERT INTO ticket_orders (invoice_no, ticket_category_id, full_name, email, phone, quantity, unit_price, total, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sisssidds', $invoice_no, $category['id'], $old['fullname'], $old['email'], $old['phone'], $quantity, $unit_price, $total, $status);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: invoice.php?no=' . urlencode($invoice_no));
            exit;
        }
        $error = 'Something went wrong. Please try again.';
    }
}

// kategori yang dicentang: pilihan sebelumnya, atau kategori pertama
$checked = isset($categories[(int) $old['category']]) ? (int) $old['category'] : (int) array_key_first($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Tickets</title>
    <link rel="stylesheet" href="../css/base.css?v=<?= filemtime(__DIR__ . '/../css/base.css') ?>">
    <link rel="stylesheet" href="../css/tour.css?v=<?= filemtime(__DIR__ . '/../css/tour.css') ?>">
    <link rel="stylesheet" href="../css/ticket.css?v=<?= filemtime(__DIR__ . '/../css/ticket.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        <!-- Page Header -->
        <section class="page-header">
            <?php if ($is_preorder): ?>
            <h2>Pre-order Your Tickets</h2>
            <p class="ticket-sub">Tickets for this show aren't on sale yet. Reserve your spot now — your ticket will be issued once your pre-order is confirmed.</p>
            <?php else: ?>
            <h2>Get Your Tickets</h2>
            <p class="ticket-sub">Choose a category and grab your spot before they sell out.</p>
            <?php endif; ?>
        </section>

        <!-- Ticket Form -->
        <section class="ticket-section">
            <?php if (!$concerts || !$categories): ?>
            <p class="ticket-sub">There are no shows on sale right now. Check the <a href="../tour/tour.php">Tour page</a> for upcoming dates.</p>
            <?php else: ?>
            <form class="ticket-form" id="ticket-form" method="post" action="ticket.php">

                <div class="form-group">
                    <label for="concert">Concert</label>
                    <!-- ganti konser → muat ulang halaman supaya kategori tiketnya ikut berganti -->
                    <select id="concert" name="concert" required onchange="location.href = 'ticket.php?concert=' + this.value">
                        <?php foreach ($concerts as $concert): ?>
                        <option value="<?= $concert['id'] ?>"<?= (int) $concert['id'] === $concert_id ? ' selected' : '' ?>><?= e(concert_label($concert)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" placeholder="Your full name" maxlength="100" value="<?= e($old['fullname']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="you@email.com" maxlength="100" value="<?= e($old['email']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="08xx xxxx xxxx" maxlength="20" value="<?= e($old['phone']) ?>" required>
                </div>

                <!-- Ticket Categories -->
                <fieldset class="ticket-categories">
                    <legend>Ticket Category</legend>

                    <?php foreach ($categories as $category): ?>
                    <label class="category-card" for="category-<?= $category['id'] ?>">
                        <input type="radio" id="category-<?= $category['id'] ?>" name="category" value="<?= $category['id'] ?>" data-price="<?= e($category['price']) ?>"<?= (int) $category['id'] === $checked ? ' checked' : '' ?><?= $category['remaining'] <= 0 ? ' disabled' : '' ?>>
                        <div class="category-info">
                            <h3><?= e($category['name']) ?><?= $category['remaining'] <= 0 ? ' (Sold Out)' : '' ?></h3>
                            <p><?= e($category['description']) ?></p>
                        </div>
                        <span class="category-price">$<?= e(rtrim(rtrim($category['price'], '0'), '.')) ?></span>
                    </label>
                    <?php endforeach; ?>
                </fieldset>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="10" value="<?= e($old['quantity']) ?>" required>
                </div>

                <div class="order-summary">
                    <span>Total</span>
                    <span id="total-price"></span>
                </div>

                <?php if ($error): ?>
                <p class="form-msg form-msg--error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>

                <button type="submit" class="ticket-submit"><?= $is_preorder ? 'Place Pre-order' : 'Proceed to Checkout' ?></button>
            </form>
            <?php endif; ?>
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
        // hanya menghitung total di layar; harga sebenarnya dihitung ulang di server saat form dikirim
        const form = document.getElementById('ticket-form');

        if (form) {
            const quantityInput = document.getElementById('quantity');
            const totalEl = document.getElementById('total-price');

            const usd = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 });

            function updateTotal() {
                const selected = form.querySelector('input[name="category"]:checked');
                const price = selected ? Number(selected.dataset.price) : 0;
                const quantity = Number(quantityInput.value) || 1;
                totalEl.textContent = usd.format(price * quantity);
            }

            form.querySelectorAll('input[name="category"]').forEach((input) => {
                input.addEventListener('change', updateTotal);
            });
            quantityInput.addEventListener('input', updateTotal);

            updateTotal();
        }
    </script>

</body>
</html>
