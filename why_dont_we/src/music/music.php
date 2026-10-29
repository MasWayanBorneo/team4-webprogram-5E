<?php
/* ============================================================================
   HALAMAN MUSIC — semua data dari database:
   - lagu populer (punya file audio) + lirik → tabel `songs`
   - album dan single (punya cover)          → tabel `albums`
   - video YouTube                           → tabel `videos`
   Popup Stream dan lirik memakai atribut HTML `popover`, tanpa JavaScript.
   ============================================================================ */
$root = "../../";
$active = "music";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$songs = db_fetch_all(db_query($conn,
    "SELECT s.id, s.title, s.audio_file, s.lyrics, s.spotify_url,
            COALESCE(s.cover_image, a.cover_image) AS cover_image
     FROM songs s
     LEFT JOIN albums a ON a.id = s.album_id
     WHERE s.audio_file IS NOT NULL
     ORDER BY s.id"));

$albums = db_fetch_all(db_query($conn,
    "SELECT id, title, release_year, cover_image
     FROM albums
     WHERE cover_image IS NOT NULL
     ORDER BY id"));

$videos = db_fetch_all(db_query($conn, "SELECT title, youtube_id, start_second FROM videos ORDER BY id"));

// lirik disimpan sebagai teks; setiap bait (dipisah baris kosong) jadi satu <p>
function lyrics_html($lyrics)
{
    $verses = preg_split("/\R\s*\R/", trim((string) $lyrics));
    $html = "";
    foreach ($verses as $verse) {
        $html .= "<p class=\"verse\">" . nl2br(e(trim($verse))) . "</p>\n";
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Why Dont We Official</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/music.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

<div class="cta">
    <h2>Listen now and get your experience!</h2>
  </div>

  <!-- LAGU POPULER -->
  <h1>See our top popular songs</h1>
  <div class="top-lagu">
    <?php foreach ($songs as $song): ?>
    <div class="player2">
      <div class="imgbox2">
        <?php if ($song["spotify_url"]): ?><a href="<?= e($song["spotify_url"]) ?>" target="_blank"><?php endif; ?>
          <img src="<?= $root . e($song["cover_image"]) ?>" alt="<?= e($song["title"]) ?>">
        <?php if ($song["spotify_url"]): ?></a><?php endif; ?>
      </div>

      <div class="song-title"><h2><?= e($song["title"]) ?></h2></div>
      <button class="tombol" popovertarget="stream-links">Stream</button>
      <?php if ($song["lyrics"]): ?>
        <button class="lirik" popovertarget="lyrics-<?= (int) $song["id"] ?>">Show lyric</button>
      <?php endif; ?>
      <audio src="<?= $root . e($song["audio_file"]) ?>" controls>
        Your browser does not support the audio element.
      </audio>

      <?php if ($song["lyrics"]): ?>
      <div id="lyrics-<?= (int) $song["id"] ?>" class="lyrics" popover>
        <h3><?= e($song["title"]) ?></h3>
        <?= lyrics_html($song["lyrics"]) ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div><!-- /.top-lagu -->

<article class="music-main">
  <!-- ALBUM DAN SINGLE -->
  <div class="description">
    <h1>All albums of Why Don't We</h1>
  </div>
  <div class="players-container">
    <?php foreach ($albums as $album): ?>
    <div class="player">
      <h2><?= e($album["release_year"]) ?></h2>
      <div class="imgbox">
        <img src="<?= $root . e($album["cover_image"]) ?>" alt="<?= e($album["title"]) ?>">
      </div>
      <div class="song-title"><h2><?= e($album["title"]) ?></h2></div>
      <button class="tombol" popovertarget="stream-links">Stream</button>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- popup platform streaming (dipakai semua tombol Stream) -->
  <div id="stream-links" popover>
    <div class="icon2">
      <a href="https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2?si=mBvpmUYUT36VpGjlDODlzw" target="_blank"><img src="../../assets/svg/spotify.svg" alt="Spotify"></a>
      <a href="https://music.apple.com/us/artist/why-dont-we/1161814111" target="_blank"><img src="../../assets/svg/apple.svg" alt="Apple Music"></a>
      <a href="https://music.youtube.com/@whydontwe8155?si=rqwp_HvA8bUYQhs6" target="_blank"><img src="../../assets/svg/ytmusic.svg" alt="YouTube Music"></a>
    </div>
  </div>

  <!-- VIDEO -->
  <h1>all videos</h1>
  <div class="video">
    <?php foreach ($videos as $video): ?>
      <iframe loading="lazy"
              src="https://www.youtube.com/embed/<?= e($video["youtube_id"]) ?>?start=<?= (int) $video["start_second"] ?>"
              title="<?= e($video["title"]) ?>"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    <?php endforeach; ?>
  </div>
</article>

<?php include __DIR__ . "/../../includes/footer.php"; ?>

</body>
</html>