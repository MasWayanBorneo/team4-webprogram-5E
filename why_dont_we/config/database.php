<?php
/* ============================================================================
   DATABASE CONNECTION (mysqli, procedural style)
   Include this file in any PHP page or API endpoint that needs MySQL; it
   creates the connection in $conn:

       require __DIR__ . '/../config/database.php';

       $result = mysqli_query($conn, "SELECT * FROM products");
       while ($row = mysqli_fetch_assoc($result)) {
           echo $row['name'];
       }

   For values that come from a form or the URL, use a prepared statement so
   the input can never change the SQL:

       $stmt = mysqli_prepare($conn, "SELECT * FROM members WHERE slug = ?");
       mysqli_stmt_bind_param($stmt, "s", $slug);
       mysqli_stmt_execute($stmt);
       $member = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

   The values below match a default XAMPP install (user "root", no password).
   ============================================================================ */

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "wdw_db";

// report errors through return values, so the checks below can handle them
mysqli_report(MYSQLI_REPORT_OFF);

// "@" hides PHP's raw warning; the message below explains the failure instead
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    http_response_code(500);
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// utf8mb4 so names and paths with special characters (e.g. "—") come back intact
mysqli_set_charset($conn, "utf8mb4");
