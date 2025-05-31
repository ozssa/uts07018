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
        <div class="container py-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Edit Data Mahasiswa</h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="sv_editMhs.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="fotolama" value="<?php echo $row['filepath']; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nim" class="form-label">NIM</label>
                                    <input type="text" class="form-control" id="nim" name="nim" 
                                           value="<?php echo $row['nim']; ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?php echo $row['nama']; ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="jurusan" class="form-label">Jurusan</label>
                                    <input type="text" class="form-control" id="jurusan" name="jurusan" 
                                           value="<?php echo $row['jurusan']; ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="gambar" class="form-label">Foto Profil</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" 
                                           accept="image/jpeg, image/png">
                                    <div class="mt-3 text-center">
                                        <img src="<?php echo $row['thumbpath']; ?>" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;"
                                             alt="Foto Profil">
                                        <p class="text-muted small mt-2">Foto saat ini</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="ajaxMahasiswa.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require "../includes/footer.php"; ?>
</body>
</html>