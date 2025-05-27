<?php
// <!-- C:\xampp\htdocs\uts07018\pages\ajaxMahasiswa.php -->
session_start();
require "../koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/lib/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .search-container {
            position: relative;
            width: 300px;
        }
        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }
            .student-thumb {
                width: 50px;
                height: 50px;
            }
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . "/../includes/header.php"; ?>

    <main class="main-content">
        <div class="container py-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h3 class="mb-3 mb-md-0">Daftar Mahasiswa</h3>
                        <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                            <div class="search-container w-100">
                                <input type="text" id="liveSearch" class="form-control pe-4" 
                                    placeholder="Cari mahasiswa...">
                                <div class="search-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <a href="addMhs.php" class="btn btn-light w-100 w-md-auto">
                                <i class="fas fa-plus me-2"></i>Tambah Baru
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Foto</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data akan diisi via JavaScript -->
                            </tbody>
                        </table>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted small" id="dataCounter">
                                    Memuat data...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus data <strong><span id="deleteNama"></span></strong>?</p>
                    <p class="text-muted small">Data yang dihapus tidak dapat dipulihkan</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . "/../includes/footer.php"; ?>
    
    <script src="../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>