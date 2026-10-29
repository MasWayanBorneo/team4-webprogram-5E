<?php
$root = "../../";
$active = "about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daniel Seavey</title>
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
                <img src="../../assets/image/daniel.jpg" class="profile-pic">
            </div>
            <h1 class="profile-name">Daniel Seavey</h1>
            <p class="profile-role">Vocals · Guitar · Producer</p>

            <div class="quick-facts">
                <div class="fact-card">
                    <div class="fact-label">Born</div>
                    <div class="fact-value">April 2, 1999</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Zodiac</div>
                    <div class="fact-value">Aries</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Hometown</div>
                    <div class="fact-value">Portland, Oregon</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Instruments</div>
                    <div class="fact-value">Guitar, Cello, Piano</div>
                </div>
            </div>

            <div class="bio-section">
                <h2 class="bio-title">Profile</h2>
                <p class="bio-text">Daniel James Seavey is the main singer, songwriter, and producer of the group. He
                    first gained public attention as a contestant on American Idol season 14 at the age of 15.</p>
                <h2 class="bio-title">Trivia</h2>
                <ul class="bio-list">
                    <li>Can play more than 20 musical instruments.</li>
                    <li>Huge fan of The Beatles and Ed Sheeran.</li>
                    <li>Has a pet dog named Kobe.</li>
                </ul>
            </div>
        </div>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>