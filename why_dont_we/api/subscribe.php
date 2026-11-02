<?php
/* ============================================================================
   SUBSCRIBE — menyimpan email dari form newsletter ke tabel subscribers,
   lalu kembali ke halaman asal dengan ?subscribe=ok|exists|invalid|error.
   Dipakai oleh form di Home, News, dan Tour (field tersembunyi "source").
   ============================================================================ */
require __DIR__ . '/../includes/db.php';

// halaman asal tiap source → [file, id bagian form]
$pages = [
    'home' => ['../index.php', 'newsletter'],
    'news' => ['../src/article/news.php', 'subscribe'],
    'tour' => ['../src/tour/tour.php', 'newsletter'],
];

$source = $_POST['source'] ?? '';
if (!isset($pages[$source])) {
    $source = 'home';
}
$email = trim($_POST['email'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $status = 'invalid';
} else {
    $stmt = mysqli_prepare($conn, 'INSERT INTO subscribers (email, source) VALUES (?, ?)');
    mysqli_stmt_bind_param($stmt, 'ss', $email, $source);

    if (mysqli_stmt_execute($stmt)) {
        $status = 'ok';
    } elseif (mysqli_stmt_errno($stmt) === 1062) {
        $status = 'exists'; // email sudah terdaftar (UNIQUE)
    } else {
        $status = 'error';
    }
}

[$page, $anchor] = $pages[$source];
header("Location: $page?subscribe=$status#$anchor");
exit;
