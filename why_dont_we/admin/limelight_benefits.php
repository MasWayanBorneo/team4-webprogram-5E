<?php
/* CRUD tabel limelight_benefits — kartu benefit Limelight di halaman About */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Benefit Limelight';
$active = 'limelight_benefits';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$data = ['icon' => 'fa-star', 'title' => '', 'description' => '', 'sort_order' => '0', 'is_active' => 1];

//.. HAPUS (DELETE)
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM limelight_benefits WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'limelight_benefits.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
if (is_post() && post('do') === 'save') {
    $data = [
        'icon'        => post('icon'),
        'title'       => post('title'),
        'description' => post('description'),
        'sort_order'  => post('sort_order') === '' ? '0' : post('sort_order'),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];

    // nama ikon Font Awesome, misalnya fa-ticket atau fa-cake-candles
    if (!preg_match('/^fa-[a-z0-9-]+$/', $data['icon']) || strlen($data['icon']) > 50) {
        $errors[] = 'Ikon harus berupa nama ikon Font Awesome, misalnya fa-ticket.';
    }
    if ($data['title'] === '' || mb_strlen($data['title']) > 100) {
        $errors[] = 'Judul benefit wajib diisi (maksimal 100 karakter).';
    }
    if ($data['description'] === '' || mb_strlen($data['description']) > 255) {
        $errors[] = 'Keterangan wajib diisi (maksimal 255 karakter).';
    }
    if (!is_int_between($data['sort_order'], 0, 255)) {
        $errors[] = 'Urutan harus berupa angka 0–255.';
    }

    if (!$errors) {
        $sort = (int) $data['sort_order'];
        if ($id) {
            $stmt = mysqli_prepare($conn, 'UPDATE limelight_benefits SET icon = ?, title = ?, description = ?, sort_order = ?, is_active = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'sssiii', $data['icon'], $data['title'], $data['description'], $sort, $data['is_active'], $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO limelight_benefits (icon, title, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssii', $data['icon'], $data['title'], $data['description'], $sort, $data['is_active']);
        }
        finish_save($stmt, 'limelight_benefits.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

//..AMBIL DATA UNTUK FORM EDIT (READ satu baris)
if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM limelight_benefits WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('limelight_benefits.php');
    }
}

/* --- DAFTAR (READ semua baris) ----------------------------------------------- */
if ($action === 'list') {
    $rows = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM limelight_benefits ORDER BY sort_order, id'), MYSQLI_ASSOC);
}

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar benefit (<?= count($rows) ?>)</h3>
    <a href="limelight_benefits.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Urutan</th><th>Ikon</th><th>Judul</th><th>Keterangan</th><th>Tampil</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= (int) $row['sort_order'] ?></td>
          <td><code><?= e($row['icon']) ?></code></td>
          <td><?= e($row['title']) ?></td>
          <td><?= e($row['description']) ?></td>
          <td><?= $row['is_active'] ? '<span class="badge text-bg-success">Ya</span>' : '<span class="badge text-bg-secondary">Disembunyikan</span>' ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('limelight_benefits.php', $row['id']) ?></td>
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
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit benefit' : 'Tambah benefit' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <div class="row">
        <div class="col-md-4"><?php field_text('icon', 'Ikon (Font Awesome)', $data['icon'], 'required maxlength="50" pattern="fa-[a-z0-9\-]+"', 'Cari nama ikon di fontawesome.com, contoh: fa-ticket'); ?></div>
        <div class="col-md-5"><?php field_text('title', 'Judul', $data['title'], 'required maxlength="100"'); ?></div>
        <div class="col-md-3"><?php field_text('sort_order', 'Urutan', $data['sort_order'], 'type="number" min="0" max="255"'); ?></div>
      </div>
      <?php field_textarea('description', 'Keterangan', $data['description'], 3, 'required maxlength="255"'); ?>
      <?php field_checkbox('is_active', 'Tampilkan di halaman About', $data['is_active']); ?>
    </div>
    <div class="card-footer"><?php form_buttons('limelight_benefits.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
