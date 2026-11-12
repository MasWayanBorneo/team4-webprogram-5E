<?php
/* CRUD tabel videos — "All videos" di halaman Music */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Video';
$active = 'videos';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$data = ['title' => '', 'youtube_id' => '', 'start_second' => '0'];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM videos WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'videos.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'title'        => post('title'),
        'youtube_id'   => post('youtube_id'),
        'start_second' => post('start_second') === '' ? '0' : post('start_second'),
    ];

    if ($data['title'] === '' || mb_strlen($data['title']) > 150) {
        $errors[] = 'Judul video wajib diisi (maksimal 150 karakter).';
    }
    // kode video YouTube selalu 11 karakter (huruf, angka, - dan _)
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $data['youtube_id'])) {
        $errors[] = 'Kode video YouTube harus 11 karakter, misalnya C3DlM19x4RQ (dari youtube.com/watch?v=C3DlM19x4RQ).';
    }
    if (!is_int_between($data['start_second'], 0, 65535)) {
        $errors[] = 'Detik mulai harus berupa angka 0 atau lebih.';
    }

    if (!$errors) {
        $start = (int) $data['start_second'];
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE videos SET title = ?, youtube_id = ?, start_second = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssii', $data['title'], $data['youtube_id'], $start, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO videos (title, youtube_id, start_second) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssi', $data['title'], $data['youtube_id'], $start);
        }
        finish_save($stmt, 'videos.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM videos WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('videos.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
// ..
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM videos ORDER BY id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar video (<?= count($rows) ?>)</h3>
    <a href="videos.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>#</th><th>Gambar</th><th>Judul</th><th>Kode YouTube</th><th>Mulai detik</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= thumb('https://img.youtube.com/vi/' . $row['youtube_id'] . '/mqdefault.jpg') ?></td>
          <td><?= e($row['title']) ?></td>
          <td><a href="https://www.youtube.com/watch?v=<?= e($row['youtube_id']) ?>" target="_blank"><code><?= e($row['youtube_id']) ?></code></a></td>
          <td><?= (int) $row['start_second'] ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('videos.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit video' : 'Tambah video' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php field_text('title', 'Judul video', $data['title'], 'required maxlength="150"'); ?>
      <?php field_text('youtube_id', 'Kode video YouTube', $data['youtube_id'], 'required maxlength="11" pattern="[A-Za-z0-9_\-]{11}"', 'Bagian setelah watch?v= di alamat video, 11 karakter.'); ?>
      <?php field_text('start_second', 'Mulai diputar dari detik ke-', $data['start_second'], 'type="number" min="0" max="65535"'); ?>
    </div>
    <div class="card-footer"><?php form_buttons('videos.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
