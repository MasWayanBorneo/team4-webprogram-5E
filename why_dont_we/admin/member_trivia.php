<?php
/* CRUD tabel member_trivia — trivia di halaman setiap personel */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Trivia Personel';
$active = 'member_trivia';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$members = options_from($conn, 'SELECT id, full_name FROM members ORDER BY sort_order, id');
$data = ['member_id' => '', 'content' => '', 'sort_order' => '1'];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM member_trivia WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'member_trivia.php');
}

//..
/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'member_id'  => post('member_id'),
        'content'    => post('content'),
        'sort_order' => post('sort_order') === '' ? '0' : post('sort_order'),
    ];

    if (!isset($members[$data['member_id']])) {
        $errors[] = 'Pilih personel pemilik trivia.';
    }
    if ($data['content'] === '' || mb_strlen($data['content']) > 255) {
        $errors[] = 'Isi trivia wajib diisi (maksimal 255 karakter).';
    }
    if (!is_int_between($data['sort_order'], 0, 255)) {
        $errors[] = 'Urutan harus berupa angka 0–255.';
    }

    if (!$errors) {
        $member_id = (int) $data['member_id'];
        $sort = (int) $data['sort_order'];
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE member_trivia SET member_id = ?, content = ?, sort_order = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'isii', $member_id, $data['content'], $sort, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO member_trivia (member_id, content, sort_order) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'isi', $member_id, $data['content'], $sort);
        }
        finish_save($stmt, 'member_trivia.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM member_trivia WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('member_trivia.php');
    }
}

//..
/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT t.*, m.full_name
        FROM member_trivia t JOIN members m ON m.id = t.member_id
        ORDER BY m.sort_order, t.sort_order, t.id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar trivia (<?= count($rows) ?>)</h3>
    <a href="member_trivia.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Personel</th><th>Urutan</th><th>Trivia</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td class="text-nowrap"><?= e($row['full_name']) ?></td>
          <td><?= (int) $row['sort_order'] ?></td>
          <td><?= e($row['content']) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('member_trivia.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit trivia' : 'Tambah trivia' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php field_select('member_id', 'Personel', $members, $data['member_id'], null); ?>
      <?php field_textarea('content', 'Isi trivia', $data['content'], 3, 'required maxlength="255"'); ?>
      <?php field_text('sort_order', 'Urutan tampil', $data['sort_order'], 'type="number" min="0" max="255"', 'Angka kecil tampil lebih dulu di halaman personel.'); ?>
    </div>
    <div class="card-footer"><?php form_buttons('member_trivia.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
