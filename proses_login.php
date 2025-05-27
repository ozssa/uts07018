<!-- C:\xampp\htdocs\uts07018\proses_login.php -->
<?php
session_start();
require "koneksi.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Validasi input
        if(empty($username) || empty($password)) {
            throw new Exception("Username dan password wajib diisi");
        }

        // Cari user di database
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 0) {
            throw new Exception("Username tidak ditemukan");
        }

        $admin = $result->fetch_assoc();

        // Verifikasi password
        if(!password_verify($password, $admin['password'])) {
            throw new Exception("Password salah");
        }

        // Set session
        $_SESSION['admin'] = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'login_time' => time()
        ];

        // Redirect ke halaman admin
        header("Location: pages/ajaxMahasiswa.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>