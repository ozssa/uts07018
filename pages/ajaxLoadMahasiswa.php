<?php
// C:\xampp\htdocs\uts07018\pages\ajaxLoadMahasiswa.php
session_start();

// Nonaktifkan output buffering dan error display untuk AJAX
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Set header untuk JSON response
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Cek apakah request adalah AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Include koneksi database
    $koneksi_file = realpath(dirname(__FILE__) . "/../koneksi.php");
    
    if (!$koneksi_file || !file_exists($koneksi_file)) {
        throw new Exception("Database connection file not found. Expected path: " . 
            dirname(__FILE__) . "/../koneksi.php");
    }
    
    require_once $koneksi_file;
    
    // Cek berbagai kemungkinan nama variabel koneksi
    $db_connection = null;
    
    if (isset($koneksi) && $koneksi instanceof mysqli) {
        $db_connection = $koneksi;
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $db_connection = $conn;
    } elseif (isset($connection) && $connection instanceof mysqli) {
        $db_connection = $connection;
    } elseif (isset($mysqli) && $mysqli instanceof mysqli) {
        $db_connection = $mysqli;
    } else {
        // Jika tidak ada variabel koneksi yang ditemukan, buat koneksi baru
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "uts07018"; // Ganti dengan nama database Anda
        
        $db_connection = new mysqli($host, $username, $password, $database);
        
        if ($db_connection->connect_error) {
            throw new Exception("Connection failed: " . $db_connection->connect_error);
        }
        
        // Set charset untuk keamanan
        $db_connection->set_charset("utf8");
    }
    
    // Test koneksi
    if ($db_connection->connect_error) {
        throw new Exception("Database connection error: " . $db_connection->connect_error);
    }
    
    // Ambil parameter dari POST
    $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    
    // Cek apakah tabel mahasiswa ada
    $check_table = $db_connection->query("SHOW TABLES LIKE 'mahasiswa'");
    if ($check_table->num_rows == 0) {
        throw new Exception("Table 'mahasiswa' does not exist in database");
    }
    
    // Cek struktur tabel untuk memastikan kolom ada
    $check_columns = $db_connection->query("DESCRIBE mahasiswa");
    $existing_columns = [];
    while ($col = $check_columns->fetch_assoc()) {
        $existing_columns[] = $col['Field'];
    }
    
    // Buat query berdasarkan kolom yang tersedia
    $select_fields = [];
    $required_fields = ['id', 'nim', 'nama', 'jurusan', 'foto', 'tanggal_upload'];
    
    // PERBAIKAN 1: Format tanggal konsisten (dd/mm/yyyy hh:mm)
    foreach ($required_fields as $field) {
        if (in_array($field, $existing_columns)) {
            if ($field === 'tanggal_upload') {
                $select_fields[] = "tanggal_upload";
                $select_fields[] = "DATE_FORMAT(tanggal_upload, '%d/%m/%Y %H:%i') as tanggal_tampil";
            } else {
                $select_fields[] = $field;
            }
        } else {
            if ($field === 'tanggal_upload') {
                $select_fields[] = "NOW() as tanggal_upload";
                $select_fields[] = "DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i') as tanggal_tampil";
            } else {
                $select_fields[] = "'' as " . $field;
            }
        }
    }
    
    $sql = "SELECT " . implode(', ', $select_fields) . " FROM mahasiswa WHERE 1=1";
    
    $params = array();
    $types = "";
    
    // Tambahkan filter keyword jika ada
    if (!empty($keyword)) {
        $keyword_conditions = [];
        
        if (in_array('nim', $existing_columns)) {
            $keyword_conditions[] = "nim LIKE ?";
            $params[] = "%{$keyword}%";
            $types .= "s";
        }
        
        if (in_array('nama', $existing_columns)) {
            $keyword_conditions[] = "nama LIKE ?";
            $params[] = "%{$keyword}%";
            $types .= "s";
        }
        
        if (in_array('jurusan', $existing_columns)) {
            $keyword_conditions[] = "jurusan LIKE ?";
            $params[] = "%{$keyword}%";
            $types .= "s";
        }
        
        if (!empty($keyword_conditions)) {
            $sql .= " AND (" . implode(' OR ', $keyword_conditions) . ")";
        }
    }
    
    // Tambahkan filter tanggal jika ada dan kolom tanggal_upload tersedia
    if (!empty($date) && in_array('tanggal_upload', $existing_columns)) {
        $sql .= " AND DATE(tanggal_upload) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    // Urutkan berdasarkan tanggal upload terbaru jika kolom tersedia
    if (in_array('tanggal_upload', $existing_columns)) {
        $sql .= " ORDER BY tanggal_upload DESC";
    } elseif (in_array('id', $existing_columns)) {
        $sql .= " ORDER BY id DESC";
    }
    
    // Prepare statement
    $stmt = $db_connection->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $db_connection->error . ". SQL: " . $sql);
    }
    
    // Bind parameters jika ada
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Ambil hasil
    $result = $stmt->get_result();
    $data = array();
    
    // PERBAIKAN 2: Penanganan format tanggal di hasil query
    while ($row = $result->fetch_assoc()) {
        // Format tanggal dengan konsisten - prioritaskan tanggal_tampil dari query
        $tanggal_tampil = '';
        if (isset($row['tanggal_tampil']) && !empty($row['tanggal_tampil'])) {
            $tanggal_tampil = $row['tanggal_tampil'];
        } elseif (isset($row['tanggal_upload']) && !empty($row['tanggal_upload'])) {
            // Fallback jika tanggal_tampil tidak ada
            $timestamp = strtotime($row['tanggal_upload']);
            if ($timestamp !== false) {
                $tanggal_tampil = date('d/m/Y H:i', $timestamp);
            }
        }
        
        // Escape HTML untuk keamanan
        $data[] = array(
            'id' => isset($row['id']) ? (int)$row['id'] : 0,
            'nim' => htmlspecialchars($row['nim'] ?? '', ENT_QUOTES, 'UTF-8'),
            'nama' => htmlspecialchars($row['nama'] ?? '', ENT_QUOTES, 'UTF-8'),
            'jurusan' => htmlspecialchars($row['jurusan'] ?? '', ENT_QUOTES, 'UTF-8'),
            'foto' => $row['foto'] ? htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') : null,
            'tanggal_upload' => htmlspecialchars($row['tanggal_upload'] ?? '', ENT_QUOTES, 'UTF-8'),
            'tanggal_tampil' => htmlspecialchars($tanggal_tampil, ENT_QUOTES, 'UTF-8')
        );
    }
    
    $stmt->close();
    
    // Kirim response JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Log error untuk debugging
    $error_message = "AJAX Load Mahasiswa Error: " . $e->getMessage() . 
                    " in " . $e->getFile() . " on line " . $e->getLine();
    error_log($error_message);
    
    // Kirim error response
    http_response_code(500);
    $error_response = [
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ];
    
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
    
} finally {
    // Tutup koneksi jika ada
    if (isset($db_connection) && $db_connection instanceof mysqli) {
        $db_connection->close();
    }
}
?>