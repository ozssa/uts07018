<!-- C:\xampp\htdocs\uts07018\pages\sv_editFotoMhs.php -->
<?php
require "../koneksi.php";

// Cek jika file gambar diupload
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    try {
        // Ambil data dari form
        $id = $_POST['id'];
        $fotoLama = $_POST['fotolama'] ?? '';
        $thumbLama = $_POST['thumbpath'] ?? ''; // Tambahkan path thumbnail lama
        
        // Validasi file
        $validTypes = ["image/jpeg", "image/png"];
        $mimeType = mime_content_type($_FILES['gambar']['tmp_name']);
        
        if(!in_array($mimeType, $validTypes)) {
            throw new Exception("Hanya format JPG/PNG yang diperbolehkan");
        }
        
        if($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            throw new Exception("Ukuran file maksimal 2MB");
        }
        
        // Tentukan lokasi penyimpanan file
        $targetDir = "../assets/images/uploads/";
        $thumbDir = "../assets/images/thumbs/";
        
        // Buat direktori jika belum ada
        if(!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        if(!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
        
        // Generate nama file unik
        $fileExtension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
        $targetFile = $targetDir . $fileName;
        $thumbFile = $thumbDir . "thumb_" . $fileName;

        // Upload file gambar
        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
            throw new Exception("Gagal mengunggah file");
        }
        
        // Proses gambar
        $imageInfo = getimagesize($targetFile);
        if($imageInfo === false) {
            throw new Exception("File bukan gambar yang valid");
        }
        
        // Buat thumbnail
        createThumbnail($targetFile, $thumbFile, $imageInfo[2]);

        // Hapus foto lama jika ada
        if ($fotoLama && file_exists($fotoLama)) unlink($fotoLama);
        if ($thumbLama && file_exists($thumbLama)) unlink($thumbLama);

        // Update data mahasiswa dengan foto baru
        $sql = "UPDATE mahasiswa SET filename = ?, filepath = ?, thumbpath = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fileName, $targetFile, $thumbFile, $id);

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }

        // Redirect dengan pesan sukses
        $_SESSION['success'] = "Foto berhasil diperbarui!";
        header("Location: editMhs.php?kode=$id");
        exit;
        
    } catch (Exception $e) {
        // Cleanup file jika ada error
        if(isset($targetFile) && file_exists($targetFile)) unlink($targetFile);
        if(isset($thumbFile) && file_exists($thumbFile)) unlink($thumbFile);
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: editMhs.php?kode=$id");
        exit;
    }
} else {
    $_SESSION['error'] = "Tidak ada file yang dipilih atau error upload";
    header("Location: editMhs.php?kode=$id");
    exit;
}

// Fungsi untuk membuat thumbnail (mendukung JPEG dan PNG)
function createThumbnail($source, $destination, $imageType) {
    switch($imageType) {
        case IMAGETYPE_JPEG:
            $srcImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $srcImage = imagecreatefrompng($source);
            break;
        default:
            throw new Exception("Format gambar tidak didukung");
    }

    $width = imagesx($srcImage);
    $height = imagesy($srcImage);
    
    $thumbWidth = 200;
    $thumbHeight = (int)($height * ($thumbWidth / $width));
    
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Handle transparansi untuk PNG
    if($imageType == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
    }
    
    imagecopyresampled($thumb, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

    switch($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $destination, 80);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $destination, 9);
            break;
    }

    imagedestroy($srcImage);
    imagedestroy($thumb);
}
?>