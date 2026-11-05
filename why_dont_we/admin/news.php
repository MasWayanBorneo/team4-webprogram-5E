<?php
/* CRUD tabel news — berita di Home, halaman News, dan halaman artikel (article.php?slug=...) */
require __DIR__ . '/includes/init.php';

$page_title = 'Berita';
$active = 'news';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$categories = options_from($conn, 'SELECT id, name FROM news_categories ORDER BY name');
$data = [
    'category_id' => '', 'slug' => '', 'title' => '', 'excerpt' => '', 'content' => '', 'thumbnail' => '',
    'read_time_min' => '', 'is_featured' => 0, 'published_at' => date('Y-m-d H:i:s'),
];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM news WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'news.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'category_id'   => post('category_id'),
        'slug'          => strtolower(post('slug')),
        'title'         => post('title'),
        'excerpt'       => post('excerpt'),
        'content'       => post('content'),
        'thumbnail'     => post('thumbnail_current'),
        'read_time_min' => post('read_time_min'),
        'is_featured'   => isset($_POST['is_featured']) ? 1 : 0,
        'published_at'  => from_datetime_input(post('published_at')),
    ];

    if (!isset($categories[$data['category_id']])) {
        $errors[] = 'Pilih kategori berita.';
    }
    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $data['slug']) || strlen($data['slug']) > 150) {
        $errors[] = 'Slug wajib diisi: huruf kecil, angka, dan tanda hubung saja, misalnya tour-dates-revealed.';
    }
    if ($data['title'] === '' || mb_strlen($data['title']) > 200) {
        $errors[] = 'Judul wajib diisi (maksimal 200 karakter).';
    }
    if (mb_strlen($data['excerpt']) > 300) {
        $errors[] = 'Ringkasan maksimal 300 karakter.';
    }
    if ($data['content'] === '') {
        $errors[] = 'Isi artikel wajib diisi.';
    }
    if ($data['read_time_min'] !== '' && !is_int_between($data['read_time_min'], 1, 255)) {
        $errors[] = 'Lama membaca harus berupa angka menit, misalnya 3.';
    }
    if (post('published_at') !== '' && !$data['published_at']) {
        $errors[] = 'Tanggal terbit tidak valid.';
    }
    $data['thumbnail'] = upload_image('thumbnail', $data['thumbnail'], $errors);

    if (!$errors) {
        $category_id = (int) $data['category_id'];
        $excerpt = null_if_empty($data['excerpt']);
        $thumbnail = null_if_empty($data['thumbnail']);
        $read_time = $data['read_time_min'] === '' ? null : (int) $data['read_time_min'];

        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE news SET category_id = ?, slug = ?, title = ?, excerpt = ?, content = ?, thumbnail = ?,
                read_time_min = ?, is_featured = ?, published_at = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'isssssiisi', $category_id, $data['slug'], $data['title'], $excerpt, $data['content'], $thumbnail,
                $read_time, $data['is_featured'], $data['published_at'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO news (category_id, slug, title, excerpt, content, thumbnail, read_time_min, is_featured, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'isssssiis', $category_id, $data['slug'], $data['title'], $excerpt, $data['content'], $thumbnail,
                $read_time, $data['is_featured'], $data['published_at']);
        }
        finish_save($stmt, 'news.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM news WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('news.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT n.id, n.slug, n.title, n.thumbnail, n.is_featured, n.published_at, c.name AS category
        FROM news n JOIN news_categories c ON c.id = n.category_id
        ORDER BY n.published_at DESC, n.id DESC'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar berita (<?= count($rows) ?>)</h3>
    <a href="news.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Gambar</th><th>Judul</th><th>Kategori</th><th>Terbit</th><th>Utama</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= thumb($row['thumbnail']) ?></td>
          <td><a href="../src/article/article.php?slug=<?= e($row['slug']) ?>" target="_blank"><?= e($row['title']) ?></a></td>
          <td><?= e($row['category']) ?></td>
          <td class="text-nowrap"><?= $row['published_at'] ? date('j M Y', strtotime($row['published_at'])) : '—' ?></td>
          <td><?= $row['is_featured'] ? '<span class="badge text-bg-success">Ya</span>' : '' ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('news.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit berita' : 'Tambah berita' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-8"><?php field_text('title', 'Judul', $data['title'], 'required maxlength="200"'); ?></div>
        <div class="col-md-4"><?php field_select('category_id', 'Kategori', $categories, $data['category_id'], null); ?></div>
        <div class="col-md-6"><?php field_text('slug', 'Slug (untuk URL)', $data['slug'], 'required maxlength="150" pattern="[a-z0-9]+(-[a-z0-9]+)*"', 'Contoh: tour-dates-revealed → article.php?slug=tour-dates-revealed'); ?></div>
        <div class="col-md-3"><?php field_text('published_at', 'Tanggal terbit', to_datetime_input($data['published_at']), 'type="datetime-local"'); ?></div>
        <div class="col-md-3"><?php field_text('read_time_min', 'Lama membaca (menit)', $data['read_time_min'], 'type="number" min="1" max="255"'); ?></div>
      </div>
      <?php field_textarea('excerpt', 'Ringkasan', $data['excerpt'], 2, 'maxlength="300"', '1–2 kalimat untuk kartu berita, maksimal 300 karakter.'); ?>
      <?php field_textarea('content', 'Isi artikel (HTML)', $data['content'], 12, 'required style="font-family:monospace"', 'Gunakan <p> untuk paragraf dan <h2> untuk subjudul.'); ?>
      <?php field_image('thumbnail', 'Gambar berita', $data['thumbnail']); ?>
      <?php field_checkbox('is_featured', 'Jadikan berita utama di halaman News', $data['is_featured']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('news.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
