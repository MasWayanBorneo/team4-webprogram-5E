<?php
/* ============================================================================
   HALAMAN ARTIKEL — article.php?slug=album
   Satu file untuk semua berita. Data dari tabel `news` dan `news_categories`.
   Kolom `content` berisi HTML yang ditulis tim (judul, paragraf, kutipan),
   jadi ditampilkan apa adanya; kolom lain di-escape dengan e().
   ============================================================================ */
$root = "../../";
$active = "news";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$slug = trim($_GET["slug"] ?? "");

$article = mysqli_fetch_assoc(db_query($conn,
    "SELECT n.title, n.content, n.thumbnail, n.read_time_min, n.published_at, c.name AS category
     FROM news n
     JOIN news_categories c ON c.id = n.category_id
     WHERE n.slug = ?",
    "s", [$slug]));

if (!$article) {
    http_response_code(404);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article ? e($article["title"]) : "Article not found" ?> | Why Don't We</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/article.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <main>
        <a href="news.php" class="back-link">&#8592; Back to News</a>

        <?php if (!$article): ?>
            <div class="article-header">
                <h1>article not found</h1>
                <p class="meta">This article doesn't exist. See all stories on the <a href="news.php">news page</a>.</p>
            </div>
        <?php else: ?>
            <div class="article-header">
                <p class="category"><?= e($article["category"]) ?></p>
                <h1><?= e($article["title"]) ?></h1>
                <p class="meta">
                    <?= date("F j, Y", strtotime($article["published_at"])) ?>
                    <?php if ($article["read_time_min"]): ?>&bull; <?= (int) $article["read_time_min"] ?> min read<?php endif; ?>
                </p>
            </div>

            <?php if ($article["thumbnail"]): ?>
                <img class="article-hero" src="<?= $root . e($article["thumbnail"]) ?>" alt="<?= e($article["title"]) ?>">
            <?php endif; ?>

            <div class="article-body">
                <?= $article["content"] ?>
            </div>
        <?php endif; ?>
    </main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>
</html>
