<?php
/* ============================================================================
   HALAMAN ABOUT — form "Become a Limelight" dikirim (POST) ke halaman ini dan
   disimpan ke tabel `limelight_members`. Daftar member dan pilihan member
   favorit diambil dari tabel `members`.
   ============================================================================ */
$root = "../../";
$active = "about";

require __DIR__ . "/../../config/database.php";
require __DIR__ . "/../../includes/functions.php";

$members = db_fetch_all(db_query($conn, "SELECT id, slug, full_name, roles, photo FROM members ORDER BY sort_order"));
$member_ids = array_map("intval", array_column($members, "id"));

$limelight_error = "";
$old = [
    "name"     => trim($_POST["name"] ?? ""),
    "email"    => trim($_POST["email"] ?? ""),
    "dob"      => trim($_POST["dob"] ?? ""),
    "favorite" => (int) ($_POST["favorite"] ?? 0),
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dob = DateTime::createFromFormat("Y-m-d", $old["dob"]);

    if ($old["name"] === "" || mb_strlen($old["name"]) > 100) {
        $limelight_error = "Please enter your full name (max 100 characters).";
    } elseif (!filter_var($old["email"], FILTER_VALIDATE_EMAIL) || strlen($old["email"]) > 100) {
        $limelight_error = "Please enter a valid email address.";
    } elseif (!$dob || $dob->format("Y-m-d") !== $old["dob"] || $dob > new DateTime()) {
        $limelight_error = "Please enter a valid date of birth.";
    } elseif (!in_array($old["favorite"], $member_ids, true)) {
        $limelight_error = "Please choose your favorite member.";
    } else {
        $exists = mysqli_fetch_assoc(db_query($conn,
            "SELECT id FROM limelight_members WHERE email = ?", "s", [$old["email"]]));

        if ($exists) {
            $limelight_error = "This email is already a Limelight member.";
        } else {
            db_query($conn,
                "INSERT INTO limelight_members (full_name, email, date_of_birth, favorite_member_id)
                 VALUES (?, ?, ?, ?)",
                "sssi", [$old["name"], $old["email"], $old["dob"], $old["favorite"]]);

            // redirect supaya refresh tidak mendaftar dua kali
            redirect("profile.php?joined=1#limelight-form");
        }
    }
}

