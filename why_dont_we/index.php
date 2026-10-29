<?php
/* ============================================================================
   HALAMAN HOME — bagian "On Tour" diambil dari tabel `concerts`;
   form newsletter dikirim ke actions/subscribe.php.
   ============================================================================ */
$root = "";
$active = "home";

require __DIR__ . "/config/database.php";
require __DIR__ . "/includes/functions.php";

// 3 konser terdekat yang belum lewat
$upcoming = db_fetch_all(db_query($conn,
    "SELECT id, venue_name, city, country, event_date, status
     FROM concerts
     WHERE event_date >= NOW()
     ORDER BY event_date
     LIMIT 3"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Why Don't We — official site. New music, world tour dates, and the latest news from the five-piece pop group.">
<title>Why Don't We — Official</title>
<link rel="stylesheet" href="src/css/base.css">
<link rel="stylesheet" href="src/css/home.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="src/css/header.css">
<link rel="stylesheet" href="src/css/footer.css">
</head>
<body>

<?php include __DIR__ . "/includes/header.php"; ?>

  <!-- ============================ HERO ============================ -->
  <section class="hero" aria-label="Why Don't We">
    
    <div class="hero__bg" aria-hidden="true"></div>
    <!-- stage-light beams (decorative) -->
    <div class="hero__grain" aria-hidden="true"></div>

    <div class="shell hero__inner">
    
      <a class="hero__badge" href="#music"><span class="dot"></span> New single — &ldquo;8 Letters&rdquo; out now</a>
      <p class="eyebrow" style="margin:0;">Pop · Five-piece · Worldwide</p>
      <h1 class="hero__name">Why<br>Don't We</h1>
      <p class="hero__tag">Five voices, one sound. New music, a world tour, and everything in between — straight from the band.</p>
      <div class="hero__cta">
        <a class="btn btn--primary" href="#music"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>Listen Now</a>
        <a class="btn btn--ghost" href="#tour">Tour Dates</a>
      </div>
    </div>
  </section>

  <!-- ============================ LATEST RELEASE ============================ -->
  <section class="section" id="about" aria-labelledby="release-h">
    <div class="shell">
      <div class="release">    
        <figure class="art art--1 release__art" aria-label="Album cover placeholder">      
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
        <li class="song">
          <img src="assets/image/8letters.jpg">
          <h3 class="song__name">8 Letters</h3><p class="song__len">3:34</p>
        </li>
        <li class="song">
           <img src="assets/image/fallin.png">
          <h3 class="song__name">Big Plans</h3><p class="song__len">2:51</p>
        </li>
        <li class="song">
           <img src="assets/image/HDYLS-new.jpg">
          <h3 class="song__name">Chills</h3><p class="song__len">3:18</p>
        </li>
        <li class="song">
           <img src="assets/image/flower.jpg">
          <h3 class="song__name">Fallin'</h3><p class="song__len">2:46</p>
        </li>
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
        <li class="member">
          <a href="src/member/jonah.php">
          <img src="assets/image/jonah.jpg" ></a>
          <h3 class="member__name">Jonah Marais</h3><p class="member__role">Vocals</p>
        </li>
        <li class="member">
          <a href="src/member/corbyn.php">
           <img src="assets/image/besson-corbyn-image.jpg"></a>
          <h3 class="member__name">Corbyn Besson</h3><p class="member__role">Vocals</p>
        </li>
        <li class="member">
             <a href="src/member/daniel.php">
           <img src="assets/image/daniel.jpg"></a>
          <h3 class="member__name">Daniel Seavey</h3><p class="member__role">Vocals · Multi</p>
        </li>
        <li class="member">
           <a href="src/member/jack.php">
           <img src="assets/image/jack.jpg"></a>
          <h3 class="member__name">Jack Avery</h3><p class="member__role">Vocals</p>
        </li>
        <li class="member">
          <a href="src/member/zach.php">
           <img src="assets/image/zach.jpg"></a>
          <h3 class="member__name">Zach Herron</h3><p class="member__role">Vocals</p>
        </li>
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
        <?php foreach ($upcoming as $concert): ?>
        <li class="tour-row">
          <div class="tour-date"><span class="day"><?= date("d", strtotime($concert["event_date"])) ?></span><span class="mon"><?= date("M", strtotime($concert["event_date"])) ?></span></div>
          <div class="tour-venue"><div class="v"><?= e($concert["venue_name"]) ?></div><div class="c"><?= e($concert["city"]) ?>, <?= e($concert["country"]) ?></div></div>
          <?php if ($concert["status"] === "available"): ?>
            <a class="btn btn--primary" href="src/ticket/ticket.php?concert=<?= (int) $concert["id"] ?>">Get Tickets</a>
          <?php else: ?>
            <span class="tour-soldout"><?= $concert["status"] === "coming_soon" ? "Coming soon" : "Sold out" ?></span>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
        <?php if (!$upcoming): ?>
        <li class="tour-row"><div class="tour-venue"><div class="c">New dates coming soon.</div></div></li>
        <?php endif; ?>
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

      <ul class="news" >
        <li><a href="src/article/article-tour-dates.php"><article class="news-card">
            <img src="assets/image/lovebackcover1_0.jpg">
          <div class="news-card__body">
            <time datetime="2026-06">June 2026</time>
            <h3>World tour dates announced</h3>
            <p>Southeast Asia leg goes on sale Friday — full routing inside.</p>
          </div>
        </article></a></li>
        <li><a href="src/article/article-international-tour.php"><article class="news-card">
          <img src="assets/image/why-dont-we-just.png" >
          <div class="news-card__body">
            <time datetime="2026-05">May 2026</time>
            <h3>&ldquo;8 Letters&rdquo; goes platinum</h3>
            <p>A thank-you to everyone who streamed, sang, and showed up.</p>
          </div>
        </article></a></li>
        <li><a href="src/article/article-video.php"><article class="news-card">
           <img src="assets/image/featured-news.png">
          <div class="news-card__body">
            <time datetime="2026-04">April 2026</time>
            <h3>Behind the scenes: the new video</h3>
            <p>Two days, one set, and the making of the latest single.</p>
          </div>
        </article></a></li>
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
        
        <li class="art art--1 video play-tile" ><img src="assets/image/jfthumbnail.jpg" ><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video one on YouTube"></a></li>
        <li class="art art--5 video play-tile" ><img src="assets/image/hdylsthumb-5_0.jpg"><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video two on YouTube"></a></li>
        <li class="art art--2 video play-tile"><img src="assets/image/LIETHUMBNAIL-9_0.jpg"><a class="play-btn" href="https://www.youtube.com/@whydontwe8155" target="_blank" rel="noopener" aria-label="Watch video three on YouTube"></a></li>
      </ul>
    </div>
  </section>

  <!-- ============================ NEWSLETTER ============================ -->
  <section class="section" id="merch" aria-labelledby="news-signup-h">
    <div class="shell">
      <div class="newsletter" id="newsletter">
        <h2 id="news-signup-h">Never miss a drop</h2>
        <p>Presale codes, tour news, and merch first. Straight to your inbox.</p>
        <?php show_subscribe_message(); ?>
        <form class="subscribe" method="post" action="actions/subscribe.php">
          <input type="hidden" name="source" value="home">
          <label for="email" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);">Email address</label>
          <input id="email" name="email" type="email" placeholder="you@email.com" maxlength="100" required>
          <button class="subscribe-btn" type="submit">Sign up</button>
        </form>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>


</body>
</html>