<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nisSiswa = $_SESSION['nis'];
$namaSiswa = $_SESSION['nama_siswa'];
$pesanError = '';
$pesanSukses = '';

// Ambil data kategori
$queryAmbilKategori = "SELECT * FROM kategori ORDER BY ket_kategori";
$hasilAmbilKategori = mysqli_query($koneksiDatabase, $queryAmbilKategori);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputIdKategori = mysqli_real_escape_string($koneksiDatabase, $_POST['id_kategori']);
    $inputLokasi = mysqli_real_escape_string($koneksiDatabase, $_POST['lokasi']);
    $inputKeterangan = mysqli_real_escape_string($koneksiDatabase, $_POST['ket']);
    $namaFileFotoSiswa = null;
    
    if (empty($inputIdKategori) || empty($inputLokasi) || empty($inputKeterangan)) {
        $pesanError = "Semua field harus diisi!";
    } else {
        // Insert data aspirasi dulu untuk mendapatkan ID
        $queryInsertAspirasi = "INSERT INTO aspirasi (nis, id_kategori, lokasi, ket, status) 
                               VALUES ('$nisSiswa', '$inputIdKategori', '$inputLokasi', '$inputKeterangan', 'Menunggu')";
        
        if (mysqli_query($koneksiDatabase, $queryInsertAspirasi)) {
            $idAspirasiBaru = mysqli_insert_id($koneksiDatabase);
            
            // Handle upload foto jika ada
            if (isset($_FILES['foto_siswa']) && $_FILES['foto_siswa']['error'] == 0) {
                $fileFoto = $_FILES['foto_siswa'];
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2MB
                
                // Validasi tipe file
                if (!in_array($fileFoto['type'], $allowedTypes)) {
                    $pesanError = "Tipe file tidak diizinkan! Hanya JPG, PNG, dan GIF.";
                    // Hapus data yang sudah diinsert
                    mysqli_query($koneksiDatabase, "DELETE FROM aspirasi WHERE id_aspirasi = $idAspirasiBaru");
                }
                // Validasi ukuran file
                elseif ($fileFoto['size'] > $maxFileSize) {
                    $pesanError = "Ukuran file terlalu besar! Maksimal 2MB.";
                    mysqli_query($koneksiDatabase, "DELETE FROM aspirasi WHERE id_aspirasi = $idAspirasiBaru");
                }
                else {
                    // Buat folder jika belum ada
                    $uploadDir = '../uploads/siswa/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Generate nama file: id_aspirasi_timestamp.extension
                    $fileExtension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
                    $namaFileBaru = $idAspirasiBaru . '_' . time() . '.' . $fileExtension;
                    $pathUpload = $uploadDir . $namaFileBaru;
                    
                    // Upload file
                    if (move_uploaded_file($fileFoto['tmp_name'], $pathUpload)) {
                        // Update database dengan nama file foto
                        $queryUpdateFoto = "UPDATE aspirasi SET foto_siswa = '$namaFileBaru' WHERE id_aspirasi = $idAspirasiBaru";
                        mysqli_query($koneksiDatabase, $queryUpdateFoto);
                        $pesanSukses = "Aspirasi berhasil dikirim beserta foto!";
                    } else {
                        $pesanError = "Gagal mengupload foto! Aspirasi tetap disimpan tanpa foto.";
                        $pesanSukses = "Aspirasi berhasil dikirim (tanpa foto)!";
                    }
                }
            } else {
                $pesanSukses = "Aspirasi berhasil dikirim!";
            }
            
            // Redirect jika sukses untuk mencegah resubmit
            if (empty($pesanError)) {
                $_SESSION['pesan_sukses'] = $pesanSukses;
                header("Location: dashboard.php?success=1");
                exit();
            }
        } else {
            $pesanError = "Gagal mengirim aspirasi: " . mysqli_error($koneksiDatabase);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Aspirasi - Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <span class="navbar-text text-white">
                <?= htmlspecialchars($namaSiswa) ?>
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Form Pengaduan Sarana</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?= $pesanError ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="id_kategori" class="form-label">Kategori Pengaduan <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_kategori" name="id_kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while ($dataKategori = mysqli_fetch_assoc($hasilAmbilKategori)): ?>
                                        <option value="<?= $dataKategori['id_kategori'] ?>">
                                            <?= htmlspecialchars($dataKategori['ket_kategori']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" 
                                       placeholder="Contoh: Ruang Kelas 12 RPL 1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan Detail <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="ket" name="ket" rows="5" 
                                          placeholder="Jelaskan secara detail masalah yang ditemukan..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="foto_siswa" class="form-label">
                                    <i class="bi bi-camera"></i> Upload Foto Bukti (Opsional)
                                </label>
                                <input type="file" class="form-control" id="foto_siswa" name="foto_siswa" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> 
                                    Format: JPG, PNG, GIF. Maksimal 2MB. Foto akan membantu admin memahami masalah dengan lebih baik.
                                </div>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-lightbulb"></i>
                                <strong>Tips:</strong> Sertakan foto jika memungkinkan untuk memperjelas laporan Anda.
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Kirim Aspirasi
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview foto sebelum upload
        document.getElementById('foto_siswa').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                // Validasi ukuran file (client-side)
                if (file.size > 2 * 1024 * 1024) {
                    preview.innerHTML = '<div class="alert alert-warning">Ukuran file terlalu besar! Maksimal 2MB.</div>';
                    this.value = '';
                    return;
                }
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    preview.innerHTML = '<div class="alert alert-warning">Format file tidak didukung!</div>';
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-2"><strong>Preview:</strong></p>
                                <img src="${e.target.result}" class="preview-image" alt="Preview">
                                <p class="text-muted small mt-2">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
                            </div>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });
    </script>
</body>
</html>