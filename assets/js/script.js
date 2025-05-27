// C:\xampp\htdocs\uts07018\assets\js\script.js
document.addEventListener('DOMContentLoaded', function() {
    // Handler untuk halaman form (add/edit)
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

    // Handler untuk halaman pencarian (ajaxMahasiswa)
    const liveSearch = document.getElementById('liveSearch');
    if(liveSearch) {
        let searchTimeout;
        let controller = new AbortController();

        const showLoading = () => {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-primary">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        const updateTable = (data) => {
            const tbody = document.getElementById('tableBody');
            const counter = document.getElementById('dataCounter');
            
            tbody.innerHTML = '';
            
            if(data.status === 'success' && data.data.length > 0) {
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <img src="${row.thumbpath}" 
                                class="student-thumb" 
                                alt="${row.nama}"
                                data-full="${row.filepath}">
                        </td>
                        <td>${row.nim}</td>
                        <td>${row.nama}</td>
                        <td>${row.jurusan}</td>
                        <td>${row.formatted_date}</td>
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
                    `;
                    tbody.appendChild(tr);
                });
                
                counter.innerHTML = `Menampilkan ${data.data.length} data`;
                initComponents();
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search-minus fa-2x mb-3"></i>
                                <p>Data tidak ditemukan</p>
                            </div>
                        </td>
                    </tr>
                `;
                counter.innerHTML = `Menampilkan 0 data`;
            }
        }

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
            deleteModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                
                document.getElementById('deleteNama').textContent = nama;
                document.getElementById('confirmDelete').href = `hpsMhs.php?kode=${id}`;
            });

            // Tooltip
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
                new bootstrap.Tooltip(tooltipTriggerEl)
            );
        }

        const handleSearch = (searchTerm) => {
            controller.abort();
            controller = new AbortController();

            const apiUrl = new URL('../pages/search.php', window.location.href);
            apiUrl.searchParams.append('cari', searchTerm);

            showLoading();

            fetch(apiUrl, { 
                signal: controller.signal 
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}. Response: ${text}`);
                    });
                }
                return response.json().catch(() => {
                    throw new Error('Respon server tidak valid');
                });
            })
            .then(data => {
                if(data.status === 'error') throw new Error(data.message);
                if(!Array.isArray(data.data)) throw new Error('Format data tidak valid');
                updateTable(data);
            })
            .catch(error => {
                if(error.name === 'AbortError') return;
                console.error('Error:', error);
                showError(error.message);
                document.getElementById('dataCounter').textContent = 'Gagal memuat data';
            });
        }

        liveSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                handleSearch(this.value.trim());
            }, 300);
        });

        // Load data awal
        const loadInitialData = () => {
            fetch('../pages/search.php?cari=')
                .then(response => {
                    if(!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json().catch(() => {
                        throw new Error('Respon server tidak valid');
                    });
                })
                .then(data => {
                    if(data.status === 'error') throw new Error(data.message);
                    if(!Array.isArray(data.data)) throw new Error('Format data tidak valid');
                    updateTable(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError(error.message);
                    document.getElementById('dataCounter').textContent = 'Gagal memuat data awal';
                });
        }

        loadInitialData();
        initComponents();
    }

    // Fungsi global untuk menampilkan error
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