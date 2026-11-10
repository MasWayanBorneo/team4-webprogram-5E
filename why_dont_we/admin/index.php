<?php
/* Beranda dashboard: jumlah data di setiap tabel, dengan tautan ke halaman CRUD-nya */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Dashboard';
$active = 'dashboard';

// warna kartu per kelompok menu
$colors = ['Data Masuk' => 'text-bg-primary', 'Konten' => 'text-bg-success', 'Musik & Personel' => 'text-bg-warning', 'Pengaturan' => 'text-bg-secondary'];

// hanya menu yang berupa tabel database yang dihitung jumlah datanya (Check-in bukan tabel)
$tables = array_column(mysqli_fetch_all(mysqli_query($conn, 'SHOW TABLES')), 0);

// ringkasan hari konser: tiket lunas, yang sudah masuk, dan pre-order yang menunggu konfirmasi
$ticket_summary = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(status = 'paid'), 0) AS paid_orders,
           COALESCE(SUM(status = 'paid' AND checked_in_at IS NOT NULL), 0) AS checked_in,
           COALESCE(SUM(status = 'pending'), 0) AS pending
    FROM ticket_orders"));

require __DIR__ . '/includes/header.php';
?>
<div class="alert alert-info d-flex flex-wrap align-items-center gap-3">
  <i class="bi bi-qr-code-scan fs-3"></i>
  <div class="me-auto">
    <strong>Check-in:</strong> <?= (int) $ticket_summary['checked_in'] ?> dari <?= (int) $ticket_summary['paid_orders'] ?> pesanan lunas sudah masuk.
    <?php if ($ticket_summary['pending']): ?>· <strong><?= (int) $ticket_summary['pending'] ?> pre-order</strong> menunggu konfirmasi.<?php endif; ?>
  </div>
  <a href="checkin.php" class="btn btn-primary btn-sm">Buka Check-in</a>
  <a href="ticket_orders.php?status=pending" class="btn btn-outline-primary btn-sm">Lihat pre-order</a>
</div>
<?php foreach ($admin_menu as $group => $items): ?>
<h2 class="fs-5 mt-2 mb-3"><?= e($group) ?></h2>
<div class="row">
  <?php foreach ($items as [$file, $title, $icon, $table]): ?>
  <?php if (!in_array($table, $tables, true)) continue; ?>
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
