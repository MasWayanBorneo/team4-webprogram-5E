<?php
/* CRUD tabel admins — akun dashboard admin. Password disimpan sebagai hash (password_hash),
   tidak pernah sebagai teks asli, dan tidak pernah ditampilkan. */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */
/** @var array $current_admin */

$page_title = 'Akun Admin';
$active = 'admins';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$data = ['username' => '', 'email' => ''];

//.. HAPUS (DELETE)
// akun admin terakhir dan akun yang sedang dipakai login tidak boleh dihapus,
// supaya dashboard tetap punya admin dan tidak ada yang terkunci di luar
if (is_post() && post('do') === 'delete') {
    $total = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM admins'))[0];
    if ($total <= 1) {
        flash('danger', 'Akun admin terakhir tidak bisa dihapus.');
        redirect('admins.php');
    }
    $delete_id = (int) $_POST['id'];
    if ($delete_id === (int) $current_admin['id']) {
        flash('danger', 'Akun yang sedang dipakai login tidak bisa dihapus. Minta admin lain untuk menghapusnya.');
        redirect('admins.php');
    }
    $stmt = mysqli_prepare($conn, 'DELETE FROM admins WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'admins.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = ['username' => post('username'), 'email' => post('email')];
    $password = $_POST['password'] ?? '';             // password tidak di-trim: spasi boleh jadi bagian password
    $confirm = $_POST['password_confirm'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $data['username'])) {
        $errors[] = 'Username 3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 100) {
        $errors[] = 'Email tidak valid.';
    }
    // akun baru wajib punya password; saat edit, password kosong = password lama tidak diubah
    if (!$id || $password !== '') {
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
    }

    if (!$errors) {
        if ($id && $password === '') {
            $stmt = mysqli_prepare($conn, 'UPDATE admins SET username = ?, email = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssi', $data['username'], $data['email'], $id);
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($id) {
                $stmt = mysqli_prepare($conn, 'UPDATE admins SET username = ?, email = ?, password_hash = ? WHERE id = ?');
                mysqli_stmt_bind_param($stmt, 'sssi', $data['username'], $data['email'], $hash, $id);
            } else {
                $stmt = mysqli_prepare($conn, 'INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'sss', $data['username'], $data['email'], $hash);
            }
        }
        finish_save($stmt, 'admins.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

//..AMBIL DATA UNTUK FORM EDIT (READ satu baris, tanpa password_hash)

if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT username, email FROM admins WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('admins.php');
    }
}

/* --- DAFTAR (READ semua baris, tanpa password_hash) -------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT id, username, email, created_at FROM admins ORDER BY id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar akun admin (<?= count($rows) ?>)</h3>
    <a href="admins.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Dibuat</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= e($row['username']) ?></td>
          <td><?= e($row['email']) ?></td>
          <td class="text-nowrap"><?= date('j M Y H:i', strtotime($row['created_at'])) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('admins.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="5" class="text-center text-secondary py-4">Belum ada akun admin.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit akun admin' : 'Tambah akun admin' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-6"><?php field_text('username', 'Username', $data['username'], 'required minlength="3" maxlength="50" pattern="[A-Za-z0-9_.\-]{3,50}" autocomplete="username"'); ?></div>
        <div class="col-md-6"><?php field_text('email', 'Email', $data['email'], 'type="email" required maxlength="100"'); ?></div>
        <div class="col-md-6"><?php field_text('password', 'Password', '', 'type="password" minlength="8" autocomplete="new-password"' . ($id ? '' : ' required'), $id ? 'Kosongkan kalau password tidak diubah.' : 'Minimal 8 karakter.'); ?></div>
        <div class="col-md-6"><?php field_text('password_confirm', 'Ulangi password', '', 'type="password" minlength="8" autocomplete="new-password"' . ($id ? '' : ' required')); ?></div>
      </div>
    </div>
    <div class="card-footer"><?php form_buttons('admins.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
