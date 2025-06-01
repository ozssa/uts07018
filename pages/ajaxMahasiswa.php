<?php
// C:\xampp\htdocs\uts07018\pages\ajaxMahasiswa.php
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
            width: 100%;
            max-width: 300px;
        }
        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        
        .student-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .student-thumb:hover {
            transform: scale(1.1);
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
            .action-buttons .btn {
                margin-right: 3px;
                margin-bottom: 3px;
                font-size: 0.7rem;
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

                    <!-- Date Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="date" class="form-control" id="searchDate">
                                <button class="btn btn-outline-secondary" type="button" id="resetDate">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="form-text">Filter berdasarkan tanggal upload</div>
                        </div>
                    </div>

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
                                <tr id="loadingRow">
                                    <td colspan="6" class="text-center">
                                        <div class="d-flex justify-content-center align-items-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="ms-3">Memuat data mahasiswa...</span>
                                        </div>
                                    </td>
                                </tr>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    
    <script>
    // Fungsi untuk memuat data mahasiswa dengan error handling yang lebih baik
    function loadMahasiswaData(keyword = '', date = '') {
        const tableBody = document.getElementById('tableBody');
        const dataCounter = document.getElementById('dataCounter');
        
        // Tampilkan loading spinner
        tableBody.innerHTML = `
            <tr id="loadingRow">
                <td colspan="6" class="text-center">
                    <div class="d-flex justify-content-center align-items-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-3">Memuat data mahasiswa...</span>
                    </div>
                </td>
            </tr>
        `;
        
        dataCounter.textContent = 'Memuat data...';
        
        // Kirim permintaan AJAX ke server
        fetch('ajaxLoadMahasiswa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `keyword=${encodeURIComponent(keyword)}&date=${encodeURIComponent(date)}`
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            // Cek jika response berhasil (status 200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Ambil response sebagai text dulu untuk debugging
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text.substring(0, 200));
            
            // Coba parse sebagai JSON
            try {
                const data = JSON.parse(text);
                renderMahasiswaTable(data);
                dataCounter.textContent = `Menampilkan ${data.length} data mahasiswa`;
            } catch (jsonError) {
                console.error('JSON Parse Error:', jsonError);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <div>
                            <h5>Terjadi kesalahan saat memuat data</h5>
                            <p class="text-muted">${error.message}</p>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMahasiswaData('${keyword}', '${date}')">
                                <i class="fas fa-sync me-1"></i> Coba Lagi
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            dataCounter.textContent = 'Gagal memuat data';
        });
    }
    
    // Fungsi untuk merender tabel mahasiswa
    function renderMahasiswaTable(data) {
        const tableBody = document.getElementById('tableBody');
        
        if (!Array.isArray(data)) {
            console.error('Data is not an array:', data);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Format data tidak valid</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        if (data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state py-5">
                            <i class="fas fa-user-graduate"></i>
                            <h4 class="text-muted mb-2">Data tidak ditemukan</h4>
                            <p class="text-muted">Coba gunakan kata kunci lain atau reset filter</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        data.forEach(mahasiswa => {
            // Pastikan semua properti ada dengan fallback
            const nim = mahasiswa.nim || '-';
            const nama = mahasiswa.nama || '-';
            const jurusan = mahasiswa.jurusan || '-';
            const tanggalTampil = mahasiswa.formatted_date || '-'; // Gunakan formatted_date dari server
            const id = mahasiswa.id || '';
            const thumbpath = mahasiswa.thumbpath || '';
            const filepath = mahasiswa.filepath || '';
            
            html += `
                <tr>
                    <td>
                        ${thumbpath ? 
                            `<img src="${thumbpath}" alt="${nama}" 
                                class="student-thumb" data-bs-toggle="modal" 
                                data-bs-target="#imageModal" 
                                data-img-src="${filepath}"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="bg-light d-none align-items-center justify-content-center" 
                                style="width:50px; height:50px; border-radius:4px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>` : 
                            `<div class="bg-light d-flex align-items-center justify-content-center" 
                                style="width:50px; height:50px; border-radius:4px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>`}
                    </td>
                    <td>${nim}</td>
                    <td>${nama}</td>
                    <td>${jurusan}</td>
                    <td>${tanggalTampil}</td>
                    <td class="action-buttons">
                        <a href="editMhs.php?id=${id}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" 
                            data-id="${id}" 
                            data-nama="${nama}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
        
        // Setup event listeners untuk tombol hapus
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                
                document.getElementById('deleteNama').textContent = nama;
                document.getElementById('confirmDelete').href = `deleteMhs.php?id=${id}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
        
        // Setup event listeners untuk preview gambar
        document.querySelectorAll('.student-thumb').forEach(img => {
            img.addEventListener('click', function() {
                document.getElementById('modalImage').src = this.getAttribute('data-img-src');
            });
        });
    }
    
    // Inisialisasi aplikasi
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        const searchDate = document.getElementById('searchDate');
        const resetDate = document.getElementById('resetDate');
        let searchTimer;
        
        // Load data awal
        loadMahasiswaData();
        
        // Live search functionality dengan debounce
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                const keyword = this.value.trim();
                const date = searchDate ? searchDate.value : '';
                
                searchTimer = setTimeout(() => {
                    loadMahasiswaData(keyword, date);
                }, 500);
            });
        }
        
        // Reset date filter
        if (resetDate) {
            resetDate.addEventListener('click', function() {
                if (searchDate) {
                    searchDate.value = '';
                }
                const keyword = searchInput ? searchInput.value.trim() : '';
                loadMahasiswaData(keyword, '');
            });
        }
        
        // Trigger search when date changes
        if (searchDate) {
            searchDate.addEventListener('change', function() {
                const keyword = searchInput ? searchInput.value.trim() : '';
                loadMahasiswaData(keyword, this.value);
            });
        }
        
        // Handle image modal
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (button && button.hasAttribute('data-img-src')) {
                    const imageUrl = button.getAttribute('data-img-src');
                    const modalImage = document.getElementById('modalImage');
                    if (modalImage) {
                        modalImage.src = imageUrl;
                    }
                }
            });
        }
    });
    </script>
</body>
</html>