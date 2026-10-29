<?php
$root = "../../";
$active = "about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zach Herron</title>
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
                <img src="../../assets/image/zach.jpg" class="profile-pic">
            </div>
            <h1 class="profile-name">Zach Herron</h1>
            <p class="profile-role">Vocals</p>

            <div class="quick-facts">
                <div class="fact-card">
                    <div class="fact-label">Born</div>
                    <div class="fact-value">May 27, 2001</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Zodiac</div>
                    <div class="fact-value">Gemini</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Hometown</div>
                    <div class="fact-value">Dallas, Texas</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Position</div>
                    <div class="fact-value">Youngest Member (Maknae)</div>
                </div>
            </div>

            <div class="bio-section">
                <h2 class="bio-title">Profile</h2>
                <p class="bio-text">Zachary Dean Herron is the youngest member of Why Don't We. He went viral on social
                    media after a video of him singing Shawn Mendes's "Stitches" at school spread widely.</p>
                <h2 class="bio-title">Trivia</h2>
                <ul class="bio-list">
                    <li>Joined the school choir before becoming famous.</li>
                    <li>Cites Justin Bieber and Shawn Mendes as his inspirations.</li>
                    <li>Released his debut solo single "Timelapse" in 2016.</li>
                </ul>
            </div>
        </div>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>