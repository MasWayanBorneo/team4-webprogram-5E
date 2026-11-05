<?php
/* Beranda dashboard: jumlah data di setiap tabel, dengan tautan ke halaman CRUD-nya */
require __DIR__ . '/includes/init.php';

$page_title = 'Dashboard';
$active = 'dashboard';

// warna kartu per kelompok menu
$colors = ['Data Masuk' => 'text-bg-primary', 'Konten' => 'text-bg-success', 'Musik & Personel' => 'text-bg-warning', 'Pengaturan' => 'text-bg-secondary'];

require __DIR__ . '/includes/header.php';
?>
<?php foreach ($admin_menu as $group => $items): ?>
<h2 class="fs-5 mt-2 mb-3"><?= e($group) ?></h2>
<div class="row">
  <?php foreach ($items as [$file, $title, $icon, $table]): ?>
  <?php $count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM $table"))[0]; ?>
  <div class="col-lg-3 col-md-4 col-6">
    <div class="small-box <?= $colors[$group] ?>">
      <div class="inner">
        <h3><?= (int) $count ?></h3>
        <p><?= e($title) ?></p>
      </div>
      <i class="small-box-icon bi <?= $icon ?>"></i>
      <a href="<?= $file ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
        Kelola <i class="bi bi-arrow-right-circle"></i>
      </a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
