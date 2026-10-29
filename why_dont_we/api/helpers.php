<?php
/* ============================================================================
   FUNGSI BANTU API — di-require oleh setiap file di folder api/.
   Membuka koneksi database ($conn) dan menyiapkan respons JSON.
   ============================================================================ */

require __DIR__ . "/../config/database.php";

header("Content-Type: application/json; charset=utf-8");

// kirim data sebagai JSON lalu hentikan script
function send_json($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// kirim pesan error dalam format { "error": "..." }
function send_error($message, $status)
{
    send_json(["error" => $message], $status);
}

// jalankan prepared statement dan kembalikan hasilnya (mysqli_result atau true)
function run_query($conn, $sql, $types = "", $params = [])
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        send_error("Query gagal disiapkan.", 500);
    }
    if ($types !== "") {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (!mysqli_stmt_execute($stmt)) {
        send_error("Query gagal dijalankan.", 500);
    }
    $result = mysqli_stmt_get_result($stmt);
    return $result === false ? true : $result;
}
