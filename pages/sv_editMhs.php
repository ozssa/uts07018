<!-- C:\xampp\htdocs\uts07018\pages\sv_editMhs.php -->
<?php
require "../koneksi.php";

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $fotoLama = $_POST['fotolama'] ?? ''; // Foto lama yang ada di database

    // Proses gambar jika ada yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Tentukan lokasi penyimpanan file
        $targetDir = "../assets/images/uploads/";
        $thumbDir = "../assets/images/thumbs/";
        $fileName = basename($_FILES["gambar"]["name"]);
        $targetFile = $targetDir . $fileName;
        $thumbFile = $thumbDir . "thumb_" . $fileName;

        // Upload file gambar
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
            // Buat thumbnail
            createThumbnail($targetFile, $thumbFile);

            // Hapus foto lama jika ada
            if ($fotoLama && file_exists($fotoLama)) {
                unlink($fotoLama);
            }

            // Update data mahasiswa dengan foto baru
            $sql = "UPDATE mahasiswa SET nim = ?, nama = ?, jurusan = ?, filename = ?, filepath = ?, thumbpath = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nim, $nama, $jurusan, $fileName, $targetFile, $thumbFile, $id);
        } else {
            echo "Gagal mengunggah foto.";
        }
    } else {
        // Jika tidak ada file gambar yang diupload, hanya update data tanpa foto
        $sql = "UPDATE mahasiswa SET nim = ?, nama = ?, jurusan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nim, $nama, $jurusan, $id);
    }

    // Eksekusi query
    if ($stmt->execute()) {
        header("Location: ajaxMahasiswa.php"); // Redirect ke daftar mahasiswa
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Tidak ada data yang dikirim.";
}

// Fungsi untuk membuat thumbnail
function createThumbnail($source, $destination) {
    $imageInfo = getimagesize($source);
    $width = $imageInfo[0];
    $height = $imageInfo[1];

    $thumbWidth = 200;
    $thumbHeight = floor($height * ($thumbWidth / $width));

    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    $srcImage = imagecreatefromjpeg($source);

    imagecopyresampled($thumb, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
    imagejpeg($thumb, $destination, 80);

    imagedestroy($srcImage);
    imagedestroy($thumb);
}
?>
