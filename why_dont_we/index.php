<?php
require __DIR__ . '/includes/db.php';

// The Band
$members = mysqli_fetch_all(mysqli_query($conn, 'SELECT slug, full_name, roles, photo FROM members ORDER BY sort_order'), MYSQLI_ASSOC);

// On Tour: 3 konser pertama yang belum sold out (sama seperti halaman Tour & tiket)
$concerts = mysqli_fetch_all(mysqli_query($conn, "
    SELECT id, venue_name, city, country, event_date, status
    FROM concerts
    WHERE status <> 'sold_out'
    ORDER BY event_date
    LIMIT 3"), MYSQLI_ASSOC);

// Merch: 4 produk, yang is_featured lebih dulu
$products = mysqli_fetch_all(mysqli_query($conn, 'SELECT name, price, image FROM products WHERE is_active = 1 ORDER BY is_featured DESC, id LIMIT 4'), MYSQLI_ASSOC);

// Latest News: 3 berita terbaru
$latest_news = mysqli_fetch_all(mysqli_query($conn, 'SELECT slug, title, excerpt, thumbnail, published_at FROM news ORDER BY published_at DESC, id DESC LIMIT 3'), MYSQLI_ASSOC);

// 65.00 → $65, 12.50 → $12.50
function price($amount)
{
    return '$' . (floor($amount) == $amount ? number_format($amount) : number_format($amount, 2));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Why Don't We — official site. New music, world tour dates, and the latest news from the five-piece pop group.">
<title>Why Don't We — Official</title>
<?php
// background animasi memakai animated WebP, bukan GIF: server TinkerHost menolak file .gif
// (403 Forbidden) sementara format gambar lain tetap bisa dibuka.
// Alamatnya diberi versi (?v=waktu file diubah), sama seperti CSS, supaya browser tidak
// memakai respons error lama dari cache (hosting menyimpannya sampai 30 hari).
$home_bg = 'assets/home-bg.webp?v=' . filemtime(__DIR__ . '/assets/home-bg.webp');
?>
<!-- background diunduh lebih awal, tidak menunggu CSS selesai dibaca -->
<link rel="preload" href="<?= $home_bg ?>" as="image" type="image/webp">
<link rel="stylesheet" href="src/css/base.css?v=<?= filemtime(__DIR__ . '/src/css/base.css') ?>">
<link rel="stylesheet" href="src/css/home.css?v=<?= filemtime(__DIR__ . '/src/css/home.css') ?>">
<style>
  /* gambar background ditulis di sini (bukan di home.css) supaya alamatnya bisa diberi versi dari PHP */
  body { background-image: url(<?= $home_bg ?>); }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="src/css/header.css?v=<?= filemtime(__DIR__ . '/src/css/header.css') ?>">
<link rel="stylesheet" href="src/css/footer.css?v=<?= filemtime(__DIR__ . '/src/css/footer.css') ?>">
</head>
<body>

<!-- ============================ HEADER ============================ -->
<header class="site-header">
  <nav class="site-nav" aria-label="Main">
    <a class="site-nav__logo" href="index.php"><img src="assets/image/about-logo.png" alt="Why Don't We"></a>
    <ul class="site-nav__links">
      <li><a href="index.php" class="is-active" aria-current="page">Home</a></li>
      <li><a href="src/member/profile.php">About</a></li>
      <li><a href="src/music/music.php">Music</a></li>
      <li><a href="src/tour/tour.php">Tour</a></li>
      <li><a href="src/merch/merch.php">Merch</a></li>
      <li><a href="src/article/news.php">News</a></li>
    </ul>
  </nav>
</header>

<main>

  <!-- ============================ HERO ============================ -->
  <section class="hero" aria-label="Why Don't We">
    <div class="shell hero__inner">
      <a class="hero__badge" href="#release"><span class="dot"></span> New single — &ldquo;8 Letters&rdquo; out now</a>
      <p class="eyebrow">Pop · Five-piece · Worldwide</p>
      <h1 class="hero__name">Why<br>Don't We</h1>
      <p class="hero__tag">Five voices, one sound. New music, a world tour, and everything in between — straight from the band.</p>
      <div class="hero__cta">
        <a class="btn btn--primary" href="#music"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>Listen Now</a>
        <a class="btn btn--ghost" href="#tour">Tour Dates</a>
      </div>
    </div>
  </section>

  <!-- ============================ LATEST RELEASE ============================ -->
  <section class="section" id="release" aria-labelledby="release-h">
    <div class="shell">
      <div class="release">
        <figure class="art art--1 release__art">
          <img src="assets/image/8letters.jpg" alt="8 Letters album cover">
        </figure>


        <div class="release__meta">
          <p class="release__kicker" id="release-h">Latest release</p>
          <h2 class="release__title">8 Letters</h2>
          <p class="release__sub">The album — 13 tracks. Out now on every platform.</p>
          <div class="streams">
            <a class="stream-chip" href="https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>Spotify</a>
            <a class="stream-chip" href="https://music.apple.com/us/artist/why-dont-we/1161814111"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>Apple Music</a>
            <a class="stream-chip" href="http://www.youtube.com/@whydontwe8155"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>YouTube</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================ FEATURED SONGS ============================ -->
  <section class="section" id="music" aria-labelledby="music-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="music-h">Featured Songs</h2>
        <a class="head-link" href="src/music/music.php">All music →</a>
      </header>

      <ul class="songs">
        <li class="song"><a href="src/music/music.php">
          <img src="assets/image/8letters.jpg" alt="8 Letters cover">
          <h3 class="song__name">8 Letters</h3><p class="song__len">3:34</p>
        </a></li>
        <li class="song"><a href="src/music/music.php">
          <img src="assets/image/fallin.jpg" alt="Fallin' cover">
          <h3 class="song__name">Fallin'</h3><p class="song__len">2:46</p>
        </a></li>
        <li class="song"><a href="src/music/music.php">
          <img src="assets/image/HDYLS-new.jpg" alt="How Do You Love Somebody cover">
          <h3 class="song__name">How Do You Love Somebody</h3><p class="song__len">3:18</p>
        </a></li>
        <li class="song"><a href="src/music/music.php">
          <img src="assets/image/flower.jpg" alt="Let Me Down Easy (Lie) cover">
          <h3 class="song__name">Let Me Down Easy (Lie)</h3><p class="song__len">2:51</p>
        </a></li>
      </ul>
    </div>
  </section>

  <!-- ============================ MEMBERS ============================ -->
  <section class="section" aria-labelledby="members-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="members-h">The Band</h2>
        <a class="head-link" href="src/member/profile.php">Full bios →</a>
      </header>

      <ul class="members">
        <?php foreach ($members as $member): ?>
        <li class="member"><a href="src/member/member.php?m=<?= e($member['slug']) ?>">
          <img src="<?= e($member['photo']) ?>" alt="<?= e($member['full_name']) ?>">
          <h3 class="member__name"><?= e($member['full_name']) ?></h3><p class="member__role"><?= e($member['roles']) ?></p>
        </a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================ TOUR ============================ -->
  <section class="section" id="tour" aria-labelledby="tour-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="tour-h">On Tour</h2>
        <a class="head-link" href="src/tour/tour.php">All dates →</a>
      </header>

      <ul class="tour-list">
        <?php foreach ($concerts as $concert): ?>
        <li class="tour-row">
          <div class="tour-date"><span class="day"><?= date('j', strtotime($concert['event_date'])) ?></span><span class="mon"><?= date('M', strtotime($concert['event_date'])) ?></span></div>
          <div class="tour-venue"><div class="v"><?= e($concert['venue_name']) ?></div><div class="c"><?= e($concert['city'] . ', ' . $concert['country']) ?></div></div>
          <?php if ($concert['status'] === 'available'): ?>
          <a class="btn btn--primary" href="src/ticket/ticket.php?concert=<?= $concert['id'] ?>">Get Tickets</a>
          <?php else: ?>
          <a class="btn btn--primary" href="src/ticket/ticket.php?concert=<?= $concert['id'] ?>">Pre Order</a>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
        <?php if (!$concerts): ?>
        <li class="tour-row">
          <div class="tour-venue"><div class="v">No upcoming shows</div><div class="c">New dates will be announced soon.</div></div>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </section>

  <!-- ============================ MERCH ============================ -->
  <section class="section" id="merch" aria-labelledby="merch-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="merch-h">Merch</h2>
        <a class="head-link" href="src/merch/merch.php">Shop all →</a>
      </header>

      <ul class="products">
        <?php foreach ($products as $product): ?>
        <li class="product"><a href="src/merch/merch.php">
          <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
          <h3 class="product__name"><?= e($product['name']) ?></h3><p class="product__price"><?= price($product['price']) ?></p>
        </a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================ NEWS ============================ -->
  <section class="section" id="news" aria-labelledby="news-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="news-h">Latest News</h2>
        <a class="head-link" href="src/article/news.php">All news →</a>
      </header>

      <ul class="news">
        <?php foreach ($latest_news as $item): ?>
        <li><a href="src/article/article.php?slug=<?= e($item['slug']) ?>"><article class="news-card">
          <img src="<?= e($item['thumbnail']) ?>" alt="">
          <div class="news-card__body">
            <time datetime="<?= date('Y-m-d', strtotime($item['published_at'])) ?>"><?= date('F Y', strtotime($item['published_at'])) ?></time>
            <h3><?= e($item['title']) ?></h3>
            <p><?= e($item['excerpt']) ?></p>
          </div>
        </article></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================ VIDEO ============================ -->
  <section class="section" aria-labelledby="video-h">
    <div class="shell">
      <header class="section-head">
        <h2 class="section-title" id="video-h">Watch</h2>
        <a class="head-link" href="http://www.youtube.com/@whydontwe8155">YouTube →</a>
      </header>

      <ul class="videos">
        <li class="art art--1 video play-tile"><img src="assets/image/jfthumbnail.jpg" alt=""><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video one on YouTube"></a></li>
        <li class="art art--5 video play-tile"><img src="assets/image/hdylsthumb-5_0.jpg" alt=""><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video two on YouTube"></a></li>
        <li class="art art--2 video play-tile"><img src="assets/image/LIETHUMBNAIL-9_0.jpg" alt=""><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video three on YouTube"></a></li>
      </ul>
    </div>
  </section>

  <!-- ============================ NEWSLETTER ============================ -->
  <section class="section" id="newsletter" aria-labelledby="news-signup-h">
    <div class="shell">
      <div class="newsletter">
        <h2 id="news-signup-h">Never miss a drop</h2>
        <p>Presale codes, tour news, and merch first. Straight to your inbox.</p>
        <form class="subscribe" method="post" action="api/subscribe.php">
          <input type="hidden" name="source" value="home">
          <label for="email" class="sr-only">Email address</label>
          <input id="email" name="email" type="email" maxlength="100" placeholder="you@email.com" required>
          <button class="subscribe-btn" type="submit">Sign up</button>
        </form>
        <?php include __DIR__ . '/includes/subscribe-message.php'; ?>
      </div>
    </div>
  </section>

</main>

<!-- ============================ FOOTER ============================ -->
<footer class="site-footer">
  <div class="shell">
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="brand" href="index.php"><img src="assets/image/about-logo.png" alt="Why Don't We"></a>
        <p>Official site. Pop's five-piece — music, tour, and more.</p>
      </div>
      <nav class="footer-col" aria-label="Music">
        <h4>Music</h4>
        <ul><li><a href="src/music/music.php">Albums</a></li><li><a href="src/music/music.php">Singles</a></li><li><a href="src/article/article.php?slug=new-music-video-released">Videos</a></li></ul>
      </nav>
      <nav class="footer-col" aria-label="Band">
        <h4>Band</h4>
        <ul><li><a href="src/member/profile.php">About</a></li><li><a href="src/tour/tour.php">Tour</a></li><li><a href="src/article/news.php">News</a></li></ul>
      </nav>
      <nav class="footer-col" aria-label="More">
        <h4>More</h4>
        <ul><li><a href="src/merch/merch.php">Merch</a></li><li><a href="src/ticket/ticket.php">Tickets</a></li><li><a href="index.php">Home</a></li></ul>
      </nav>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> Why Don't We. All rights reserved.</p>
      <ul class="socials">
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
