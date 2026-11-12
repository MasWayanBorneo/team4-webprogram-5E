<?php
/* CRUD tabel ticket_orders — pesanan tiket & pre-order dari form tiket.
   Di sini juga panitia mengonfirmasi pre-order: ubah status pending → paid. */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Pesanan Tiket';
$active = 'ticket_orders';

$action = $_GET['action'] ?? 'list';   // list | add | edit
$id = (int) ($_GET['id'] ?? 0);
$errors = [];
$rows = [];
$statuses = ['pending' => 'Pending (pre-order, belum dikonfirmasi)', 'paid' => 'Paid (lunas, tiket terbit)', 'cancelled' => 'Cancelled (batal)'];

// pilihan kategori tiket beserta konser dan harganya
$category_rows = mysqli_fetch_all(mysqli_query($conn, '
    SELECT c.id, c.name, c.price, k.venue_name, k.event_date
    FROM ticket_categories c JOIN concerts k ON k.id = c.concert_id
    ORDER BY k.event_date, c.price'), MYSQLI_ASSOC);
$categories = [];
$prices = [];
foreach ($category_rows as $row) {
    $categories[$row['id']] = $row['venue_name'] . ' — ' . date('j M Y', strtotime($row['event_date'])) . ' — ' . $row['name'] . ' ($' . number_format($row['price'], 2) . ')';
    $prices[$row['id']] = $row['price'];
}

$data = ['invoice_no' => '', 'ticket_category_id' => '', 'full_name' => '', 'email' => '', 'phone' => '', 'quantity' => '1', 'unit_price' => '', 'status' => 'paid'];

/* --- HAPUS (DELETE) ---------------------------------------------------------- */
if (is_post() && post('do') === 'delete') {
    $delete_id = (int) $_POST['id'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM ticket_orders WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    finish_delete($stmt, 'ticket_orders.php');
}

/* --- SIMPAN (INSERT / UPDATE) ------------------------------------------------ */
//..
if (is_post() && post('do') === 'save') {
    $data = [
        'invoice_no'         => post('invoice_no'),
        'ticket_category_id' => post('ticket_category_id'),
        'full_name'          => preg_replace('/\s+/u', ' ', post('full_name')),
        'email'              => post('email'),
        'phone'              => post('phone'),
        'quantity'           => post('quantity'),
        'unit_price'         => post('unit_price'),
        'status'             => post('status'),
    ];

    if (!isset($categories[$data['ticket_category_id']])) {
        $errors[] = 'Pilih kategori tiket.';
    }
    if (!preg_match('/^\p{L}+( \p{L}+)*$/u', $data['full_name']) || mb_strlen($data['full_name']) < 2 || mb_strlen($data['full_name']) > 100) {
        $errors[] = 'Nama hanya boleh berisi huruf dan spasi, 2–100 karakter.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 100) {
        $errors[] = 'Email tidak valid.';
    }
    if (!preg_match('/^\+?[0-9]{8,15}$/', $data['phone'])) {
        $errors[] = 'Nomor HP hanya angka, 8–15 digit.';
    }
    if (!is_int_between($data['quantity'], 1, 255)) {
        $errors[] = 'Jumlah tiket harus berupa angka 1 atau lebih.';
    }
    // harga kosong → pakai harga kategori yang berlaku sekarang
    if ($data['unit_price'] === '' && isset($prices[$data['ticket_category_id']])) {
        $data['unit_price'] = $prices[$data['ticket_category_id']];
    }
    if (!is_numeric($data['unit_price']) || $data['unit_price'] < 0) {
        $errors[] = 'Harga satuan harus berupa angka 0 atau lebih.';
    }
    if (!isset($statuses[$data['status']])) {
        $errors[] = 'Status tidak valid.';
    }

    if (!$errors) {
        $category_id = (int) $data['ticket_category_id'];
        $quantity = (int) $data['quantity'];
        $unit_price = (float) $data['unit_price'];
        $total = $unit_price * $quantity;   // total selalu dihitung ulang di server

        if ($id) {
            // nomor invoice tidak diubah, karena sudah dipakai di link invoice dan kode QR tiket
            $stmt = mysqli_prepare($conn, 'UPDATE ticket_orders SET ticket_category_id = ?, full_name = ?, email = ?, phone = ?,
                quantity = ?, unit_price = ?, total = ?, status = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'isssiddsi', $category_id, $data['full_name'], $data['email'], $data['phone'],
                $quantity, $unit_price, $total, $data['status'], $id);
        } else {
            $invoice_no = 'WDW-' . strtoupper(bin2hex(random_bytes(4)));
            $stmt = mysqli_prepare($conn, 'INSERT INTO ticket_orders (invoice_no, ticket_category_id, full_name, email, phone, quantity, unit_price, total, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sisssidds', $invoice_no, $category_id, $data['full_name'], $data['email'], $data['phone'],
                $quantity, $unit_price, $total, $data['status']);
        }
        finish_save($stmt, 'ticket_orders.php', $errors);
    }
    $action = $id ? 'edit' : 'add';
}

//.. AMBIL DATA UNTUK FORM EDIT (READ satu baris)

if ($action === 'edit' && !is_post()) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM ticket_orders WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        flash('warning', 'Data tidak ditemukan.');
        redirect('ticket_orders.php');
    }
}



/* --- DAFTAR (READ dengan pencarian & filter) --------------------------------- */
$concerts = options_from($conn, "SELECT id, CONCAT(venue_name, ' — ', DATE_FORMAT(event_date, '%e %b %Y')) FROM concerts ORDER BY event_date");
$checkin_filters = ['yes' => 'Sudah check-in', 'no' => 'Belum check-in'];
$filter = [
    'q'       => trim($_GET['q'] ?? ''),        // cari nomor invoice / nama / email / HP
    'concert' => $_GET['concert'] ?? '',
    'status'  => $_GET['status'] ?? '',
    'checkin' => $_GET['checkin'] ?? '',
];

if ($action === 'list') {
    // syarat WHERE disusun sesuai filter yang diisi; nilainya tetap lewat prepared statement (?)
    $where = [];
    $types = '';
    $params = [];
    if ($filter['q'] !== '') {
        $where[] = '(o.invoice_no LIKE ? OR o.full_name LIKE ? OR o.email LIKE ? OR o.phone LIKE ?)';
        $like = '%' . $filter['q'] . '%';
        $types .= 'ssss';
        array_push($params, $like, $like, $like, $like);
    }
    if (isset($concerts[$filter['concert']])) {
        $where[] = 'k.id = ?';
        $types .= 'i';
        $params[] = (int) $filter['concert'];
    }
    if (isset($statuses[$filter['status']])) {
        $where[] = 'o.status = ?';
        $types .= 's';
        $params[] = $filter['status'];
    }
    if ($filter['checkin'] === 'yes') {
        $where[] = 'o.checked_in_at IS NOT NULL';
    } elseif ($filter['checkin'] === 'no') {
        $where[] = 'o.checked_in_at IS NULL';
    }

    $stmt = mysqli_prepare($conn, '
        SELECT o.*, c.name AS category, k.venue_name
        FROM ticket_orders o
        JOIN ticket_categories c ON c.id = o.ticket_category_id
        JOIN concerts k ON k.id = c.concert_id
        ' . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . '
        ORDER BY o.created_at DESC, o.id DESC');
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    $is_filtered = $where !== [];
}

$status_badges = ['pending' => 'text-bg-warning', 'paid' => 'text-bg-success', 'cancelled' => 'text-bg-danger'];

require __DIR__ . '/includes/header.php';
?>
<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h3 class="card-title mb-0">Daftar pesanan (<?= count($rows) ?><?= $is_filtered ? ' sesuai filter' : '' ?>)</h3>
    <a href="ticket_orders.php?action=add" class="btn btn-primary btn-sm ms-auto"><i class="bi bi-plus-lg"></i> Tambah</a>
  </div>
  <!-- pencarian & filter (GET, jadi hasilnya bisa di-bookmark atau dibagikan ke panitia lain) -->
  <div class="card-body border-bottom">
    <form method="get" class="row g-2">
      <div class="col-lg-4">
        <input class="form-control" name="q" value="<?= e($filter['q']) ?>" placeholder="Cari invoice, nama, email, atau HP">
      </div>
      <div class="col-lg-3 col-md-4">
        <select class="form-select" name="concert">
          <option value="">Semua konser</option>
          <?php foreach ($concerts as $value => $label): ?>
          <option value="<?= $value ?>"<?= (string) $value === $filter['concert'] ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-lg-2 col-md-3">
        <select class="form-select" name="status">
          <option value="">Semua status</option>
          <?php foreach ($statuses as $value => $label): ?>
          <option value="<?= $value ?>"<?= $value === $filter['status'] ? ' selected' : '' ?>><?= e(ucfirst($value)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-lg-2 col-md-3">
        <select class="form-select" name="checkin">
          <option value="">Semua check-in</option>
          <?php foreach ($checkin_filters as $value => $label): ?>
          <option value="<?= $value ?>"<?= $value === $filter['checkin'] ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-lg-1 col-md-2 d-flex gap-1">
        <button class="btn btn-primary flex-fill" title="Cari"><i class="bi bi-search"></i></button>
        <?php if ($is_filtered): ?><a href="ticket_orders.php" class="btn btn-outline-secondary" title="Hapus filter"><i class="bi bi-x-lg"></i></a><?php endif; ?>
      </div>
    </form>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead><tr><th>Invoice</th><th>Pemesan</th><th>Konser / kategori</th><th>Jumlah</th><th>Total</th><th>Status</th><th>Check-in</th><th>Dipesan</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <td><a href="../src/ticket/invoice.php?no=<?= urlencode($row['invoice_no']) ?>" target="_blank"><code><?= e($row['invoice_no']) ?></code></a></td>
          <td><?= e($row['full_name']) ?><br><small class="text-secondary"><?= e($row['email']) ?> · <?= e($row['phone']) ?></small></td>
          <td><?= e($row['venue_name']) ?><br><small class="text-secondary"><?= e($row['category']) ?></small></td>
          <td><?= (int) $row['quantity'] ?></td>
          <td>$<?= number_format($row['total'], 2) ?></td>
          <td><span class="badge <?= $status_badges[$row['status']] ?>"><?= e(ucfirst($row['status'])) ?></span></td>
          <td class="text-nowrap">
            <?php if ($row['checked_in_at']): ?>
            <span class="badge text-bg-dark"><i class="bi bi-check2"></i> <?= date('j M H:i', strtotime($row['checked_in_at'])) ?></span>
            <?php elseif ($row['status'] === 'paid'): ?>
            <a href="checkin.php?code=<?= urlencode($row['invoice_no']) ?>" class="small">Check-in</a>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td class="text-nowrap"><?= date('j M Y H:i', strtotime($row['created_at'])) ?></td>
          <td class="text-end text-nowrap"><?= action_buttons('ticket_orders.php', $row['id']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="9" class="text-center text-secondary py-4"><?= $is_filtered ? 'Tidak ada pesanan yang cocok dengan filter.' : 'Belum ada data.' ?></td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="card">
  <form method="post">
    <div class="card-header"><h3 class="card-title mb-0"><?= $id ? 'Edit pesanan ' . e($data['invoice_no']) : 'Tambah pesanan' ?></h3></div>
    <div class="card-body">
      <?php show_errors($errors); ?>
      <?php if ($id): ?><input type="hidden" name="invoice_no" value="<?= e($data['invoice_no']) ?>"><?php endif; ?>
      <?php field_select('ticket_category_id', 'Konser & kategori tiket', $categories, $data['ticket_category_id'], null); ?>
      <div class="row">
        <div class="col-md-4"><?php field_text('full_name', 'Nama pemesan', $data['full_name'], 'required minlength="2" maxlength="100" pattern="[\p{L} ]+"'); ?></div>
        <div class="col-md-4"><?php field_text('email', 'Email', $data['email'], 'type="email" required maxlength="100"'); ?></div>
        <div class="col-md-4"><?php field_text('phone', 'Nomor HP', $data['phone'], 'required maxlength="16" pattern="\+?[0-9]{8,15}"'); ?></div>
        <div class="col-md-4"><?php field_text('quantity', 'Jumlah tiket', $data['quantity'], 'type="number" min="1" max="255" required'); ?></div>
        <div class="col-md-4"><?php field_text('unit_price', 'Harga satuan (USD)', $data['unit_price'], 'type="number" min="0" step="0.01"', 'Kosongkan untuk memakai harga kategori sekarang. Total dihitung otomatis.'); ?></div>
        <div class="col-md-4"><?php field_select('status', 'Status', $statuses, $data['status'], null, 'Konfirmasi pre-order: ubah Pending menjadi Paid.'); ?></div>
      </div>
    </div>
    <div class="card-footer"><?php form_buttons('ticket_orders.php'); ?></div>
  </form>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
