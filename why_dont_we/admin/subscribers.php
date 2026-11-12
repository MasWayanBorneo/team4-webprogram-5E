<?php
/* CRUD tabel subscribers — email dari form subscribe di Home, News, dan Tour */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Subscriber';
$active = 'subscribers';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$sources = ['home' => 'Home', 'news' => 'News', 'tour' => 'Tour'];
$data = ['email' => '', 'source' => 'home'];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM subscribers WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'subscribers.php');
}

//..
/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = ['email' => post('email'), 'source' => post('source')];

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 100) {
        $errors[] = 'Email tidak valid.';
    }
    if (!isset($sources[$data['source']])) {
        $errors[] = 'Sumber tidak valid.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE subscribers SET email = ?, source = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssi', $data['email'], $data['source'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO subscribers (email, source) VALUES (?, ?)');
            mysqli_stmt_bind_param($stmt, 'ss', $data['email'], $data['source']);
        }
        finish_save($stmt, 'subscribers.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

/* --- AMBIL DATA UNTUK FORM EDIT (READ satu baris) ---------------------------- */
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM subscribers WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('subscribers.php');
    }
}



/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM subscribers ORDER BY subscribed_at DESC, id DESC'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar subscriber (<?= count($rows) ?>)</h3>
    <a href="subscribers.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Email</th><th>Dari halaman</th><th>Waktu subscribe</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= e($row['email']) ?></td>
          <td><?= e($sources[$row['source']]) ?></td>
          <td class="text-nowrap"><?= date('j M Y H:i', strtotime($row['subscribed_at'])) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('subscribers.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit subscriber' : 'Tambah subscriber' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php field_text('email', 'Email', $data['email'], 'type="email" required maxlength="100"', 'Satu email hanya bisa terdaftar sekali.'); ?>
      <?php field_select('source', 'Dari halaman', $sources, $data['source']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('subscribers.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
