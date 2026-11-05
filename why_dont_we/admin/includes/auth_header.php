<?php
/* Kerangka halaman login & pembuatan akun pertama (gaya "login page" AdminLTE 4),
   tanpa menu samping. Sebelum include, halaman mengisi $page_title. */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($page_title) ?> | Admin Why Don't We</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/adminlte.min.css">
</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
  <div class="login-logo">
    <img src="../assets/image/about-logo.png" alt="" style="height:56px" class="me-2"><b>WDW</b> Admin
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <?php if ($flash): ?>
      <div class="alert alert-<?= e($flash[0]) ?>"><?= e($flash[1]) ?></div>
      <?php endif; ?>
