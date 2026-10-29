<?php
$root = "../../";
$active = "about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jonah Marais</title>
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
        <div class="profile-header">
            <div class="profile-pic">
                <img src="../../assets/image/jonah.jpg" class="profile-pic">
            </div>
            <h1 class="profile-name">Jonah Marais</h1>
            <p class="profile-role">Vocals · Keys</p>

            <div class="quick-facts">
                <div class="fact-card">
                    <div class="fact-label">Born</div>
                    <div class="fact-value">June 16, 1998</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Zodiac</div>
                    <div class="fact-value">Gemini</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Hometown</div>
                    <div class="fact-value">Stillwater, Minnesota</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Sports</div>
                    <div class="fact-value">Baseball</div>
                </div>
            </div>

            <div class="bio-section">
                <h2 class="bio-title">Profile</h2>
                <p class="bio-text">Jonah Marais Roth Frantzich is the oldest member of the group. Like Corbyn, he also
                    gained early popularity through the YouNow app and previously released a solo EP titled "When the
                    Daylight's Gone".</p>
                <h2 class="bio-title">Trivia</h2>
                <ul class="bio-list">
                    <li>Huge fan of Harry Potter.</li>
                    <li>His father is a musician and music teacher.</li>
                    <li>Has a habit of drinking coffee every morning.</li>
                </ul>
            </div>
        </div>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>