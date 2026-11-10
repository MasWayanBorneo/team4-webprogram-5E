<?php
/* CHECK-IN TIKET di pintu masuk konser.
   Panitia memindai QR tiket (isinya alamat invoice) atau mengetik nomor invoice.
   Tiket lunas yang belum dipakai bisa di-check-in sekali; waktu masuk dicatat di
   ticket_orders.checked_in_at, sehingga tiket yang sama (mis. hasil cetak ulang PDF)
   ditolak kalau dipindai lagi. */

   
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Check-in Tiket';
$active = 'checkin';

// QR berisi alamat lengkap (…/invoice.php?no=WDW-XXXXXXXX), jadi cukup ambil kode WDW-nya
$input = trim($_GET['code'] ?? '');
$invoice_no = preg_match('/WDW-[0-9A-F]{8}/i', $input, $m) ? strtoupper($m[0]) : '';

/* --- CHECK-IN / BATALKAN CHECK-IN -------------------------------------------- */
if (is_post()) {
    $order_id = (int) $_POST['id'];
    $back = 'checkin.php?code=' . urlencode(post('invoice_no'));

    if (post('do') === 'checkin') {
        // syarat ditulis di WHERE: hanya tiket lunas yang belum pernah check-in.
        // Kalau dua panitia memindai tiket yang sama bersamaan, hanya satu yang berhasil.
        $stmt = mysqli_prepare($conn, "UPDATE ticket_orders SET checked_in_at = NOW() WHERE id = ? AND status = 'paid' AND checked_in_at IS NULL");
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 1) {
            flash('success', 'Check-in berhasil. Berikan gelang sesuai jumlah tiket, lalu sobek bagian Committee Copy.');
        } else {
            flash('danger', 'Check-in ditolak: tiket sudah dipakai atau belum lunas.');
        }
        redirect($back);
    }

    if (post('do') === 'undo') {
        // untuk membetulkan salah check-in
        $stmt = mysqli_prepare($conn, 'UPDATE ticket_orders SET checked_in_at = NULL WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        flash('warning', 'Check-in dibatalkan. Tiket ini bisa dipakai masuk lagi.');
        redirect($back);
    }
}

