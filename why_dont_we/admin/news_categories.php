<?php
/* CRUD tabel news_categories — kategori berita (Announcement, Tour, ...) */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Kategori Berita';
$active = 'news_categories';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$data = ['name' => ''];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
// kategori yang masih dipakai berita tidak bisa dihapus (foreign key RESTRICT)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM news_categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'news_categories.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = ['name' => post('name')];

    if ($data['name'] === '' || mb_strlen($data['name']) > 50) {
        $errors[] = 'Nama kategori wajib diisi (maksimal 50 karakter).';
    }

    if (!$errors) {
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE news_categories SET name = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'si', $data['name'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO news_categories (name) VALUES (?)');
            mysqli_stmt_bind_param($stmt, 's', $data['name']);
        }
        finish_save($stmt, 'news_categories.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM news_categories WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('news_categories.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT c.*, COUNT(n.id) AS news_count
        FROM news_categories c LEFT JOIN news n ON n.category_id = c.id
        GROUP BY c.id ORDER BY c.name'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar kategori (<?= count($rows) ?>)</h3>
    <a href="news_categories.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>#</th><th>Nama kategori</th><th>Jumlah berita</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= e($row['name']) ?></td>
          <td><?= (int) $row['news_count'] ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('news_categories.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit kategori' : 'Tambah kategori' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php field_text('name', 'Nama kategori', $data['name'], 'required maxlength="50"', 'Nama kategori tidak boleh sama dengan kategori lain.'); ?>
    </div>
    <div class="card-footer"><?php form_buttons('news_categories.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
