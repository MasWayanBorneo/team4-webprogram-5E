<?php
/* ============================================================================
   HEADER BERSAMA — dipanggil oleh setiap halaman:
       <?php include __DIR__ . "/../../includes/header.php"; ?>
   Halaman harus mengisi dua variabel sebelum memanggil file ini:
       $root   → path dari halaman ke root project, mis. "../../" (index: "")
       $active → menu yang aktif: home, about, music, tour, merch, atau news
   ============================================================================ */

$nav_links = [
    "home"  => ["Home",  "index.php"],
    "about" => ["About", "src/member/profile.php"],
    "music" => ["Music", "src/music/music.php"],
    "tour"  => ["Tour",  "src/tour/tour.php"],
    "merch" => ["Merch", "src/merch/merch.php"],
    "news"  => ["News",  "src/article/news.php"],
];
?>
<!-- ============================ HEADER ============================ -->
<header class="site-header">
  <nav class="site-nav" aria-label="Main">
    <a class="site-nav__logo" href="<?= $root ?>index.php"><img src="<?= $root ?>assets/image/about-logo.png" alt="Why Don't We"></a>
    <ul class="site-nav__links">
<?php foreach ($nav_links as $key => $link): ?>
      <li><a href="<?= $root . $link[1] ?>"<?= $key === $active ? ' class="is-active" aria-current="page"' : "" ?>><?= $link[0] ?></a></li>
<?php endforeach; ?>
    </ul>
  </nav>
</header>
