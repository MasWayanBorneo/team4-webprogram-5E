<?php
/* ============================================================================
   HALAMAN PROFIL MEMBER — member.php?slug=jack
   Satu file untuk kelima member. Data dari tabel `members` dan `member_trivia`.
   ============================================================================ */
$root = "../../";
$active = "about";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$slug = trim($_GET["slug"] ?? "");

$member = mysqli_fetch_assoc(db_query($conn,
    "SELECT id, full_name, roles, birth_date, zodiac, hometown, extra_label, extra_value, bio, photo
     FROM members
     WHERE slug = ?",
    "s", [$slug]));

if ($member) {
    $trivia = db_fetch_all(db_query($conn,
        "SELECT content FROM member_trivia WHERE member_id = ? ORDER BY sort_order",
        "i", [$member["id"]]));

    // kartu fakta singkat; kartu ke-4 berbeda per member (Instruments, Skills, ...)
    $facts = [
        "Born"     => $member["birth_date"] ? date("F j, Y", strtotime($member["birth_date"])) : null,
        "Zodiac"   => $member["zodiac"],
        "Hometown" => $member["hometown"],
    ];
    if ($member["extra_label"]) {
        $facts[$member["extra_label"]] = $member["extra_value"];
    }
    $facts = array_filter($facts);
} else {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $member ? e($member["full_name"]) : "Member not found" ?></title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <div class="app">
        <div class="back-nav">
            <a href="profile.php" class="back-link">‹ Members</a>
        </div>

        <?php if (!$member): ?>
        <div class="profile-header">
            <h1 class="profile-name">member not found</h1>
            <p class="profile-role">We couldn't find that member. Go back to the <a href="profile.php">members list</a>.</p>
        </div>
        <?php else: ?>
        <div class="profile-header">
            <div class="profile-pic">
                <img src="<?= $root . e($member["photo"]) ?>" alt="<?= e($member["full_name"]) ?>" class="profile-pic">
            </div>
            <h1 class="profile-name"><?= e($member["full_name"]) ?></h1>
            <p class="profile-role"><?= e($member["roles"]) ?></p>

            <div class="quick-facts">
                <?php foreach ($facts as $label => $value): ?>
                <div class="fact-card">
                    <div class="fact-label"><?= e($label) ?></div>
                    <div class="fact-value"><?= e($value) ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bio-section">
                <?php if ($member["bio"]): ?>
                    <h2 class="bio-title">Profile</h2>
                    <p class="bio-text"><?= e($member["bio"]) ?></p>
                <?php endif; ?>

                <?php if ($trivia): ?>
                    <h2 class="bio-title">Trivia</h2>
                    <ul class="bio-list">
                        <?php foreach ($trivia as $item): ?>
                            <li><?= e($item["content"]) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>
