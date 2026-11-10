<?php
/* PEMBUATAN AKUN ADMIN PERTAMA — hanya bisa dibuka selama tabel admins masih kosong.
   Setelah akun pertama dibuat, halaman ini otomatis tertutup (diarahkan ke login);
   akun admin berikutnya dibuat dari menu Akun Admin di dashboard. */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */

$page_title = 'Buat akun admin pertama';

$admin_count = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM admins'))[0];
if ($admin_count > 0) {
    redirect('login.php');
}

$errors = [];
$data = ['username' => '', 'email' => ''];

if (is_post()) {
    $data = ['username' => post('username'), 'email' => post('email')];
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    // aturan sama dengan halaman Akun Admin (admins.php)
    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $data['username'])) {
        $errors[] = 'Username 3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 100) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // INSERT ... SELECT ... WHERE NOT EXISTS: akun hanya dibuat kalau tabel masih kosong,
        // jadi dua orang yang mengirim form bersamaan tidak bisa sama-sama membuat akun pertama
        $stmt = mysqli_prepare($conn, 'INSERT INTO admins (username, email, password_hash)
            SELECT ?, ?, ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM admins)');
        mysqli_stmt_bind_param($stmt, 'sss', $data['username'], $data['email'], $hash);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = mysqli_insert_id($conn);
            unset($_SESSION['csrf']);
            flash('success', 'Akun admin pertama berhasil dibuat. Selamat datang, ' . $data['username'] . '.');
            redirect('index.php');
        }
        redirect('login.php');   // keduluan akun lain yang dibuat bersamaan
    }
}

require __DIR__ . '/includes/auth_header.php';
?>
<p class="login-box-msg">Belum ada akun admin. Buat akun pertama untuk mengelola dashboard.</p>
<?php if ($errors): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" action="setup.php">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label" for="f-username">Username</label>
    <input class="form-control" id="f-username" name="username" value="<?= e($data['username']) ?>" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_.\-]{3,50}" autocomplete="username" autofocus>
  </div>
  <div class="mb-3">
    <label class="form-label" for="f-email">Email</label>
    <input class="form-control" id="f-email" type="email" name="email" value="<?= e($data['email']) ?>" required maxlength="100">
  </div>
  <div class="mb-3">
    <label class="form-label" for="f-password">Password</label>
    <input class="form-control" id="f-password" type="password" name="password" required minlength="8" autocomplete="new-password">
    <div class="form-text">Minimal 8 karakter.</div>
  </div>
  <div class="mb-3">
    <label class="form-label" for="f-confirm">Ulangi password</label>
    <input class="form-control" id="f-confirm" type="password" name="password_confirm" required minlength="8" autocomplete="new-password">
  </div>
  <button class="btn btn-primary w-100"><i class="bi bi-person-plus"></i> Buat akun &amp; masuk</button>
</form>
<?php require __DIR__ . '/includes/auth_footer.php'; ?>
