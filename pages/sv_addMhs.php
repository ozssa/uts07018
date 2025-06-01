<!-- C:\xampp\htdocs\uts07018\pages\sv_addMhs.php -->
<?php
require "../koneksi.php";
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk konversi dan validasi gambar
function convertImage($source) {
    $imageInfo = getimagesize($source);
    
    // Validasi apakah file benar-benar gambar
    if($imageInfo === false) {
        throw new Exception("File bukan gambar yang valid");
    }

    // Baca gambar asli
    switch($imageInfo[2]) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($source);
            break;
        default:
            throw new Exception("Format gambar tidak didukung");
    }

    // Buat canvas baru
    $dst = imagecreatetruecolor($imageInfo[0], $imageInfo[1]);

    // Handle transparansi untuk PNG
    if($imageInfo[2] == IMAGETYPE_PNG) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $imageInfo[0], $imageInfo[1], $transparent);
    }

    // Salin gambar ke canvas baru
    imagecopy($dst, $src, 0, 0, 0, 0, $imageInfo[0], $imageInfo[1]);

    // Simpan ulang gambar dengan kualitas tinggi
    switch($imageInfo[2]) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $source, 90); // Timpa file asli
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $source, 9); // Timpa file asli
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);

    // Verifikasi ulang dimensi
    $verifiedInfo = getimagesize($source);
    if($verifiedInfo[0] == 0 || $verifiedInfo[1] == 0) {
        throw new Exception("Gagal memverifikasi dimensi gambar");
    }

    return $verifiedInfo;
}

function createThumbnail($source, $destination) {
    $imageInfo = getimagesize($source);
    
    switch($imageInfo[2]) {
        case IMAGETYPE_JPEG:
            $srcImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $srcImage = imagecreatefrompng($source);
            break;
        default:
            throw new Exception("Format gambar tidak didukung");
    }

    $thumbWidth = 200;
    $thumbHeight = (int)($imageInfo[1] * ($thumbWidth / $imageInfo[0]));
    
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Handle transparansi untuk PNG
    if($imageInfo[2] == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
    }
    
    imagecopyresampled($thumb, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imageInfo[0], $imageInfo[1]);

    switch($imageInfo[2]) {
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validasi input
        $requiredFields = ['nim', 'nama', 'jurusan'];
        foreach($requiredFields as $field) {
            if(empty($_POST[$field])) {
                throw new Exception(ucfirst($field) . " tidak boleh kosong");
            }
        }

        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $jurusan = $_POST['jurusan'];
        $fileName = null;
        $targetFile = null;
        $thumbFile = null;
        $width = 0;
        $height = 0;

        // Handle file upload
        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $targetDir = "../assets/images/uploads/";
            $thumbDir = "../assets/images/thumbs/";
            
            // Buat direktori jika belum ada
            if(!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                throw new Exception("Gagal membuat direktori upload");
            }
            if(!is_dir($thumbDir) && !mkdir($thumbDir, 0755, true)) {
                throw new Exception("Gagal membuat direktori thumbnail");
            }

            $originalName = basename($_FILES["gambar"]["name"]);
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetFile = "assets/images/uploads/" . $fileName;
            $thumbFile = "assets/images/thumbs/thumb_" . $fileName;

            // Validasi file
            $validTypes = ["image/jpeg", "image/png"];
            $mimeType = mime_content_type($_FILES['gambar']['tmp_name']);
            
            if(!in_array($mimeType, $validTypes)) {
                throw new Exception("Hanya format JPG/PNG yang diperbolehkan");
            }

            if($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
                throw new Exception("Ukuran file maksimal 2MB");
            }

            if(!move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
                throw new Exception("Gagal mengupload file");
            }

            try {
                // Proses konversi dan validasi gambar
                $imageInfo = convertImage($targetFile);
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                
                // Buat thumbnail
                createThumbnail($targetFile, $thumbFile);
                
            } catch(Exception $e) {
                // Cleanup file yang gagal diproses
                if(file_exists($targetFile)) unlink($targetFile);
                if(file_exists($thumbFile)) unlink($thumbFile);
                throw $e;
            }
        }

        // Insert ke database
        $sql = "INSERT INTO mahasiswa (
            nim, 
            nama, 
            jurusan, 
            filename, 
            filepath, 
            thumbpath, 
            width, 
            height, 
            uploaded_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $currentDateTime = date('Y-m-d H:i:s');
        
        $stmt->bind_param(
            "ssssssiss", 
            $nim, 
            $nama, 
            $jurusan, 
            $fileName, 
            $targetFile, 
            $thumbFile,
            $width,
            $height,
            $currentDateTime
        );

        if(!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }

        header("Location: ajaxMahasiswa.php?status=success");
        exit();
        
    } catch (Exception $e) {
        // Cleanup file jika ada error
        if(isset($targetFile) && file_exists($targetFile)) unlink($targetFile);
        if(isset($thumbFile) && file_exists($thumbFile)) unlink($thumbFile);
        
        $errorMessage = urlencode($e->getMessage());
        header("Location: addMhs.php?status=error&message=$errorMessage");
        exit();
    }
}
?>