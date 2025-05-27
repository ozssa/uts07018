<!-- C:\xampp\htdocs\uts07018\koneksi.php -->
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "akademik07018";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