$joined = isset($_GET["joined"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why Don't We - Members</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/profile.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>

<?php include __DIR__ . "/../../includes/header.php"; ?>

    <div class="app">
        <div class="page-header">
            <p class="section-label">Why Don't We</p>
            <p class="page-title">About</p>
        </div>

        <div class="content about-section">
            <p class="section-label">Who We Are</p>
            <h2 class="section-heading">About the Band</h2>
            <p class="about-text">
                Why Don't We is a five-piece group built on harmony, friendship, and a shared
                love of songwriting. Blending pop hooks with live-band energy, the guys write
                and produce much of their own music, performing it for fans — called
                Limelights all over the world.
            </p>
        </div>

        <div class="content history-section">
            <p class="section-label">Our Journey</p>
            <h2 class="section-heading">History</h2>
            <ol class="history-timeline">
                <li class="timeline-item">
                    <span class="timeline-year">2016</span>
                    <p>Five solo creators came together and formed Why Don't We, releasing
                        their debut single "Taking You" the same year.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">2018</span>
                    <p>Signed with Island Records and released their debut studio album,
                        <em>8 Letters</em>, which landed in the Billboard 200 Top 10.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">2019</span>
                    <p>Released <em>The Good Times and the Bad Ones</em>, growing into a
                        more personal and mature sound.</p>
                </li>
                <li class="timeline-item">
                    <span class="timeline-year">Today</span>
                    <p>Continues to write, record, and tour worldwide — staying close to
                        Limelights through music and live shows.</p>
                </li>
            </ol>
        </div>

        <div class="content gallery-section">
            <p class="section-label">Behind The Scenes</p>
            <h2 class="section-heading">Gallery</h2>
            <div class="gallery-grid">
                <img src="../../assets/image/WDW COVER FINAL (1) (1).jpg" alt="Why Don't We cover art" class="gallery-img">
                <img src="../../assets/image/8letters.jpg" alt="8 Letters album era" class="gallery-img">
                <img src="../../assets/image/HDYLS-new.jpg" alt="How Do You Love? era" class="gallery-img">
                <img src="../../assets/image/Polaroid5_0.png" alt="Band polaroid" class="gallery-img">
                <img src="../../assets/image/Polaroid (1)_1.png" alt="Band polaroid" class="gallery-img">
                <img src="../../assets/image/album2.jpg" alt="Album artwork" class="gallery-img">
                <img src="../../assets/image/why-dont-we-just.png" alt="Why Don't We Just era" class="gallery-img">
                <img src="../../assets/image/only-the-begining.png" alt="Only the Beginning era" class="gallery-img">
            </div>
        </div>

        <div class="content members-section">
            <p class="section-label">The Band</p>
            <h2 class="section-heading">Members</h2>

            <div class="content">
                <?php foreach ($members as $member): ?>
                <a href="member.php?slug=<?= urlencode($member["slug"]) ?>" class="member-card">
                    <div class="avatar">
                        <img src="<?= $root . e($member["photo"]) ?>" alt="<?= e($member["full_name"]) ?>" class="avatar-img">
                    </div>
                    <div class="member-info">
                        <p class="member-name"><?= e($member["full_name"]) ?></p>
                        <p class="member-role"><?= e($member["roles"]) ?></p>
                    </div>
                    <span class="member-arrow">›</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="content limelight-section">
            <p class="section-label">Join The Fam</p>
            <h2 class="section-heading">Limelight Membership</h2>
            <p class="about-text">
                Limelight is the official Why Don't We fan membership built for the
                Limelights who support the guys every step of the way. Join to get closer
                to the band, unlock exclusive perks, and connect with fans around the world.
            </p>

            <div class="limelight-grid">
                <div class="benefit-card">
                    <i class="fa-solid fa-ticket benefit-icon"></i>
                    <p class="benefit-title">Early Ticket Access</p>
                    <p class="benefit-desc">Pre-sale codes and priority booking before tickets go public.</p>
                </div>
                <div class="benefit-card">
                    <i class="fa-solid fa-video benefit-icon"></i>
                    <p class="benefit-title">Exclusive Content</p>
                    <p class="benefit-desc">Behind-the-scenes footage, unreleased clips, and livestreams.</p>
                </div>
                <div class="benefit-card">
                    <i class="fa-solid fa-bag-shopping benefit-icon"></i>
                    <p class="benefit-title">Merch Discounts</p>
                    <p class="benefit-desc">Members-only drops and discount codes on official merch.</p>
                </div>
                <div class="benefit-card">
                    <i class="fa-solid fa-comments benefit-icon"></i>
                    <p class="benefit-title">Direct Connection</p>
                    <p class="benefit-desc">Monthly Q&As and video calls with Daniel, Corbyn, Jack, Jonah, and Zach.</p>
                </div>
                <div class="benefit-card">
                    <i class="fa-solid fa-cake-candles benefit-icon"></i>
                    <p class="benefit-title">Birthday Shoutouts</p>
                    <p class="benefit-desc">A little something special from the band on your big day.</p>
                </div>
                <div class="benefit-card">
                    <i class="fa-solid fa-star benefit-icon"></i>
                    <p class="benefit-title">Meet &amp; Greet Priority</p>
                    <p class="benefit-desc">First in line for meet-and-greets and VIP experiences at shows.</p>
                </div>
            </div>

            <form class="limelight-form" id="limelight-form" method="post" action="profile.php#limelight-form">
                <h3 class="limelight-form-title">Become a Limelight</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ll-name">Full Name</label>
                        <input type="text" id="ll-name" name="name" value="<?= e($old["name"]) ?>" placeholder="Your full name" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="ll-email">Email</label>
                        <input type="email" id="ll-email" name="email" value="<?= e($old["email"]) ?>" placeholder="you@email.com" maxlength="100" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ll-dob">Date of Birth</label>
                        <input type="date" id="ll-dob" name="dob" value="<?= e($old["dob"]) ?>" max="<?= date("Y-m-d") ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ll-favorite">Favorite Member</label>
                        <select id="ll-favorite" name="favorite" required>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= (int) $member["id"] ?>"<?= (int) $member["id"] === $old["favorite"] ? " selected" : "" ?>><?= e($member["full_name"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if ($limelight_error): ?>
                    <p class="form-message is-error" role="alert"><?= e($limelight_error) ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-join">Join Limelight</button>
                <?php if ($joined): ?>
                    <p class="limelight-confirm" role="status">Welcome to Limelight! Check your email to confirm your membership.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php include __DIR__ . "/../../includes/footer.php"; ?>

</body>

</html>