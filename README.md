Berikut adalah README yang telah diperbarui dalam bahasa Indonesia yang baik dan benar dengan URL repositori yang Anda berikan:

---

# Sistem Informasi Akademik (SIA)

Ini adalah aplikasi berbasis web untuk mengelola data akademik, termasuk data mahasiswa, foto profil, dan informasi terkait lainnya. Aplikasi ini menyediakan berbagai fitur seperti penambahan, pengeditan, penghapusan, dan pencarian data mahasiswa.

## Daftar Isi

* [Fitur](#fitur)
* [Instalasi](#instalasi)
* [Teknologi yang Digunakan](#teknologi-yang-digunakan)
* [Struktur Direktori](#struktur-direktori)
* [Database](#database)
* [Penggunaan](#penggunaan)

## Fitur

* Menambah dan Mengedit Data Mahasiswa (NIM, Nama, Jurusan)
* Mengunggah Foto Profil Mahasiswa
* Melihat Daftar Mahasiswa dengan Pratinjau Gambar
* Pencarian Mahasiswa Secara Langsung (Live Search)
* Menghapus Data Mahasiswa
* Login Admin yang Aman

## Instalasi

### Prasyarat:

* XAMPP/WAMP atau server lokal dengan dukungan PHP dan MySQL
* PHP 8.0+
* MySQL atau MariaDB

### Langkah-langkah:

1. **Clone Repositori**
   Clone proyek ini ke mesin lokal Anda atau unduh file ZIP.

   ```bash
   git clone https://github.com/ozssa/uts07018.git
   ```

2. **Setup Database**
   Impor file dump SQL `akademik07018.sql` ke dalam database MySQL menggunakan phpMyAdmin atau melalui command line.

   ```sql
   CREATE DATABASE akademik07018;
   ```

   Kemudian, impor file `akademik07018.sql` untuk membuat tabel-tabel dan memasukkan data contoh.

3. **Konfigurasi Koneksi Database**
   Buka file `koneksi.php` dan sesuaikan kredensial database Anda:

   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db = "akademik07018";

   $conn = new mysqli($host, $user, $pass, $db);

   if ($conn->connect_error) {
       die("Koneksi gagal: " . $conn->connect_error);
   }
   ```

4. **Jalankan Aplikasi**
   Jalankan layanan Apache dan MySQL di XAMPP/WAMP, lalu buka folder proyek di browser Anda (misalnya `http://localhost/uts07018`).

## Teknologi yang Digunakan

* **Frontend:**

  * HTML
  * CSS (Bootstrap)
  * JavaScript (AJAX, jQuery)
* **Backend:**

  * PHP
  * MySQL/MariaDB
* **Database:**

  * MySQL/MariaDB (Skema database: `akademik07018`)

## Struktur Direktori

```
/uts07018
│
├── assets
│   ├── css
│   │   └── style.css
│   ├── images
│   │   ├── thumbs
│   │   └── uploads
│   ├── js
│   │   └── script.js
│   └── lib
│       ├── bootstrap
│       └── jquery
├── data
│   └── akademik07018.sql
├── includes
│   ├── footer.php
│   └── header.php
├── pages
│   ├── addMhs.php
│   ├── ajaxMahasiswa.php
│   ├── editMhs.php
│   ├── hpsMhs.php
│   ├── search.php
│   ├── sv_addMhs.php
│   ├── sv_editFotoMhs.php
│   └── sv_editMhs.php
└── koneksi.php
```

## Database

### Nama Database: `akademik07018`

### Tabel:

1. **mahasiswa**: Menyimpan data mahasiswa (NIM, Nama, Jurusan, Foto Profil).
2. **admin**: Menyimpan kredensial admin (Username, Password).

### Contoh Data (untuk tabel `mahasiswa`):

```sql
INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `jurusan`, `filename`, `filepath`, `thumbpath`, `width`, `height`, `uploaded_at`) VALUES
(23, 'A12.2023.00001', 'Satu', 'Teknik', '1748367590_6835f8e696085.jpg', '../assets/images/uploads/1748367590_6835f8e696085.jpg', '../assets/images/thumbs/thumb_1748367590_6835f8e696085.jpg', 3648, 3648, '2025-05-27 17:39:51'),
(24, 'A11.2023.00002', 'Dua', 'Kedokteran', '1748367616_6835f90000c56.jpg', '../assets/images/uploads/1748367616_6835f90000c56.jpg', '../assets/images/thumbs/thumb_1748367616_6835f90000c56.jpg', 4706, 4706, '2025-05-27 17:40:18');
```

## Penggunaan

1. **Login Admin:**

   * Buka halaman login (`login.php`), dan login menggunakan kredensial admin default:

     * Username: `admin`
     * Password: `admin123` (disimpan dalam format hash di database).

2. **Menambah Mahasiswa:**

   * Setelah login, Anda dapat menambah data mahasiswa melalui halaman `addMhs.php`.
   * Unggah foto profil dan masukkan informasi mahasiswa (NIM, Nama, Jurusan).

3. **Mengedit dan Menghapus Mahasiswa:**

   * Anda dapat mengedit atau menghapus data mahasiswa dari daftar di halaman `ajaxMahasiswa.php`.

4. **Mencari Mahasiswa:**

   * Gunakan kotak pencarian untuk mencari mahasiswa berdasarkan NIM, Nama, atau Jurusan.

## Kontribusi

Silakan fork proyek ini, berkontribusi pada pengembangannya, atau memberikan saran perbaikan.

---

README ini memberikan panduan dasar untuk menggunakan dan mengelola aplikasi "Sistem Informasi Akademik".
