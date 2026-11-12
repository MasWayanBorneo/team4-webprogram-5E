<?php
/* LOGOUT — hanya lewat tombol Keluar (form POST dengan token CSRF), supaya tautan
   dari situs lain tidak bisa membuat admin keluar tanpa sengaja. */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

//..
if (!is_post()) {
    redirect('index.php');
}

$_SESSION = [];
session_destroy();
redirect('login.php?logged_out=1');
