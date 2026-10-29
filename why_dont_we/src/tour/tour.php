<?php
/* ============================================================================
   HALAMAN TOUR — konser diambil dari tabel `concerts`.
   ?region=asia      → filter konser yang akan datang per region
   ?year=2026        → tahun yang ditampilkan di arsip konser yang sudah lewat
   ============================================================================ */
$root = "../../";
$active = "tour";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$regions = ["all" => "All", "asia" => "Asia", "america" => "America", "europe" => "Europe", "other" => "Other"];
$region  = $_GET["region"] ?? "all";
if (!isset($regions[$region])) {
    $region = "all";
}

// konser yang akan datang
if ($region === "all") {
    $upcoming = db_fetch_all(db_query($conn,
        "SELECT * FROM concerts WHERE event_date >= NOW() ORDER BY event_date"));
} else {
    $upcoming = db_fetch_all(db_query($conn,
        "SELECT * FROM concerts WHERE event_date >= NOW() AND region = ? ORDER BY event_date",
        "s", [ucfirst($region)]));
}

// tahun-tahun yang punya konser di masa lalu, terbaru dulu
$years = array_column(db_fetch_all(db_query($conn,
    "SELECT DISTINCT YEAR(event_date) AS year FROM concerts WHERE event_date < NOW() ORDER BY year DESC")), "year");

$year = (int) ($_GET["year"] ?? ($years[0] ?? 0));
$past = db_fetch_all(db_query($conn,
    "SELECT * FROM concerts WHERE event_date < NOW() AND YEAR(event_date) = ? ORDER BY event_date DESC",
    "i", [$year]));

// status di database → [class badge, teks badge]
$status_badges = [
    "available"   => ["available", "Available"],
    "coming_soon" => ["coming", "Coming Soon"],
    "sold_out"    => ["sold", "Sold Out"],
];

// tampilkan satu kartu konser
function concert_card($concert, $is_past)
{
    global $root, $status_badges;
    [$badge_class, $badge_text] = $is_past
        ? ["ended", "Ended"]
        : ($status_badges[$concert["status"]] ?? $status_badges["sold_out"]);
    ?>
            <article class="tour-card">
                <img src="<?= $root . e($concert["image"]) ?>" alt="<?= e($concert["venue_name"]) ?>">

                <div class="tour-info">
                    <h3><?= e($concert["venue_name"]) ?></h3>
                    <p><?= e($concert["city"]) ?>, <?= e($concert["country"]) ?></p>
                    <p><?= format_date($concert["event_date"]) ?></p>
                </div>

                <div class="tour-status">
                    <span class="<?= $badge_class ?>"><?= $badge_text ?></span>
                    <?php if (!$is_past && $concert["status"] === "available"): ?>
                        <a href="../ticket/ticket.php?concert=<?= (int) $concert["id"] ?>" class="ticket-link">Get Ticket</a>
                    <?php elseif (!$is_past && $concert["status"] === "coming_soon"): ?>
                        <button type="button" disabled>Pre Order</button>
                    <?php endif; ?>
                </div>
            </article>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Page</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/tour.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <main>

        <!-- Page Header -->
        <section class="page-header">
            
                 <h2>
                    Find a show near you and get your ticket
                    before they sell out!

                </h2>
                    <div class="hero-banner">
                
                <img src="../../assets/image/maxresdefault[1]_0(1).jpg" alt="">
            </div>
        </section>

        <!-- Filter -->
        <section class="filter-section" id="tours">
            <nav class="filters" aria-label="Filter by region">
                <?php foreach ($regions as $key => $label): ?>
                    <a href="?region=<?= $key ?>#tours"<?= $key === $region ? ' class="is-active" aria-current="true"' : "" ?>><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
        </section>

        <!-- Tour Listing -->
        <section class="tour-section" aria-label="Upcoming concerts">
            <?php foreach ($upcoming as $concert) concert_card($concert, false); ?>
        </section>
        <?php if (!$upcoming): ?>
            <p class="tour-empty">No upcoming concerts in this region yet.</p>
        <?php endif; ?>

        <!-- Archive -->
        <section class="archive-section" id="archive">
            <h2>Past Tour Archive</h2>

            <?php if ($years): ?>
                <form method="get" action="#archive" class="archive-form">
                    <input type="hidden" name="region" value="<?= e($region) ?>">
                    <label for="archive-year" class="visually-hidden">Year</label>
                    <select id="archive-year" name="year">
                        <?php foreach ($years as $y): ?>
                            <option value="<?= (int) $y ?>"<?= (int) $y === $year ? " selected" : "" ?>><?= (int) $y ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Show</button>
                </form>

                <div class="tour-section archive-list">
                    <?php foreach ($past as $concert) concert_card($concert, true); ?>
                </div>
            <?php else: ?>
                <p class="tour-empty">No past shows yet.</p>
            <?php endif; ?>
        </section>

        <!-- Newsletter -->
        <section class="newsletter" id="newsletter">
           

            <?php show_subscribe_message(); ?>

            <p>
                Be the first to know about new tour dates.
                Join the mailing list.
            </p>

            <form method="post" action="../../actions/subscribe.php">
                <input type="hidden" name="source" value="tour">
                <label for="email">Email</label>

                <div class="newsletter-form">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                    <button type="submit">
                        Subscribe
                    </button>
                </div>
            </form>
        </section>

    </main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>


</body>
</html>