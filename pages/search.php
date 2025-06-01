<?php
// Aktifkan output buffering untuk menghindari masalah header
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

    // Ambil parameter pencarian (GET/POST compatible)
    $searchTerm = isset($_REQUEST['cari']) ? trim($_REQUEST['cari']) : '';
    $tanggal = isset($_REQUEST['tanggal']) ? trim($_REQUEST['tanggal']) : '';

    // Validasi format tanggal jika ada
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
            WHERE 1=1";

    // Tambahkan kondisi pencarian jika ada keyword
    $params = [];
    $types = '';

    if (!empty($searchTerm)) {
        $sql .= " AND (nim LIKE ? OR nama LIKE ? OR jurusan LIKE ?)";
        $searchParam = '%' . $searchTerm . '%';
        $types .= 'sss';
        $params = array_fill(0, 3, $searchParam);
    }

    // Tambahkan filter tanggal jika ada
    if (!empty($tanggal)) {
        $sql .= " AND DATE(uploaded_at) = ?";
        $types .= 's';
        $params[] = $tanggal;
    }

    $sql .= " ORDER BY uploaded_at DESC";

    // Persiapkan statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }

    // Binding parameter dinamis
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    // Proses hasil query
    if ($result->num_rows > 0) {
        $response['data'] = [];
        while ($row = $result->fetch_assoc()) {
            $response['data'][] = [
                'id' => $row['id'],
                'nim' => $row['nim'],
                'nama' => $row['nama'],
                'jurusan' => $row['jurusan'],
                'filename' => $row['filename'],
                'formatted_date' => $row['formatted_date'],
                'filepath' => $row['filepath'] ?? '',
                'thumbpath' => $row['thumbpath'] ?? ''
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