Sistem Informasi Akademik (SIA)
This is a web-based application for managing academic data, including student records, profile pictures, and their respective details. It provides functionalities like adding, editing, deleting, and searching student data.

Table of Contents
Features

Installation

Technologies Used

Directory Structure

Database

Usage

Features
Add and Edit Student Data (NIM, Name, Department)

Upload Profile Picture for Students

View Student List with Image Preview

Live Search for Students

Delete Student Records

Secure Admin Login

Installation
Prerequisites:
XAMPP/WAMP or any local server with PHP and MySQL support

PHP 8.0+

MySQL or MariaDB database

Steps:
Clone the Repository
Clone the project to your local machine or download the ZIP file.

bash
Copy
Edit
git clone https://github.com/yourusername/sistem-informasi-akademik.git
Setup the Database
Import the SQL dump file akademik07018.sql into your MySQL database using phpMyAdmin or command-line.

sql
Copy
Edit
CREATE DATABASE akademik07018;
Then, import the akademik07018.sql file to create the tables and insert sample data.

Configure Database Connection
Open the koneksi.php file and set the correct database credentials:

php
Copy
Edit
$host = "localhost";
$user = "root";
$pass = "";
$db = "akademik07018";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
Run the Application
Start your Apache and MySQL services in XAMPP/WAMP, then open the project folder in your browser (e.g., http://localhost/uts07018).

Technologies Used
Frontend:

HTML

CSS (Bootstrap)

JavaScript (AJAX, jQuery)

Backend:

PHP

MySQL/MariaDB

Database:

MySQL/MariaDB (Database schema: akademik07018)

Directory Structure
pgsql
Copy
Edit
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
Database
Database Name: akademik07018
Tables:
mahasiswa: Stores student records (NIM, Name, Department, Profile Picture).

admin: Stores admin credentials (Username, Password).

Example Data (for mahasiswa table):
sql
Copy
Edit
INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `jurusan`, `filename`, `filepath`, `thumbpath`, `width`, `height`, `uploaded_at`) VALUES
(23, 'A12.2023.00001', 'Satu', 'Teknik', '1748367590_6835f8e696085.jpg', '../assets/images/uploads/1748367590_6835f8e696085.jpg', '../assets/images/thumbs/thumb_1748367590_6835f8e696085.jpg', 3648, 3648, '2025-05-27 17:39:51'),
(24, 'A11.2023.00002', 'Dua', 'Kedokteran', '1748367616_6835f90000c56.jpg', '../assets/images/uploads/1748367616_6835f90000c56.jpg', '../assets/images/thumbs/thumb_1748367616_6835f90000c56.jpg', 4706, 4706, '2025-05-27 17:40:18');
Usage
Admin Login:

Go to the login page (login.php), and login using the default admin credentials:

Username: admin

Password: admin123 (hashed in the database).

Add Students:

After logging in, you can add student records from the addMhs.php page.

Upload a profile picture and enter the student details (NIM, Name, Department).

Edit and Delete Students:

You can edit or delete students' records from the list on ajaxMahasiswa.php.

Search Students:

Use the search bar to search for students by NIM, Name, or Department.

Contributions
Feel free to fork this project, contribute to its development, or suggest any improvements.

This README serves as a basic guide to using and managing the "Sistem Informasi Akademik" application.