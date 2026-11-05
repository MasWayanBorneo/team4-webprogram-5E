<?php
/* CRUD tabel members — personel di Home, About, dan halaman personel (member.php?m=slug) */
require __DIR__ . '/includes/init.php';

$page_title = 'Personel';
$active = 'members';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$data = [
    'slug' => '', 'full_name' => '', 'roles' => '', 'birth_date' => '', 'zodiac' => '', 'hometown' => '',
    'extra_label' => '', 'extra_value' => '', 'bio' => '', 'photo' => '', 'hero_photo' => '', 'sort_order' => '0',
];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
// trivia personel ikut terhapus (ON DELETE CASCADE); pendaftar Limelight yang memilih
// personel ini tetap ada, kolom favoritnya dikosongkan (ON DELETE SET NULL)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM members WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'members.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'slug'        => strtolower(post('slug')),
        'full_name'   => post('full_name'),
        'roles'       => post('roles'),
        'birth_date'  => post('birth_date'),
        'zodiac'      => post('zodiac'),
        'hometown'    => post('hometown'),
        'extra_label' => post('extra_label'),
        'extra_value' => post('extra_value'),
        'bio'         => post('bio'),
        'photo'       => post('photo_current'),
        'hero_photo'  => post('hero_photo_current'),
        'sort_order'  => post('sort_order') === '' ? '0' : post('sort_order'),
    ];

    // slug dipakai di URL: huruf kecil, angka, dan tanda hubung saja
    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $data['slug']) || strlen($data['slug']) > 50) {
        $errors[] = 'Slug wajib diisi: huruf kecil, angka, dan tanda hubung saja, misalnya jonah.';
    }
    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 100) {
        $errors[] = 'Nama lengkap wajib diisi (maksimal 100 karakter).';
    }
    if ($data['roles'] === '' || mb_strlen($data['roles']) > 100) {
        $errors[] = 'Peran wajib diisi, misalnya Vocals · Keys.';
    }
    if ($data['birth_date'] !== '' && !DateTime::createFromFormat('Y-m-d', $data['birth_date'])) {
        $errors[] = 'Tanggal lahir tidak valid.';
    }
    if (($data['extra_label'] === '') !== ($data['extra_value'] === '')) {
        $errors[] = 'Judul dan isi fakta keempat harus diisi berdua, atau dikosongkan berdua.';
    }
    if (!is_int_between($data['sort_order'], 0, 255)) {
        $errors[] = 'Urutan harus berupa angka 0–255.';
    }
    $data['photo'] = upload_image('photo', $data['photo'], $errors);
    $data['hero_photo'] = upload_image('hero_photo', $data['hero_photo'], $errors);

    if (!$errors) {
        $birth = null_if_empty($data['birth_date']);
        $zodiac = null_if_empty($data['zodiac']);
        $hometown = null_if_empty($data['hometown']);
        $extra_label = null_if_empty($data['extra_label']);
        $extra_value = null_if_empty($data['extra_value']);
        $bio = null_if_empty($data['bio']);
        $photo = null_if_empty($data['photo']);
        $hero = null_if_empty($data['hero_photo']);
        $sort = (int) $data['sort_order'];

        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE members SET slug = ?, full_name = ?, roles = ?, birth_date = ?, zodiac = ?, hometown = ?,
                extra_label = ?, extra_value = ?, bio = ?, photo = ?, hero_photo = ?, sort_order = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'sssssssssssii', $data['slug'], $data['full_name'], $data['roles'], $birth, $zodiac, $hometown,
                $extra_label, $extra_value, $bio, $photo, $hero, $sort, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO members (slug, full_name, roles, birth_date, zodiac, hometown,
                extra_label, extra_value, bio, photo, hero_photo, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssssssssssi', $data['slug'], $data['full_name'], $data['roles'], $birth, $zodiac, $hometown,
                $extra_label, $extra_value, $bio, $photo, $hero, $sort);
        }
        finish_save($stmt, 'members.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM members WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('members.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT m.*, (SELECT COUNT(*) FROM member_trivia t WHERE t.member_id = m.id) AS trivia_count
        FROM members m ORDER BY m.sort_order, m.id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar personel (<?= count($rows) ?>)</h3>
    <a href="members.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Urutan</th><th>Foto</th><th>Nama</th><th>Slug</th><th>Peran</th><th>Trivia</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= (int) $row['sort_order'] ?></td>
          <td><?= thumb($row['photo']) ?></td>
          <td><?= e($row['full_name']) ?></td>
          <td><a href="../src/member/member.php?m=<?= e($row['slug']) ?>" target="_blank"><code><?= e($row['slug']) ?></code></a></td>
          <td><?= e($row['roles']) ?></td>
          <td><a href="member_trivia.php"><?= (int) $row['trivia_count'] ?> trivia</a></td>
          <td class="text-end text-nowrap"><?= action_buttons('members.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit personel' : 'Tambah personel' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_text('full_name', 'Nama lengkap', $data['full_name'], 'required maxlength="100"'); ?></div>
        <div class="col-md-6"><?php field_text('slug', 'Slug (untuk URL)', $data['slug'], 'required maxlength="50" pattern="[a-z0-9]+(-[a-z0-9]+)*"', 'Contoh: jonah → member.php?m=jonah'); ?></div>
        <div class="col-md-6"><?php field_text('roles', 'Peran', $data['roles'], 'required maxlength="100"', 'Contoh: Vocals · Keys'); ?></div>
        <div class="col-md-6"><?php field_text('sort_order', 'Urutan tampil', $data['sort_order'], 'type="number" min="0" max="255"', 'Angka kecil tampil lebih dulu di halaman About.'); ?></div>
        <div class="col-md-4"><?php field_text('birth_date', 'Tanggal lahir', $data['birth_date'], 'type="date"'); ?></div>
        <div class="col-md-4"><?php field_text('zodiac', 'Zodiak', $data['zodiac'], 'maxlength="20"'); ?></div>
        <div class="col-md-4"><?php field_text('hometown', 'Kota asal', $data['hometown'], 'maxlength="100"'); ?></div>
        <div class="col-md-6"><?php field_text('extra_label', 'Fakta keempat — judul', $data['extra_label'], 'maxlength="50"', 'Contoh: Sports, Instruments, Position'); ?></div>
        <div class="col-md-6"><?php field_text('extra_value', 'Fakta keempat — isi', $data['extra_value'], 'maxlength="150"', 'Contoh: Baseball'); ?></div>
      </div>
      <?php field_textarea('bio', 'Profil singkat', $data['bio'], 4); ?>
      <div class="row">
        <div class="col-md-6"><?php field_image('photo', 'Foto profil', $data['photo']); ?></div>
        <div class="col-md-6"><?php field_image('hero_photo', 'Foto besar', $data['hero_photo']); ?></div>
      </div>
    </div>
    <div class="card-footer"><?php form_buttons('members.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
