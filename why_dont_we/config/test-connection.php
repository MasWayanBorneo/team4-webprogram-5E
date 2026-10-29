<?php
/* ============================================================================
   TES KONEKSI — buka http://localhost/team4-webdesign-4E/why_dont_we/config/test-connection.php
   Menampilkan semua tabel di wdw_db beserta jumlah barisnya. Hapus file ini
   setelah situs tersambung ke database; file ini hanya untuk pengecekan.
   ============================================================================ */

require __DIR__ . "/database.php";

header("Content-Type: text/html; charset=utf-8");

$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $count_result = mysqli_query($conn, "SELECT COUNT(*) FROM `" . $row[0] . "`");
    $tables[$row[0]] = mysqli_fetch_row($count_result)[0];
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tes koneksi wdw_db</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; color: #010912; }
        table { border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: .4rem .8rem; text-align: left; }
        .ok { color: #0b5d1e; font-weight: bold; }
    </style>
</head>
<body>
    <p class="ok">Terhubung ke database <?= htmlspecialchars($db_name) ?> (<?= htmlspecialchars(mysqli_get_server_info($conn)) ?>)</p>
    <table>
        <tr><th>Tabel</th><th>Jumlah baris</th></tr>
        <?php foreach ($tables as $name => $count): ?>
            <tr>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= (int) $count ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php mysqli_close($conn); ?>
