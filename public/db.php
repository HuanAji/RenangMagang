<?php
// Koneksi statis mentah PHP untuk test alat IoT (Tanpa kerangka Laravel)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db-renangcoba";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error);
}
?>
