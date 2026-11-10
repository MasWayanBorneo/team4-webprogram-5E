<?php
/* Bagian atas tampilan AdminLTE 4: <head>, navbar, menu samping, judul halaman.
   Sebelum include, halaman mengisi $page_title (judul) dan $active (nama tabel menu yang aktif). */
/** @var string $page_title */
/** @var string $active */
/** @var array $admin_menu */
/** @var array $current_admin */
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
  <!-- AdminLTE 4 (sudah termasuk Bootstrap 5) -->
  <link rel="stylesheet" href="assets/adminlte.min.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <!-- ============================ NAVBAR ============================ -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Buka/tutup menu"><i class="bi bi-list"></i></a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="../index.php" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i> Lihat situs</a>
        </li>
        <li class="nav-item">
          <span class="nav-link"><i class="bi bi-person-circle me-1"></i> <?= e($current_admin['username']) ?></span>
        </li>
        <li class="nav-item">
          <form method="post" action="logout.php" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-secondary ms-2"><i class="bi bi-box-arrow-right"></i> Keluar</button>
          </form>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============================ MENU SAMPING ============================ -->
  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="index.php" class="brand-link">
        <img src="../assets/image/about-logo.png" alt="" class="brand-image opacity-75">
        <span class="brand-text fw-light">WDW Admin</span>
      </a>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2" aria-label="Menu admin">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false">
          <li class="nav-item">
            <a href="index.php" class="nav-link<?= $active === 'dashboard' ? ' active' : '' ?>">
              <i class="nav-icon bi bi-speedometer"></i><p>Dashboard</p>
            </a>
          </li>
          <?php foreach ($admin_menu as $group => $items): ?>
          <li class="nav-header"><?= e(strtoupper($group)) ?></li>
            <?php foreach ($items as [$file, $title, $icon, $table]): ?>
            <li class="nav-item">
              <a href="<?= $file ?>" class="nav-link<?= $active === $table ? ' active' : '' ?>">
                <i class="nav-icon bi <?= $icon ?>"></i><p><?= e($title) ?></p>
              </a>
            </li>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- ============================ ISI HALAMAN ============================ -->
  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <h1 class="mb-0 fs-3"><?= e($page_title) ?></h1>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
        <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash[0]) ?> alert-dismissible fade show" role="alert">
          <?= e($flash[1]) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
        <?php endif; ?>
