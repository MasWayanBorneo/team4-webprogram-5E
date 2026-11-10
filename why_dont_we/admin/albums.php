<?php
/* CRUD tabel albums — kartu "All albums" di halaman Music */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Album';
$active = 'albums';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$types = ['album' => 'Album', 'ep' => 'EP', 'single' => 'Single'];
$data = ['title' => '', 'type' => 'album', 'release_year' => '', 'cover_image' => ''];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM albums WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'albums.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'title'        => post('title'),
        'type'         => post('type'),
        'release_year' => post('release_year'),
        'cover_image'  => post('cover_image_current'),
    ];

    if ($data['title'] === '' || mb_strlen($data['title']) > 150) {
        $errors[] = 'Judul wajib diisi (maksimal 150 karakter).';
    }
    if (!isset($types[$data['type']])) {
        $errors[] = 'Jenis album tidak valid.';
    }
    if ($data['release_year'] !== '' && !preg_match('/^(19|20)\d{2}$/', $data['release_year'])) {
        $errors[] = 'Tahun rilis harus 4 angka, misalnya 2018.';
    }
    $data['cover_image'] = upload_image('cover_image', $data['cover_image'], $errors);

    if (!$errors) {
        $year = $data['release_year'] === '' ? null : (int) $data['release_year'];
        $cover = $data['cover_image'] === '' ? null : $data['cover_image'];

        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE albums SET title = ?, type = ?, release_year = ?, cover_image = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssisi', $data['title'], $data['type'], $year, $cover, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO albums (title, type, release_year, cover_image) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssis', $data['title'], $data['type'], $year, $cover);
        }
        finish_save($stmt, 'albums.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM albums WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('albums.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM albums ORDER BY id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar album (<?= count($rows) ?>)</h3>
    <a href="albums.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>#</th><th>Sampul</th><th>Judul</th><th>Jenis</th><th>Tahun</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= thumb($row['cover_image']) ?></td>
          <td><?= e($row['title']) ?></td>
          <td><?= e($types[$row['type']]) ?></td>
          <td><?= e($row['release_year']) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('albums.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit album' : 'Tambah album' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php field_text('title', 'Judul', $data['title'], 'required maxlength="150"'); ?>
      <?php field_select('type', 'Jenis', $types, $data['type']); ?>
      <?php field_text('release_year', 'Tahun rilis', $data['release_year'], 'inputmode="numeric" maxlength="4" pattern="(19|20)[0-9]{2}"', 'Boleh dikosongkan.'); ?>
      <?php field_image('cover_image', 'Gambar sampul', $data['cover_image']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('albums.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
