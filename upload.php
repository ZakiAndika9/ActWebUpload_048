<?php
$target_dir = "uploads/";


if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$message = "";


if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (file_exists($target_file)) {
        $message = "<span style='color: red;'>Maaf, berkas sudah ada.</span>";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $message = "<span style='color: red;'>Maaf, berkas Anda terlalu besar (Maks 500KB).</span>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $message = "<span style='color: green;'><b>Hasil Upload:</b> Berkas <u>". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). "</u> telah berhasil diunggah!</span>";
        } else {
            $message = "<span style='color: red;'>Maaf, terjadi kesalahan saat mengunggah berkas Anda.</span>";
        }
    }
}


if (isset($_GET['delete'])) {
    $fileToDelete = $target_dir . basename($_GET['delete']);
    if (file_exists($fileToDelete) && strpos($fileToDelete, $target_dir) === 0) {
        unlink($fileToDelete);
        $message = "<span style='color: blue;'><b>Hasil Delete:</b> Berkas berhasil dihapus dari server!</span>";
    } else {
        $message = "<span style='color: red;'>Gagal menghapus berkas atau berkas tidak ditemukan.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses & Manajemen Web Upload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .alert { background: #f0f0f0; padding: 15px; border-left: 5px solid #007BFF; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-delete { color: red; text-decoration: none; font-weight: bold; }
        .btn-download { color: green; text-decoration: none; font-weight: bold; }
        .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; color: #333; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Halaman Manajemen Berkas</h2>

    <?php if (!empty($message)): ?>
        <div class="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h3>Daftar Berkas di Server (Preview & Kelola)</h3>
    <table>
        <thead>
            <tr>
                <th>Nama File</th>
                <th>Preview Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_dir($target_dir)) {
                $files = array_diff(scandir($target_dir), array('.', '..'));
                
                if (count($files) > 0) {
                    foreach ($files as $file) {
                        $filePath = $target_dir . $file;
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($file) . "</td>";
                        
                        // Kolom Preview (Syarat 2)
                        echo "<td>";
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='$filePath' width='80' alt='Preview Gambar'>";
                        } else {
                            echo "<em>Bukan berkas gambar (Preview tidak tersedia)</em>";
                        }
                        echo "</td>";
                        
                        // Kolom Aksi untuk Unduh (Syarat 5) dan Delete (Syarat 4)
                        echo "<td>
                                <a class='btn-download' href='$filePath' download>Unduh</a> | 
                                <a class='btn-delete' href='upload.php?delete=" . urlencode($file) . "' onclick='return confirm(\"Yakin ingin menghapus file ini?\")'>Hapus</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Belum ada berkas yang diunggah di dalam folder 'uploads/'.</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <a class="btn-back" href="index.html">&laquo; Kembali ke Form Utama (Sebelum Unggah)</a>

</body>
</html>