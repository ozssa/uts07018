<!-- C:\xampp\htdocs\uts07018\pages\editMhs.php -->
<?php
require "../koneksi.php";

$id = $_GET['kode'];
$sql = "SELECT * FROM mahasiswa WHERE id='$id'";
$qry = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($qry);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/lib/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <?php require "../includes/header.php"; ?>

    <main class="main-content">
        <div class="container">
            <h2 class="text-center mb-4">Edit Data Mahasiswa</h2>
            <form method="POST" action="sv_editMhs.php">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="form-group">
                    <label for="nim">NIM:</label>
                    <input class="form-control" type="text" name="nim" id="nim" value="<?php echo $row['nim']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input class="form-control" type="text" name="nama" id="nama" value="<?php echo $row['nama']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="jurusan">Jurusan:</label>
                    <input class="form-control" type="text" name="jurusan" id="jurusan" value="<?php echo $row['jurusan']; ?>" required>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="ajaxMahasiswa.php" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </main>

    <?php require "../includes/footer.php"; ?>
</body>
</html>
