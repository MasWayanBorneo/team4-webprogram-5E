<?php
/* CRUD tabel limelight_members — pendaftar fan club dari form Limelight (halaman About) */
require __DIR__ . '/includes/init.php';

$page_title = 'Anggota Limelight';
$active = 'limelight_members';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$members = options_from($conn, 'SELECT id, full_name FROM members ORDER BY sort_order, id');
$data = ['full_name' => '', 'email' => '', 'date_of_birth' => '', 'favorite_member_id' => ''];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM limelight_members WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'limelight_members.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
// aturan isian sama dengan form Limelight di halaman About
if (is_post() && post('do') === 'save') {
    $data = [
        'full_name'          => preg_replace('/\s+/u', ' ', post('full_name')),
        'email'              => post('email'),
        'date_of_birth'      => post('date_of_birth'),
        'favorite_member_id' => post('favorite_member_id'),
    ];
    $dob = DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);

    if (!preg_match('/^\p{L}+( \p{L}+)*$/u', $data['full_name']) || mb_strlen($data['full_name']) < 2 || mb_strlen($data['full_name']) > 100) {
        $errors[] = 'Nama hanya boleh berisi huruf dan spasi, 2–100 karakter.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 100) {
        $errors[] = 'Email tidak valid.';
    }
    if (!$dob || $dob->format('Y-m-d') !== $data['date_of_birth'] || $dob > new DateTime()) {
        $errors[] = 'Tanggal lahir tidak valid.';
    }
    if ($data['favorite_member_id'] !== '' && !isset($members[$data['favorite_member_id']])) {
        $errors[] = 'Personel favorit tidak ditemukan.';
    }

    if (!$errors) {
        $favorite = $data['favorite_member_id'] === '' ? null : (int) $data['favorite_member_id'];
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE limelight_members SET full_name = ?, email = ?, date_of_birth = ?, favorite_member_id = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'sssii', $data['full_name'], $data['email'], $data['date_of_birth'], $favorite, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO limelight_members (full_name, email, date_of_birth, favorite_member_id) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssi', $data['full_name'], $data['email'], $data['date_of_birth'], $favorite);
        }
        finish_save($stmt, 'limelight_members.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM limelight_members WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('limelight_members.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, '
        SELECT l.*, m.full_name AS favorite_name
        FROM limelight_members l LEFT JOIN members m ON m.id = l.favorite_member_id
        ORDER BY l.joined_at DESC, l.id DESC'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar anggota Limelight (<?= count($rows) ?>)</h3>
    <a href="limelight_members.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Nama</th><th>Email</th><th>Tanggal lahir</th><th>Personel favorit</th><th>Bergabung</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= e($row['full_name']) ?></td>
          <td><?= e($row['email']) ?></td>
          <td class="text-nowrap"><?= date('j M Y', strtotime($row['date_of_birth'])) ?></td>
          <td><?= e($row['favorite_name'] ?? '—') ?></td>
          <td class="text-nowrap"><?= date('j M Y H:i', strtotime($row['joined_at'])) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('limelight_members.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit anggota Limelight' : 'Tambah anggota Limelight' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_text('full_name', 'Nama lengkap', $data['full_name'], 'required minlength="2" maxlength="100" pattern="[\p{L} ]+"', 'Huruf dan spasi saja.'); ?></div>
        <div class="col-md-6"><?php field_text('email', 'Email', $data['email'], 'type="email" required maxlength="100"', 'Satu email hanya bisa terdaftar sekali.'); ?></div>
        <div class="col-md-6"><?php field_text('date_of_birth', 'Tanggal lahir', $data['date_of_birth'], 'type="date" required max="' . date('Y-m-d') . '"'); ?></div>
        <div class="col-md-6"><?php field_select('favorite_member_id', 'Personel favorit', $members, $data['favorite_member_id'], '— Tidak ada —'); ?></div>
      </div>
    </div>
    <div class="card-footer"><?php form_buttons('limelight_members.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
