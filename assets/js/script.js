// C:\xampp\htdocs\uts07018\assets\js\script.js
document.addEventListener('DOMContentLoaded', function() {
    // ====================== HANDLER UNTUK HALAMAN FORM (ADD/EDIT) ======================
    const form = document.getElementById('mahasiswaForm');
    if(form) {
        const nimInput = document.getElementById('nim');
        const nimError = document.getElementById('nimError');
        const fileInput = document.getElementById('gambar');
        const previewImg = document.getElementById('new-img-preview');
        const defaultPreview = '../assets/images/thumbs/default.jpg';

        // Validasi NIM
        const validateNIM = () => {
            const isValid = /^A\d{2}\.\d{4}\.\d{5}$/.test(nimInput.value);
            nimInput.classList.toggle('is-invalid', !isValid);
            nimError.textContent = isValid ? '' : 'Format: A12.2023.07000';
            return isValid;
        }

        // Preview Gambar
        const handleImagePreview = (file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewImg.classList.add('loaded');
                previewImg.style.opacity = '0';
                setTimeout(() => previewImg.style.opacity = '1', 100);
            }
            reader.readAsDataURL(file);
        }

        // Validasi File
        const validateFile = (file) => {
            const validTypes = ['image/jpeg', 'image/png'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if(!validTypes.includes(file.type)) {
                showError('Hanya format JPG/PNG yang diizinkan');
                return false;
            }
            
            if(file.size > maxSize) {
                showError('Ukuran file maksimal 2MB');
                return false;
            }
            
            return true;
        }

        // Event Listeners
        nimInput.addEventListener('input', validateNIM);

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file && validateFile(file)) {
                handleImagePreview(file);
            } else {
                previewImg.src = defaultPreview;
                previewImg.classList.remove('loaded');
                this.value = '';
            }
        });

        form.addEventListener('submit', function(e) {
            if(!validateNIM()) {
                e.preventDefault();
                nimInput.focus();
            }
        });
    }

    // ====================== HANDLER UNTUK HALAMAN PENCARIAN (ajaxMahasiswa) ======================
    const liveSearch = document.getElementById('liveSearch');
    if(liveSearch) {
        // Variabel kontrol
        let isLoading = false;
        let searchTimer;
        
        // Elemen UI
        const searchDate = document.getElementById('searchDate');
        const resetDate = document.getElementById('resetDate');
        const tableBody = document.getElementById('tableBody');
        const dataCounter = document.getElementById('dataCounter');

        // Fungsi untuk memuat data mahasiswa
        const loadMahasiswaData = (keyword = '', date = '') => {
            if (isLoading) return;
            
            isLoading = true;
            
            // Tampilkan loading spinner
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="d-flex justify-content-center align-items-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-3">Memuat data...</span>
                        </div>
                    </td>
                </tr>
            `;
            
            dataCounter.textContent = 'Memuat data...';
            
            // Kirim request ke server
            fetch('../pages/search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `cari=${encodeURIComponent(keyword)}&tanggal=${encodeURIComponent(date)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') throw new Error(data.message);
                renderMahasiswaTable(data);
                dataCounter.textContent = data.data.length > 0 
                    ? `Menampilkan ${data.data.length} data` 
                    : 'Tidak ada data ditemukan';
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Terjadi kesalahan: ${error.message}</p>
                            <button class="btn btn-sm btn-outline-primary mt-2" 
                                onclick="retrySearch()">
                                <i class="fas fa-sync me-1"></i> Coba Lagi
                            </button>
                        </td>
                    </tr>
                `;
                dataCounter.textContent = 'Gagal memuat data';
            })
            .finally(() => {
                isLoading = false;
            });
        }

        // Fungsi untuk render tabel - PERBAIKAN
        const renderMahasiswaTable = (data) => {
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search-minus fa-2x mb-3"></i>
                                <p>Data tidak ditemukan</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.data.forEach(row => {
                // Gunakan properti yang benar
                const thumbpath = row.thumbpath || '';
                const filepath = row.filepath || '';
                const formatted_date = row.formatted_date || row.tanggal_tampil || '-';
                
                html += `
                    <tr>
                        <td>
                            ${thumbpath ? 
                                `<img src="${thumbpath}" 
                                    class="student-thumb" 
                                    alt="${row.nama}"
                                    data-full="${filepath}">` : 
                                `<div class="bg-light d-flex align-items-center justify-content-center" 
                                    style="width:50px; height:50px; border-radius:4px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>`}
                        </td>
                        <td>${row.nim}</td>
                        <td>${row.nama}</td>
                        <td>${row.jurusan}</td>
                        <td>${formatted_date}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="editMhs.php?kode=${row.id}" 
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="tooltip"
                                title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-id="${row.id}"
                                        data-nama="${row.nama}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
            initComponents();
        }

        // Fungsi untuk inisialisasi komponen
        const initComponents = () => {
            // Preview Gambar
            document.querySelectorAll('.student-thumb').forEach(img => {
                img.addEventListener('click', function() {
                    document.getElementById('modalImage').src = this.dataset.full;
                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                });
            });

            // Delete Modal
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nama = button.getAttribute('data-nama');
                    
                    document.getElementById('deleteNama').textContent = nama;
                    document.getElementById('confirmDelete').href = `hpsMhs.php?kode=${id}`;
                });
            }

            // Tooltip
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
                new bootstrap.Tooltip(tooltipTriggerEl)
            );
        }

        // Fungsi untuk debounce pencarian
        const debounceSearch = (func, delay) => {
            let timer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(context, args), delay);
            };
        }

        // Fungsi retry untuk UI
        window.retrySearch = () => {
            const keyword = liveSearch.value.trim();
            const date = searchDate ? searchDate.value : '';
            loadMahasiswaData(keyword, date);
        }

        // Event handler untuk input pencarian
        const handleSearchInput = debounceSearch(() => {
            const keyword = liveSearch.value.trim();
            const date = searchDate ? searchDate.value : '';
            loadMahasiswaData(keyword, date);
        }, 300);

        // Event handler untuk tanggal
        const handleDateChange = () => {
            const keyword = liveSearch.value.trim();
            const date = searchDate ? searchDate.value : '';
            loadMahasiswaData(keyword, date);
        }

        // Event handler reset tanggal
        const handleResetDate = () => {
            if (searchDate) searchDate.value = '';
            const keyword = liveSearch.value.trim();
            loadMahasiswaData(keyword, '');
        }

        // Setup event listeners
        liveSearch.addEventListener('input', handleSearchInput);
        
        if (searchDate) {
            searchDate.addEventListener('change', handleDateChange);
        }
        
        if (resetDate) {
            resetDate.addEventListener('click', handleResetDate);
        }

        // Load data awal
        loadMahasiswaData();
        initComponents();
    }

    // ====================== FUNGSI GLOBAL ======================
    window.showError = function(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger mt-3';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            ${message}
        `;
        const container = document.querySelector('.container') || document.body;
        container.prepend(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }
});