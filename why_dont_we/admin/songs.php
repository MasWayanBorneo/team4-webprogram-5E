<?php
/* CRUD tabel songs — "Top popular songs" di halaman Music (audio, lirik, link streaming) */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Lagu';
$active = 'songs';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$albums = options_from($conn, 'SELECT id, title FROM albums ORDER BY title');
$data = [
    'album_id' => '', 'title' => '', 'duration_sec' => '', 'audio_file' => '', 'cover_image' => '',
    'lyrics' => '', 'spotify_url' => '', 'apple_url' => '', 'youtube_url' => '', 'is_featured' => 1,
];

//.. HAPUS (DELETE)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM songs WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'songs.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */


if (is_post() && post('do') === 'save') {
    $data = [
        'album_id'     => post('album_id'),
        'title'        => post('title'),
        'duration_sec' => post('duration_sec'),
        'audio_file'   => post('audio_file_current'),
        'cover_image'  => post('cover_image_current'),
        'lyrics'       => post('lyrics'),
        'spotify_url'  => post('spotify_url'),
        'apple_url'    => post('apple_url'),
        'youtube_url'  => post('youtube_url'),
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
    ];

    if ($data['album_id'] !== '' && !isset($albums[$data['album_id']])) {
        $errors[] = 'Album tidak ditemukan.';
    }
    if ($data['title'] === '' || mb_strlen($data['title']) > 150) {
        $errors[] = 'Judul lagu wajib diisi (maksimal 150 karakter).';
    }
    if ($data['duration_sec'] !== '' && !is_int_between($data['duration_sec'], 1, 65535)) {
        $errors[] = 'Durasi harus berupa angka detik, misalnya 185.';
    }
    foreach (['spotify_url' => 'Spotify', 'apple_url' => 'Apple Music', 'youtube_url' => 'YouTube Music'] as $key => $name) {
        if ($data[$key] !== '' && (!filter_var($data[$key], FILTER_VALIDATE_URL) || strlen($data[$key]) > 255)) {
            $errors[] = "Link $name harus berupa alamat lengkap, misalnya https://...";
        }
    }
    $data['cover_image'] = upload_image('cover_image', $data['cover_image'], $errors);
    $data['audio_file'] = upload_audio('audio_file', $data['audio_file'], $errors);

    if (!$errors) {
        $album_id = $data['album_id'] === '' ? null : (int) $data['album_id'];
        $duration = $data['duration_sec'] === '' ? null : (int) $data['duration_sec'];
        $audio = null_if_empty($data['audio_file']);
        $cover = null_if_empty($data['cover_image']);
        $lyrics = null_if_empty($data['lyrics']);
        $spotify = null_if_empty($data['spotify_url']);
        $apple = null_if_empty($data['apple_url']);
        $youtube = null_if_empty($data['youtube_url']);

        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE songs SET album_id = ?, title = ?, duration_sec = ?, audio_file = ?, cover_image = ?, lyrics = ?,
                spotify_url = ?, apple_url = ?, youtube_url = ?, is_featured = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'isissssssii', $album_id, $data['title'], $duration, $audio, $cover, $lyrics,
                $spotify, $apple, $youtube, $data['is_featured'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO songs (album_id, title, duration_sec, audio_file, cover_image, lyrics,
                spotify_url, apple_url, youtube_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'isissssssi', $album_id, $data['title'], $duration, $audio, $cover, $lyrics,
                $spotify, $apple, $youtube, $data['is_featured']);
        }
        finish_save($stmt, 'songs.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

//..AMBIL DATA UNTUK FORM EDIT (READ satu baris)
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM songs WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('songs.php');
    }
}

//..
/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT s.*, a.title AS album_title
        FROM songs s LEFT JOIN albums a ON a.id = s.album_id
        ORDER BY s.id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar lagu (<?= count($rows) ?>)</h3>
    <a href="songs.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>#</th><th>Sampul</th><th>Judul</th><th>Album</th><th>Audio</th><th>Lirik</th><th>Populer</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= thumb($row['cover_image']) ?></td>
          <td><?= e($row['title']) ?></td>
          <td><?= e($row['album_title'] ?? '—') ?></td>
          <td><?= $row['audio_file'] ? '<i class="bi bi-check-circle text-success"></i>' : '—' ?></td>
          <td><?= $row['lyrics'] ? '<i class="bi bi-check-circle text-success"></i>' : '—' ?></td>
          <td><?= $row['is_featured'] ? '<span class="badge text-bg-success">Ya</span>' : '<span class="badge text-bg-secondary">Tidak</span>' ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('songs.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit lagu' : 'Tambah lagu' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_text('title', 'Judul lagu', $data['title'], 'required maxlength="150"'); ?></div>
        <div class="col-md-6"><?php field_select('album_id', 'Album', $albums, $data['album_id'], '— Tanpa album (single) —'); ?></div>
        <div class="col-md-6"><?php field_text('duration_sec', 'Durasi (detik)', $data['duration_sec'], 'type="number" min="1" max="65535"', 'Boleh dikosongkan. Contoh: 185 = 3 menit 5 detik.'); ?></div>
      </div>
      <div class="row">
        <div class="col-md-6"><?php field_audio('audio_file', 'File lagu (MP3)', $data['audio_file']); ?></div>
        <div class="col-md-6"><?php field_image('cover_image', 'Gambar sampul', $data['cover_image']); ?></div>
      </div>
      <?php field_textarea('lyrics', 'Lirik', $data['lyrics'], 12, '', 'Ketik biasa, tanpa HTML: tekan Enter untuk baris baru, dan beri satu baris kosong untuk bait baru. Lirik lama yang berisi tag HTML tetap tampil seperti biasa.'); ?>
      <div class="row">
        <div class="col-md-4"><?php field_text('spotify_url', 'Link Spotify', $data['spotify_url'], 'type="url" maxlength="255"'); ?></div>
        <div class="col-md-4"><?php field_text('apple_url', 'Link Apple Music', $data['apple_url'], 'type="url" maxlength="255"'); ?></div>
        <div class="col-md-4"><?php field_text('youtube_url', 'Link YouTube Music', $data['youtube_url'], 'type="url" maxlength="255"'); ?></div>
      </div>
      <?php field_checkbox('is_featured', 'Tampilkan di "Top popular songs"', $data['is_featured']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('songs.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
