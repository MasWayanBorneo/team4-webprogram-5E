<?php
$root = "../../";
$active = "about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jack Avery</title>
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
                <img src="../../assets/image/jack.jpg" class="profile-pic">
            </div>
            <h1 class="profile-name">Jack Avery</h1>
            <p class="profile-role">Vocals · Guitar</p>

            <div class="quick-facts">
                <div class="fact-card">
                    <div class="fact-label">Born</div>
                    <div class="fact-value">July 1, 1999</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Zodiac</div>
                    <div class="fact-value">Cancer</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Hometown</div>
                    <div class="fact-value">Burbank, California</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Skills</div>
                    <div class="fact-value">Skateboarding</div>
                </div>
            </div>

            <div class="bio-section">
                <h2 class="bio-title">Profile</h2>
                <p class="bio-text">Jack Robert Avery started his career by uploading song covers on YouTube and
                    participating in the IMPACT music tour before joining Why Don't We.</p>
                <h2 class="bio-title">Trivia</h2>
                <ul class="bio-list">
                    <li>Has a daughter named Lavender May Avery.</li>
                    <li>Initially more interested in basketball before focusing on music.</li>
                    <li>Can play the piano and guitar.</li>
                </ul>
            </div>
        </div>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>