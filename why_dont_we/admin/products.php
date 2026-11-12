<?php
/* CRUD tabel products — produk merch di Home dan halaman Merch */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Produk Merch';
$active = 'products';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$data = ['name' => '', 'collection' => '', 'price' => '', 'image' => '', 'is_featured' => 0, 'is_active' => 1];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM products WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'products.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
//..
if (is_post() && post('do') === 'save') {
    $data = [
        'name'        => post('name'),
        'collection'  => post('collection'),
        'price'       => post('price'),
        'image'       => post('image_current'),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($data['name'] === '' || mb_strlen($data['name']) > 150) {
        $errors[] = 'Nama produk wajib diisi (maksimal 150 karakter).';
    }
    if (mb_strlen($data['collection']) > 100) {
        $errors[] = 'Nama koleksi maksimal 100 karakter.';
    }
    if (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = 'Harga harus berupa angka 0 atau lebih, misalnya 65 atau 49.99.';
    }
    $data['image'] = upload_image('image', $data['image'], $errors);

    if (!$errors) {
        $collection = null_if_empty($data['collection']);
        $price = (float) $data['price'];
        $image = null_if_empty($data['image']);
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE products SET name = ?, collection = ?, price = ?, image = ?, is_featured = ?, is_active = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssdsiii', $data['name'], $collection, $price, $image, $data['is_featured'], $data['is_active'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO products (name, collection, price, image, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssdsii', $data['name'], $collection, $price, $image, $data['is_featured'], $data['is_active']);
        }
        finish_save($stmt, 'products.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM products WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('products.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
//..
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM products ORDER BY is_featured DESC, id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar produk (<?= count($rows) ?>)</h3>
    <a href="products.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Gambar</th><th>Nama</th><th>Koleksi</th><th>Harga</th><th>Unggulan</th><th>Tampil</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= thumb($row['image']) ?></td>
          <td><?= e($row['name']) ?></td>
          <td><?= e($row['collection'] ?? '—') ?></td>
          <td>$<?= number_format($row['price'], 2) ?></td>
          <td><?= $row['is_featured'] ? '<span class="badge text-bg-success">Ya</span>' : '' ?></td>
          <td><?= $row['is_active'] ? '<span class="badge text-bg-success">Ya</span>' : '<span class="badge text-bg-secondary">Disembunyikan</span>' ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('products.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit produk' : 'Tambah produk' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-5"><?php field_text('name', 'Nama produk', $data['name'], 'required maxlength="150"'); ?></div>
        <div class="col-md-4"><?php field_text('collection', 'Koleksi', $data['collection'], 'maxlength="100"', 'Contoh: New Era Collection'); ?></div>
        <div class="col-md-3"><?php field_text('price', 'Harga (USD)', $data['price'], 'type="number" min="0" step="0.01" required'); ?></div>
      </div>
      <?php field_image('image', 'Gambar produk', $data['image']); ?>
      <?php field_checkbox('is_featured', 'Produk unggulan (tampil di kartu besar halaman Merch)', $data['is_featured']); ?>
      <?php field_checkbox('is_active', 'Tampilkan di situs', $data['is_active']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('products.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
