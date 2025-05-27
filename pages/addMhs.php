<!-- C:\xampp\htdocs\uts07018\pages\addMhs.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa Baru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/lib/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <?php 
    session_start();
    require "../includes/header.php";
    ?>

    <main class="main-content">
        <div class="container py-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Tambah Data Mahasiswa</h3>
                </div>
                
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); endif; ?>

                    <form id="mahasiswaForm" method="POST" enctype="multipart/form-data" action="sv_addMhs.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nim" class="form-label">NIM</label>
                                    <input type="text" class="form-control" id="nim" name="nim" 
                                           placeholder="A12.2023.07000" required
                                           pattern="^A\d{2}\.\d{4}\.\d{5}$">
                                    <div id="nimError" class="form-text text-danger"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="jurusan" class="form-label">Jurusan</label>
                                    <input type="text" class="form-control" id="jurusan" name="jurusan" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="gambar" class="form-label">Foto Profil</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" 
                                           accept="image/jpeg, image/png">
                                    <div class="preview-container mt-3 text-center">
                                        <img id="new-img-preview" class="img-preview" 
                                             src="../assets/images/thumbs/depositphotos_531920820-stock-illustration-photo-available-vector-icon-default.jpg" 
                                             alt="Preview Foto">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="ajaxMahasiswa.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="ajaxResponse" class="mt-3"></div>
                </div>
            </div>
        </div>
    </main>

    <?php require "../includes/footer.php"; ?>
    
    <script src="../assets/lib/jquery/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>