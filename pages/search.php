<?php
// Aktifkan output buffering
ob_start();

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../koneksi.php'; // Gunakan path absolut

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan server',
    'data' => []
];

try {
    // Validasi koneksi database
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    // Sanitasi input pencarian
    $searchTerm = isset($_GET['cari']) ? trim($_GET['cari']) : '';
    $searchTerm = '%' . str_replace('%', '\\%', $conn->real_escape_string($searchTerm)) . '%';
    
    // Tangkap parameter tanggal
    $tanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : null;
    
    // Validasi format tanggal
    if ($tanggal && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        throw new Exception("Format tanggal tidak valid. Gunakan format YYYY-MM-DD");
    }

    // Query dasar dengan format tanggal Indonesia
    $sql = "SELECT 
                id,
                nim,
                nama,
                jurusan,
                filename,
                DATE_FORMAT(uploaded_at, '%d/%m/%Y %H:%i') AS formatted_date,
                filepath,
                thumbpath
            FROM mahasiswa 
            WHERE 
                (nim LIKE ? OR 
                nama LIKE ? OR 
                jurusan LIKE ?)";
    
    // Tambahkan kondisi tanggal jika ada
    if ($tanggal) {
        $sql .= " AND DATE(uploaded_at) = ?";
    }
    
    $sql .= " ORDER BY uploaded_at DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }

    // Binding parameter berdasarkan ada/tidaknya tanggal
    if ($tanggal) {
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $tanggal);
    } else {
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    // Validasi hasil query
    if ($result->num_rows > 0) {
        $response['data'] = [];
        while ($row = $result->fetch_assoc()) {
            // PERBAIKAN: Struktur data yang sesuai dengan pengecekan path
            $response['data'][] = [
                'id' => $row['id'],
                'nim' => $row['nim'],
                'nama' => $row['nama'],
                'jurusan' => $row['jurusan'],
                'filename' => $row['filename'],
                'formatted_date' => $row['formatted_date'],
                'filepath' => !empty($row['filepath']) ? $row['filepath'] : '',
                'thumbpath' => !empty($row['thumbpath']) ? $row['thumbpath'] : ''
            ];
        }
        $response['status'] = 'success';
        $response['message'] = 'Ditemukan ' . count($response['data']) . ' hasil';
    } else {
        $response['status'] = 'success';
        $response['message'] = 'Data tidak ditemukan';
        $response['data'] = [];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Kesalahan Server: ' . $e->getMessage();
} finally {
    // Bersihkan output buffer
    ob_end_clean();
    
    // Tutup koneksi
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    
    // Pastikan hanya output JSON
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit();
}