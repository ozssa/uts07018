<!-- C:\xampp\htdocs\uts07018\pages\hpsMhs.php -->
<?php
require "../koneksi.php";

// Ambil id yang akan dihapus
$id = $_GET['kode'];

// Ambil data mahasiswa berdasarkan id
$sql = "SELECT * FROM mahasiswa WHERE id='$id'";
$query = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($query);

// Hapus file foto (thumb dan file asli)
$thumbpath = $row['thumbpath'];
$filepath = $row['filepath'];

if (!empty($thumbpath) && file_exists($thumbpath)) {
    unlink($thumbpath);
}

if (!empty($filepath) && file_exists($filepath)) {
    unlink($filepath);
}

// Hapus data mahasiswa dari database
$sqlDelete = "DELETE FROM mahasiswa WHERE id='$id'";
if (mysqli_query($conn, $sqlDelete)) {
    header("Location: ajaxMahasiswa.php"); // Redirect ke daftar mahasiswa
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
