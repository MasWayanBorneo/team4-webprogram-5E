<?php
require __DIR__ . '/../../includes/db.php';

$members = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM members ORDER BY sort_order'), MYSQLI_ASSOC);

// kartu benefit Limelight; benefit baru cukup ditambah sebagai baris di tabel
$benefits = mysqli_fetch_all(mysqli_query($conn, 'SELECT icon, title, description FROM limelight_benefits WHERE is_active = 1 ORDER BY sort_order, id'), MYSQLI_ASSOC);

/* --- form Limelight → tabel limelight_members ----------------------------- */
$ll_error = '';
$ll_old = ['name' => '', 'email' => '', 'dob' => '', 'favorite' => ''];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $ll_old = [
        'name'     => trim($_POST['name'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'dob'      => $_POST['dob'] ?? '',
        'favorite' => $_POST['favorite'] ?? '',
    ];
    $dob = DateTime::createFromFormat('Y-m-d', $ll_old['dob']);

    if ($ll_old['name'] === '' || strlen($ll_old['name']) > 100) {
        $ll_error = 'Please enter your full name.';
    } elseif (!filter_var($ll_old['email'], FILTER_VALIDATE_EMAIL) || strlen($ll_old['email']) > 100) {
        $ll_error = 'Please enter a valid email address.';
    } elseif (!$dob || $dob->format('Y-m-d') !== $ll_old['dob'] || $dob > new DateTime()) {
        $ll_error = 'Please enter a valid date of birth.';
    } else {
        // favorite_member_id boleh kosong
        $favorite = $ll_old['favorite'] === '' ? null : (int) $ll_old['favorite'];

        $stmt = mysqli_prepare($conn, 'INSERT INTO limelight_members (full_name, email, date_of_birth, favorite_member_id) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sssi', $ll_old['name'], $ll_old['email'], $ll_old['dob'], $favorite);

        if (mysqli_stmt_execute($stmt)) {
            // redirect supaya refresh tidak mengirim form dua kali
            header('Location: profile.php?joined=1#limelight-form');
            exit;
        }
        $ll_error = mysqli_stmt_errno($stmt) === 1062
            ? 'This email is already registered as a Limelight.'
            : 'Something went wrong. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why Don't We - Members</title>
    <link rel="stylesheet" href="../css/base.css?v=<?= filemtime(__DIR__ . '/../css/base.css') ?>">
    <link rel="stylesheet" href="../css/profile.css?v=<?= filemtime(__DIR__ . '/../css/profile.css') ?>">
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
      <li><a href="profile.php" class="is-active" aria-current="page">About</a></li>
      <li><a href="../music/music.php">Music</a></li>
      <li><a href="../tour/tour.php">Tour</a></li>
      <li><a href="../merch/merch.php">Merch</a></li>
      <li><a href="../article/news.php">News</a></li>
    </ul>
  </nav>
</header>

    <div class="app">
        <div class="page-header">
            <p class="section-label">Why Don't We</p>
            <p class="page-title">About</p>
        </div>

        <div class="content about-section">
            <p class="section-label">Who We Are</p>
            <h2 class="section-heading">About the Band</h2>
            <p class="about-text">
                Why Don't We is a five-piece group built on harmony, friendship, and a shared
                love of songwriting. Blending pop hooks with live-band energy, the guys write
                and produce much of their own music, performing it for fans — called
                Limelights all over the world.
            </p>
        </div>

        <div class="content history-section">
            <p class="section-label">Our Journey</p>
            <h2 class="section-heading">History</h2>
            <ol class="history-timeline">
                <li class="timeline-item">
                    <span class="timeline-year">2016</span>
                    <p>Five solo creators came together and formed Why Don't We, releasing
                        their debut single "Taking You" the same year.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">2018</span>
                    <p>Signed with Island Records and released their debut studio album,
                        <em>8 Letters</em>, which landed in the Billboard 200 Top 10.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">2019</span>
                    <p>Released <em>The Good Times and the Bad Ones</em>, growing into a
                        more personal and mature sound.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">Today</span>
                    <p>Continues to write, record, and tour worldwide — staying close to
                        Limelights through music and live shows.</p>
                </li>
            </ol>
        </div>

        <div class="content gallery-section">
            <p class="section-label">Behind The Scenes</p>
            <h2 class="section-heading">Gallery</h2>
            <div class="gallery-grid">
                <img src="../../assets/image/wdw-cover-final.jpg" alt="Why Don't We cover art" class="gallery-img">
                <img src="../../assets/image/8letters.jpg" alt="8 Letters album era" class="gallery-img">
                <img src="../../assets/image/HDYLS-new.jpg" alt="How Do You Love? era" class="gallery-img">
                <img src="../../assets/image/Polaroid5_0.png" alt="Band polaroid" class="gallery-img">
                <img src="../../assets/image/polaroid-1.png" alt="Band polaroid" class="gallery-img">
                <img src="../../assets/image/album2.jpg" alt="Album artwork" class="gallery-img">
                <img src="../../assets/image/why-dont-we-just.jpg" alt="Why Don't We Just era" class="gallery-img">
                <img src="../../assets/image/only-the-begining.jpg" alt="Only the Beginning era" class="gallery-img">
            </div>
        </div>

        <div class="content members-section">
            <p class="section-label">The Band</p>
            <h2 class="section-heading">Members</h2>

            <div class="content">
                <?php foreach ($members as $m): ?>
                <a href="member.php?m=<?= e($m['slug']) ?>" class="member-card">
                    <div class="avatar">
                        <img src="../../<?= e($m['photo']) ?>" alt="<?= e($m['full_name']) ?>" class="avatar-img">
                    </div>
                    <div class="member-info">
                        <p class="member-name"><?= e($m['full_name']) ?></p>
                        <p class="member-role"><?= e($m['roles']) ?></p>
                    </div>
                    <span class="member-arrow">›</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="content limelight-section">
            <p class="section-label">Join The Fam</p>
            <h2 class="section-heading">Limelight Membership</h2>
            <p class="about-text">
                Limelight is the official Why Don't We fan membership built for the
                Limelights who support the guys every step of the way. Join to get closer
                to the band, unlock exclusive perks, and connect with fans around the world.
            </p>

            <div class="limelight-grid">
                <?php foreach ($benefits as $benefit): ?>
                <div class="benefit-card">
                    <i class="fa-solid <?= e($benefit['icon']) ?> benefit-icon"></i>
                    <p class="benefit-title"><?= e($benefit['title']) ?></p>
                    <p class="benefit-desc"><?= e($benefit['description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- form tertutup sampai CTA diklik; tetap terbuka setelah kirim form
                 supaya pesan berhasil / error langsung terlihat -->
            <details class="limelight-join" id="limelight-form"<?= $ll_error || isset($_GET['joined']) ? ' open' : '' ?>>
            <summary class="limelight-cta">
                <i class="fa-solid fa-star" aria-hidden="true"></i>
                <span>Become a Limelight</span>
            </summary>

            <form class="limelight-form" method="post" action="profile.php#limelight-form">

                <div class="form-row">
                    <div class="form-group">
                        <label for="ll-name">Full Name</label>
                        <input type="text" id="ll-name" name="name" placeholder="Your full name" maxlength="100" value="<?= e($ll_old['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ll-email">Email</label>
                        <input type="email" id="ll-email" name="email" placeholder="you@email.com" maxlength="100" value="<?= e($ll_old['email']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ll-dob">Date of Birth</label>
                        <input type="date" id="ll-dob" name="dob" max="<?= date('Y-m-d') ?>" value="<?= e($ll_old['dob']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ll-favorite">Favorite Member</label>
                        <select id="ll-favorite" name="favorite">
                            <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>"<?= (string) $m['id'] === $ll_old['favorite'] ? ' selected' : '' ?>><?= e($m['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-join">Join Limelight</button>
                <?php if (isset($_GET['joined'])): ?>
                <p class="limelight-confirm">Welcome to Limelight! Check your email to confirm your membership.</p>
                <?php elseif ($ll_error): ?>
                <p class="limelight-confirm limelight-error"><?= e($ll_error) ?></p>
                <?php endif; ?>
            </form>
            </details>
        </div>
    </div>
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
        <ul><li><a href="profile.php">About</a></li><li><a href="../tour/tour.php">Tour</a></li><li><a href="../article/news.php">News</a></li></ul>
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