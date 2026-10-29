<?php
$root = "../../";
$active = "about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corbyn Besson</title>
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
                <img src="../../assets/image/corbyn.jpg" class="profile-pic">
            </div>
            <h1 class="profile-name">Corbyn Besson</h1>
            <p class="profile-role">Vocals · Guitar</p>

            <div class="quick-facts">
                <div class="fact-card">
                    <div class="fact-label">Born</div>
                    <div class="fact-value">November 25, 1998</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Zodiac</div>
                    <div class="fact-value">Sagittarius</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Hometown</div>
                    <div class="fact-value">Fairfax, Virginia</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Instruments</div>
                    <div class="fact-value">Guitar, Piano</div>
                </div>
            </div>

            <div class="bio-section">
                <h2 class="bio-title">Profile</h2>
                <p class="bio-text">Corbyn Matthew Besson grew up in Fairfax, Virginia. Before Why Don't We he built
                    a following by livestreaming and posting song covers online, and in 2016 he joined the band
                    as one of its five vocalists.</p>
                <h2 class="bio-title">Trivia</h2>
                <ul class="bio-list">
                    <li>Built his first fanbase on YouNow and YouTube.</li>
                    <li>Plays guitar and piano.</li>
                    <li>The only member from the East Coast of the US.</li>
                </ul>
            </div>
        </div>
    </div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
</body>

</html>