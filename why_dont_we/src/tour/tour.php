<?php
require __DIR__ . '/../../includes/db.php';

// tombol filter: tour.php?region=asia, kosong = semua region
$regions = ['asia' => 'Asia', 'america' => 'America', 'europe' => 'Europe', 'other' => 'Other'];
$region = $_GET['region'] ?? '';

if (isset($regions[$region])) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM concerts WHERE region = ? ORDER BY event_date');
    mysqli_stmt_bind_param($stmt, 's', $region);
    mysqli_stmt_execute($stmt);
    $concerts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
} else {
    $region = '';
    $concerts = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM concerts ORDER BY event_date'), MYSQLI_ASSOC);
}

// status di database → [class CSS, teks label]
$statuses = [
    'available'   => ['available', 'Available'],
    'coming_soon' => ['coming', 'Coming Soon'],
    'sold_out'    => ['sold', 'Sold Out'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Page</title>
    <link rel="stylesheet" href="../css/base.css?v=<?= filemtime(__DIR__ . '/../css/base.css') ?>">
    <link rel="stylesheet" href="../css/tour.css?v=<?= filemtime(__DIR__ . '/../css/tour.css') ?>">
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
      <li><a href="tour.php" class="is-active" aria-current="page">Tour</a></li>
      <li><a href="../merch/merch.php">Merch</a></li>
      <li><a href="../article/news.php">News</a></li>
    </ul>
  </nav>
</header>

    <main>

        <!-- Page Header -->
        <section class="page-header">
            
                 <h2>
                    Find a show near you and get your ticket
                    before they sell out!

                </h2>
                    <div class="hero-banner">
                
                <img src="../../assets/image/maxresdefault-1-0.jpg" alt="">
            </div>
        </section>

        <!-- Filter -->
        <section class="filter-section">
        

            <form class="filters" method="get" action="tour.php">
                <button type="submit" name="region" value=""<?= $region === '' ? ' aria-pressed="true"' : '' ?>>All</button>
                <?php foreach ($regions as $value => $label): ?>
                <button type="submit" name="region" value="<?= $value ?>"<?= $region === $value ? ' aria-pressed="true"' : '' ?>><?= $label ?></button>
                <?php endforeach; ?>
            </form>
        </section>

        <!-- Tour Listing -->
        <section class="tour-section">

            <?php if (!$concerts): ?>
            <p>No shows scheduled in this region yet.</p>
            <?php endif; ?>

            <?php foreach ($concerts as $concert): ?>
            <?php [$status_class, $status_label] = $statuses[$concert['status']]; ?>
            <article class="tour-card">
                <img src="../../<?= e($concert['image']) ?>" alt="<?= e($concert['venue_name']) ?>">

                <div class="tour-info">
                    <h3><?= e($concert['venue_name']) ?></h3>
                    <p><?= e($concert['city']) ?></p>
                    <p><?= date('j F Y', strtotime($concert['event_date'])) ?></p>
                </div>

                <div class="tour-status">
                    <span class="<?= $status_class ?>"><?= $status_label ?></span>
                    <?php if ($concert['status'] === 'available'): ?>
                    <a href="../ticket/ticket.php?concert=<?= $concert['id'] ?>" class="ticket-link">Get Ticket</a>
                    <?php elseif ($concert['status'] === 'coming_soon'): ?>
                    <button>Pre Order</button>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>

        </section>

        <!-- Archive -->
        <section class="archive-section">
            <h2>Past Tour Archive</h2>

            <select>
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
            </select>
        </section>

        <!-- Newsletter -->
        <!-- susunan sama dengan form subscribe di halaman News -->
        <section class="newsletter" id="newsletter">

            <h2>Stay Updated</h2>

            <p>
                Be the first to know about new tour dates.
                Join the mailing list.
            </p>

            <form class="newsletter-form" method="post" action="../../api/subscribe.php">
                <input type="hidden" name="source" value="tour">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <button type="submit">
                    Subscribe
                </button>
            </form>
            <?php include __DIR__ . '/../../includes/subscribe-message.php'; ?>
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
        <ul><li><a href="../member/profile.php">About</a></li><li><a href="tour.php">Tour</a></li><li><a href="../article/news.php">News</a></li></ul>
      </nav>
      <nav class="footer-col" aria-label="More">
        <h4>More</h4>
        <ul><li><a href="../merch/merch.php">Merch</a></li><li><a href="../ticket/ticket.php">Tickets</a></li><li><a href="../../index.php">Home</a></li></ul>
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

</body>
</html>