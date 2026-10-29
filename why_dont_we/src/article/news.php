<?php
/* ============================================================================
   HALAMAN NEWS — daftar berita dari tabel `news`.
   Berita dengan is_featured = 1 tampil besar di atas; sisanya di grid.
   Setiap kartu mengarah ke article.php?slug=...
   ============================================================================ */
$root = "../../";
$active = "news";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$news = db_fetch_all(db_query($conn,
    "SELECT n.slug, n.title, n.thumbnail, n.read_time_min, n.is_featured, n.published_at, c.name AS category
     FROM news n
     JOIN news_categories c ON c.id = n.category_id
     WHERE n.published_at IS NOT NULL AND n.published_at <= NOW()
     ORDER BY n.published_at DESC"));

$featured = null;
foreach ($news as $i => $item) {
    if ((int) $item["is_featured"] === 1) {
        $featured = $item;
        unset($news[$i]);
        break;
    }
}

// "May 15, 2026 • 3 min read"
function news_meta($item)
{
    $meta = date("F j, Y", strtotime($item["published_at"]));
    if ($item["read_time_min"]) {
        $meta .= " • " . (int) $item["read_time_min"] . " min read";
    }
    return $meta;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why Don't We | News</title>

    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/news.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

<main>

    <!-- Featured News -->
    <section class="featured-section">

        <h1>News</h1>

        <?php if ($featured): ?>
        <a href="article.php?slug=<?= urlencode($featured["slug"]) ?>" style="text-decoration:none;color:inherit;">
        <div class="featured-card">

            <div class="featured-image">
                <img src="<?= $root . e($featured["thumbnail"]) ?>" alt="<?= e($featured["title"]) ?>">
            </div>

            <div class="featured-content">
                <p class="category"><?= e($featured["category"]) ?></p>
                <h2><?= e($featured["title"]) ?></h2>
                <p class="meta"><?= e(news_meta($featured)) ?></p>
            </div>

        </div>
        </a>
        <?php endif; ?>

    </section>

    <!-- All News -->

    <section class="all-news">

        <h2>All News</h2>

        <div class="news-grid">
            <?php foreach ($news as $item): ?>
            <a href="article.php?slug=<?= urlencode($item["slug"]) ?>" style="text-decoration:none;color:inherit;">
            <article class="news-card">

                <img src="<?= $root . e($item["thumbnail"]) ?>" alt="<?= e($item["title"]) ?>">

                <div class="news-content">
                    <p class="category"><?= e($item["category"]) ?></p>
                    <h3><?= e($item["title"]) ?></h3>
                    <p class="meta"><?= e(news_meta($item)) ?></p>
                </div>

            </article>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (!$news && !$featured): ?>
            <p>No news yet.</p>
        <?php endif; ?>

    </section>

    <!-- Subscribe -->

    <section class="subscribe-section" id="newsletter">

        <h2>Stay Updated</h2>

        <p>
            Subscribe for exclusive news,
            updates and merchandise drops.
        </p>

        <?php show_subscribe_message(); ?>

        <form class="subscribe-form" method="post" action="../../actions/subscribe.php">
            <input type="hidden" name="source" value="news">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="100"
                    placeholder="Enter your email"
                    required>

            </div>

            <button type="submit">
                Subscribe
            </button>

        </form>

    </section>

</main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>

</body>
</html>