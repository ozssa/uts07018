<!-- C:\xampp\htdocs\uts07018\pages\sv_editFotoMhs.php -->
<?php
require "../koneksi.php";

// Cek jika file gambar diupload
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    // Ambil data dari form
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $fotoLama = $_POST['fotolama'] ?? ''; // Foto lama yang ada di database

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
        $sql = "UPDATE mahasiswa SET filename = ?, filepath = ?, thumbpath = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fileName, $targetFile, $thumbFile, $id);

        if ($stmt->execute()) {
            echo "Foto berhasil diperbarui!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Gagal mengunggah foto.";
    }
} else {
    echo "Tidak ada file yang dipilih.";
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
