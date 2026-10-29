<?php
/* ============================================================================
   FUNGSI BANTU — dipakai halaman PHP yang mengambil atau menyimpan data.
       require __DIR__ . "/../../includes/functions.php";
   ============================================================================ */

// tampilkan teks dengan aman di HTML (mencegah XSS)
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, "UTF-8");
}

// jalankan query dengan prepared statement
// kembalian: mysqli_result untuk SELECT, true untuk INSERT/UPDATE/DELETE
function db_query($conn, $sql, $types = "", $params = [])
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        http_response_code(500);
        die("Query gagal disiapkan: " . e(mysqli_error($conn)));
    }
    if ($types !== "") {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (!mysqli_stmt_execute($stmt)) {
        http_response_code(500);
        die("Query gagal dijalankan: " . e(mysqli_stmt_error($stmt)));
    }
    $result = mysqli_stmt_get_result($stmt);
    return $result === false ? true : $result;
}

// semua baris hasil query sebagai array
function db_fetch_all($result)
{
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// 65 → "$65", 49.5 → "$49.50", 1500 → "$1,500"
function money($amount)
{
    $amount = (float) $amount;
    $decimals = floor($amount) == $amount ? 0 : 2;
    return "$" . number_format($amount, $decimals);
}

// "2027-02-13 19:00:00" → "13 February 2027"
function format_date($datetime)
{
    return date("j F Y", strtotime($datetime));
}

// pesan hasil form subscribe (lihat actions/subscribe.php)
function subscribe_message($status)
{
    $messages = [
        "ok"      => "Thanks! You're on the mailing list.",
        "exists"  => "You're already on the mailing list.",
        "invalid" => "Please enter a valid email address.",
    ];
    return $messages[$status] ?? "";
}

// tampilkan pesan subscribe (kalau ada) di atas form newsletter
function show_subscribe_message()
{
    $status  = $_GET["subscribe"] ?? "";
    $message = subscribe_message($status);
    if ($message === "") {
        return;
    }
    $class = $status === "invalid" ? "form-message is-error" : "form-message";
    echo '<p class="' . $class . '" role="status">' . e($message) . "</p>";
}

// pindah ke halaman lain lalu hentikan script
function redirect($url)
{
    header("Location: " . $url);
    exit;
}
