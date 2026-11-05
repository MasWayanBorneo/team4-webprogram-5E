<?php
/* CRUD tabel ticket_categories — kategori tiket (Regular, VIP) setiap konser, dengan harga & kuota */
require __DIR__ . '/includes/init.php';

$page_title = 'Kategori Tiket';
$active = 'ticket_categories';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$concerts = options_from($conn, "SELECT id, CONCAT(venue_name, ' — ', DATE_FORMAT(event_date, '%e %b %Y')) FROM concerts ORDER BY event_date");
$data = ['concert_id' => '', 'name' => '', 'description' => '', 'price' => '', 'quota' => ''];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
// kategori yang sudah punya pesanan tidak bisa dihapus (foreign key RESTRICT)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM ticket_categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'ticket_categories.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'concert_id'  => post('concert_id'),
        'name'        => post('name'),
        'description' => post('description'),
        'price'       => post('price'),
        'quota'       => post('quota'),
    ];

    if (!isset($concerts[$data['concert_id']])) {
        $errors[] = 'Pilih konser.';
    }
    if ($data['name'] === '' || mb_strlen($data['name']) > 50) {
        $errors[] = 'Nama kategori wajib diisi, misalnya Regular atau VIP.';
    }
    if (mb_strlen($data['description']) > 255) {
        $errors[] = 'Keterangan maksimal 255 karakter.';
    }
    if (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = 'Harga harus berupa angka 0 atau lebih, misalnya 50 atau 49.99.';
    }
    if (!is_int_between($data['quota'], 0, 1000000)) {
        $errors[] = 'Kuota harus berupa angka bulat 0 atau lebih.';
    }

    if (!$errors) {
        $concert_id = (int) $data['concert_id'];
        $description = null_if_empty($data['description']);
        $price = (float) $data['price'];
        $quota = (int) $data['quota'];
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE ticket_categories SET concert_id = ?, name = ?, description = ?, price = ?, quota = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'issdii', $concert_id, $data['name'], $description, $price, $quota, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO ticket_categories (concert_id, name, description, price, quota) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'issdi', $concert_id, $data['name'], $description, $price, $quota);
        }
        finish_save($stmt, 'ticket_categories.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM ticket_categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('ticket_categories.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
// terjual = jumlah tiket dari pesanan yang tidak dibatalkan (pending + paid)
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, "
        SELECT c.*, k.venue_name, k.event_date, COALESCE(SUM(o.quantity), 0) AS sold
        FROM ticket_categories c
        JOIN concerts k ON k.id = c.concert_id
        LEFT JOIN ticket_orders o ON o.ticket_category_id = c.id AND o.status <> 'cancelled'
        GROUP BY c.id
        ORDER BY k.event_date, c.price"), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar kategori tiket (<?= count($rows) ?>)</h3>
    <a href="ticket_categories.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Konser</th><th>Kategori</th><th>Harga</th><th>Kuota</th><th>Terjual</th><th>Sisa</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= e($row['venue_name']) ?><br><small class="text-secondary"><?= date('j M Y', strtotime($row['event_date'])) ?></small></td>
          <td><?= e($row['name']) ?></td>
          <td>$<?= number_format($row['price'], 2) ?></td>
          <td><?= (int) $row['quota'] ?></td>
          <td><?= (int) $row['sold'] ?></td>
          <td><?= max(0, $row['quota'] - $row['sold']) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('ticket_categories.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit kategori tiket' : 'Tambah kategori tiket' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_select('concert_id', 'Konser', $concerts, $data['concert_id'], null); ?></div>
        <div class="col-md-6"><?php field_text('name', 'Nama kategori', $data['name'], 'required maxlength="50"', 'Contoh: Regular, VIP'); ?></div>
        <div class="col-md-6"><?php field_text('price', 'Harga (USD)', $data['price'], 'type="number" min="0" step="0.01" required'); ?></div>
        <div class="col-md-6"><?php field_text('quota', 'Kuota tiket', $data['quota'], 'type="number" min="0" required'); ?></div>
      </div>
      <?php field_textarea('description', 'Fasilitas / keterangan', $data['description'], 2, 'maxlength="255"'); ?>
    </div>
    <div class="card-footer"><?php form_buttons('ticket_categories.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
