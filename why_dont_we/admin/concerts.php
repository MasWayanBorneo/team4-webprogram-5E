<?php
/* CRUD tabel concerts — jadwal konser di Home, Tour, dan form tiket */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Konser';
$active = 'concerts';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$regions = ['asia' => 'Asia', 'america' => 'America', 'europe' => 'Europe', 'other' => 'Other'];
$statuses = ['available' => 'Available (tiket dijual)', 'coming_soon' => 'Coming Soon (pre-order)', 'sold_out' => 'Sold Out'];
$data = ['venue_name' => '', 'city' => '', 'country' => '', 'region' => 'asia', 'event_date' => '', 'image' => '', 'status' => 'available'];

//..HAPUS (DELETE) 
// konser yang masih punya kategori tiket tidak bisa dihapus (foreign key RESTRICT)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM concerts WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'concerts.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'venue_name' => post('venue_name'),
        'city'       => post('city'),
        'country'    => post('country'),
        'region'     => post('region'),
        'event_date' => from_datetime_input(post('event_date')),
        'image'      => post('image_current'),
        'status'     => post('status'),
    ];

    if ($data['venue_name'] === '' || mb_strlen($data['venue_name']) > 150) {
        $errors[] = 'Nama tempat wajib diisi (maksimal 150 karakter).';
    }
    if ($data['city'] === '' || mb_strlen($data['city']) > 100) {
        $errors[] = 'Kota wajib diisi.';
    }
    if ($data['country'] === '' || mb_strlen($data['country']) > 100) {
        $errors[] = 'Negara wajib diisi.';
    }
    if (!isset($regions[$data['region']])) {
        $errors[] = 'Wilayah tidak valid.';
    }
    if (!$data['event_date']) {
        $errors[] = 'Tanggal dan jam konser wajib diisi.';
    }
    if (!isset($statuses[$data['status']])) {
        $errors[] = 'Status tidak valid.';
    }
    $data['image'] = upload_image('image', $data['image'], $errors);

    if (!$errors) {
        $image = null_if_empty($data['image']);
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE concerts SET venue_name = ?, city = ?, country = ?, region = ?, event_date = ?, image = ?, status = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'sssssssi', $data['venue_name'], $data['city'], $data['country'], $data['region'], $data['event_date'], $image, $data['status'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO concerts (venue_name, city, country, region, event_date, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssssss', $data['venue_name'], $data['city'], $data['country'], $data['region'], $data['event_date'], $image, $data['status']);
        }
        finish_save($stmt, 'concerts.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

//.. AMBIL DATA UNTUK FORM EDIT (READ satu baris) 
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM concerts WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('concerts.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT k.*, (SELECT COUNT(*) FROM ticket_categories c WHERE c.concert_id = k.id) AS category_count
        FROM concerts k ORDER BY k.event_date'), MYSQLI_ASSOC);
}

$status_badges = ['available' => 'text-bg-success', 'coming_soon' => 'text-bg-warning', 'sold_out' => 'text-bg-danger'];

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar konser (<?= count($rows) ?>)</h3>
    <a href="concerts.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Gambar</th><th>Tempat</th><th>Kota</th><th>Wilayah</th><th>Tanggal</th><th>Status</th><th>Kategori tiket</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= thumb($row['image']) ?></td>
          <td><?= e($row['venue_name']) ?></td>
          <td><?= e($row['city'] . ', ' . $row['country']) ?></td>
          <td><?= e($regions[$row['region']]) ?></td>
          <td class="text-nowrap"><?= date('j M Y H:i', strtotime($row['event_date'])) ?></td>
          <td><span class="badge <?= $status_badges[$row['status']] ?>"><?= e($statuses[$row['status']]) ?></span></td>
          <td><a href="ticket_categories.php"><?= (int) $row['category_count'] ?> kategori</a></td>
          <td class="text-end text-nowrap"><?= action_buttons('concerts.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="8" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit konser' : 'Tambah konser' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_text('venue_name', 'Nama tempat', $data['venue_name'], 'required maxlength="150"'); ?></div>
        <div class="col-md-3"><?php field_text('city', 'Kota', $data['city'], 'required maxlength="100"'); ?></div>
        <div class="col-md-3"><?php field_text('country', 'Negara', $data['country'], 'required maxlength="100"'); ?></div>
        <div class="col-md-4"><?php field_select('region', 'Wilayah (filter halaman Tour)', $regions, $data['region']); ?></div>
        <div class="col-md-4"><?php field_text('event_date', 'Tanggal & jam', to_datetime_input($data['event_date']), 'type="datetime-local" required'); ?></div>
        <div class="col-md-4"><?php field_select('status', 'Status', $statuses, $data['status'], null, 'Coming Soon = tombol Pre Order, pesanan disimpan pending.'); ?></div>
      </div>
      <?php field_image('image', 'Gambar konser', $data['image']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('concerts.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