/* --- CARI TIKET ---------------------------------------------------------------- */
$order = null;
if ($invoice_no !== '') {
    $stmt = mysqli_prepare($conn, '
        SELECT o.*, c.name AS category, k.venue_name, k.city, k.event_date
        FROM ticket_orders o
        JOIN ticket_categories c ON c.id = o.ticket_category_id
        JOIN concerts k ON k.id = c.concert_id
        WHERE o.invoice_no = ?');
    mysqli_stmt_bind_param($stmt, 's', $invoice_no);
    mysqli_stmt_execute($stmt);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// check-in terakhir, untuk dipantau panitia
$recent = mysqli_fetch_all(mysqli_query($conn, '
    SELECT o.invoice_no, o.full_name, o.quantity, o.checked_in_at, k.venue_name
    FROM ticket_orders o
    JOIN ticket_categories c ON c.id = o.ticket_category_id
    JOIN concerts k ON k.id = c.concert_id
    WHERE o.checked_in_at IS NOT NULL
    ORDER BY o.checked_in_at DESC
    LIMIT 10'), MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="checkin.php" class="row g-2 align-items-center">
      <div class="col-md">
        <label class="visually-hidden" for="code">Kode tiket</label>
        <input class="form-control form-control-lg" id="code" name="code" value="<?= e($input) ?>"
               placeholder="Pindai QR tiket atau ketik nomor invoice, mis. WDW-1A2B3C4D" autocomplete="off" autofocus required>
      </div>
      <div class="col-md-auto">
        <button class="btn btn-primary btn-lg w-100"><i class="bi bi-search"></i> Cek tiket</button>
      </div>
    </form>
    <div class="form-text mt-2">Scanner QR (USB/Bluetooth) bisa langsung dipakai: arahkan ke QR di tiket, kodenya akan terisi otomatis.</div>
  </div>
</div>

<?php if ($input !== '' && !$order): ?>
<div class="alert alert-danger fs-5"><i class="bi bi-x-octagon"></i> <strong>Tiket tidak ditemukan.</strong> Periksa lagi nomor invoice-nya — kemungkinan tiket palsu.</div>
<?php elseif ($order): ?>
  <?php
  // status tiket untuk panitia: [warna kartu, ikon, judul, keterangan]
  if ($order['status'] === 'cancelled') {
      $view = ['danger', 'bi-x-octagon', 'PESANAN DIBATALKAN', 'Jangan izinkan masuk.'];
  } elseif ($order['status'] === 'pending') {
      $view = ['warning', 'bi-hourglass-split', 'PRE-ORDER BELUM DIKONFIRMASI', 'Pesanan belum lunas, jadi belum bisa dipakai masuk.'];
  } elseif ($order['checked_in_at']) {
      $view = ['danger', 'bi-exclamation-octagon', 'SUDAH DIPAKAI', 'Tiket ini sudah check-in pada ' . date('j M Y, H:i', strtotime($order['checked_in_at'])) . '. Jangan izinkan masuk lagi.'];
  } else {
      $view = ['success', 'bi-check-circle', 'TIKET VALID', 'Boleh masuk. Berikan ' . (int) $order['quantity'] . ' gelang.'];
  }
  ?>
<div class="card border-<?= $view[0] ?> border-3 mb-4">
  <div class="card-header text-bg-<?= $view[0] ?>">
    <h3 class="card-title mb-0 fs-4"><i class="bi <?= $view[1] ?>"></i> <?= $view[2] ?></h3>
  </div>
  <div class="card-body">
    <p class="fs-5"><?= e($view[3]) ?></p>
    <div class="row">
      <div class="col-md-6">
        <dl class="row mb-0">
          <dt class="col-5">Nomor invoice</dt><dd class="col-7"><code><?= e($order['invoice_no']) ?></code></dd>
          <dt class="col-5">Nama</dt><dd class="col-7"><?= e($order['full_name']) ?></dd>
          <dt class="col-5">Email / HP</dt><dd class="col-7"><?= e($order['email']) ?><br><?= e($order['phone']) ?></dd>
        </dl>
      </div>
      <div class="col-md-6">
        <dl class="row mb-0">
          <dt class="col-5">Konser</dt><dd class="col-7"><?= e($order['venue_name']) ?>, <?= e($order['city']) ?><br><?= date('j M Y, H:i', strtotime($order['event_date'])) ?></dd>
          <dt class="col-5">Kategori</dt><dd class="col-7"><?= e($order['category']) ?></dd>
          <dt class="col-5">Jumlah gelang</dt><dd class="col-7 fs-4 fw-bold"><?= (int) $order['quantity'] ?></dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <?php if ($order['status'] === 'paid' && !$order['checked_in_at']): ?>
    <form method="post" class="d-inline">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
      <input type="hidden" name="invoice_no" value="<?= e($order['invoice_no']) ?>">
      <button class="btn btn-success btn-lg" name="do" value="checkin"><i class="bi bi-box-arrow-in-right"></i> Check-in (<?= (int) $order['quantity'] ?> gelang)</button>
    </form>
    <?php elseif ($order['checked_in_at']): ?>
    <form method="post" class="d-inline" onsubmit="return confirm('Batalkan check-in tiket ini? Tiket akan bisa dipakai masuk lagi.')">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
      <input type="hidden" name="invoice_no" value="<?= e($order['invoice_no']) ?>">
      <button class="btn btn-outline-secondary" name="do" value="undo"><i class="bi bi-arrow-counterclockwise"></i> Batalkan check-in (kalau salah pindai)</button>
    </form>
    <?php elseif ($order['status'] === 'pending'): ?>
    <a href="ticket_orders.php?action=edit&id=<?= (int) $order['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Konfirmasi pembayaran di halaman pesanan</a>
    <?php endif; ?>
    <a href="checkin.php" class="btn btn-link">Pindai tiket berikutnya</a>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><h3 class="card-title mb-0">10 check-in terakhir</h3></div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>Waktu masuk</th><th>Invoice</th><th>Nama</th><th>Konser</th><th>Gelang</th></tr></thead>
      <tbody>
        <?php foreach ($recent as $row): ?>
        <tr>
          <td class="text-nowrap"><?= date('j M H:i', strtotime($row['checked_in_at'])) ?></td>
          <td><a href="checkin.php?code=<?= urlencode($row['invoice_no']) ?>"><code><?= e($row['invoice_no']) ?></code></a></td>
          <td><?= e($row['full_name']) ?></td>
          <td><?= e($row['venue_name']) ?></td>
          <td><?= (int) $row['quantity'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="5" class="text-center text-secondary py-4">Belum ada tiket yang check-in.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
