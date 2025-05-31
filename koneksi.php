<?php
// C:\xampp\htdocs\uts07018\koneksi.php
// PENTING: Hapus HTML comment yang ada di awal file koneksi.php lama!

$host = "localhost";
$user = "root";
$pass = "";
$db = "akademik07018";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset untuk keamanan
$conn->set_charset("utf8");
?>