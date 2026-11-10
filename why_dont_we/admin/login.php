<?php
/* LOGIN dashboard admin — username atau email + password (dicek dengan password_verify
   terhadap admins.password_hash). Halaman ini terbuka tanpa login (lihat includes/init.php). */
require __DIR__ . '/includes/init.php';
// variabel dari includes/init.php (keterangan untuk editor, supaya tidak ditandai merah)
/** @var mysqli $conn */
/** @var array|null $current_admin */

$page_title = 'Login';

// sudah login → langsung ke dashboard
if ($current_admin) {
    redirect('index.php');
}

// belum ada akun admin sama sekali → buat akun pertama dulu
$admin_count = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM admins'))[0];
if ($admin_count == 0) {
    redirect('setup.php');
}

$error = '';
$login = '';

if (is_post()) {
    $login = post('login');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, 'SELECT id, username, password_hash FROM admins WHERE username = ? OR email = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $login, $login);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // id sesi baru setelah login, supaya id sesi lama (sebelum login) tidak bisa dipakai orang lain
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        unset($_SESSION['csrf']);
        flash('success', 'Selamat datang, ' . $admin['username'] . '.');
        redirect('index.php');
    }

    // pesan sama untuk username salah maupun password salah, dan jeda 1 detik
    // supaya menebak password berkali-kali jadi lambat
    sleep(1);
    $error = 'Username/email atau password salah.';
}

require __DIR__ . '/includes/auth_header.php';
?>
<p class="login-box-msg">Masuk ke dashboard admin</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['logged_out'])): ?><div class="alert alert-info">Anda sudah keluar.</div><?php endif; ?>

<form method="post" action="login.php">
  <?= csrf_field() ?>
  <div class="input-group mb-3">
    <input class="form-control" name="login" value="<?= e($login) ?>" placeholder="Username atau email" autocomplete="username" required autofocus>
    <div class="input-group-text"><i class="bi bi-person"></i></div>
  </div>
  <div class="input-group mb-3">
    <input class="form-control" type="password" name="password" placeholder="Password" autocomplete="current-password" required>
    <div class="input-group-text"><i class="bi bi-lock"></i></div>
  </div>
  <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
</form>
<?php require __DIR__ . '/includes/auth_footer.php'; ?>
