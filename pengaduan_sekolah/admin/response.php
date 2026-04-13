<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

$idAspirasi = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pesanSukses = '';
$pesanError = '';

// Ambil data aspirasi
$queryDetailAspirasi = "SELECT a.*, s.nama, s.kelas, k.ket_kategori 
                        FROM aspirasi a 
                        JOIN siswa s ON a.nis = s.nis 
                        JOIN kategori k ON a.id_kategori = k.id_kategori 
                        WHERE a.id_aspirasi = $idAspirasi";
$hasilDetailAspirasi = mysqli_query($koneksiDatabase, $queryDetailAspirasi);

if (mysqli_num_rows($hasilDetailAspirasi) == 0) {
    header("Location: dashboard.php");
    exit();
}

$dataAspirasi = mysqli_fetch_assoc($hasilDetailAspirasi);

// Update status, feedback, dan foto admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputStatus = mysqli_real_escape_string($koneksiDatabase, $_POST['status']);
    $inputFeedback = mysqli_real_escape_string($koneksiDatabase, $_POST['feedback']);
    $namaFileFotoAdmin = $dataAspirasi['foto_admin']; // Pertahankan foto lama jika tidak upload baru
    
    // Validasi upload foto admin (wajib jika status Selesai)
    if ($inputStatus == 'Selesai') {
        if (isset($_FILES['foto_admin']) && $_FILES['foto_admin']['error'] == 0) {
            $fileFotoAdmin = $_FILES['foto_admin'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            // Validasi tipe file
            if (!in_array($fileFotoAdmin['type'], $allowedTypes)) {
                $pesanError = "Tipe file foto tidak diizinkan! Hanya JPG, PNG, dan GIF.";
            }
            // Validasi ukuran file
            elseif ($fileFotoAdmin['size'] > $maxFileSize) {
                $pesanError = "Ukuran file foto terlalu besar! Maksimal 5MB.";
            }
            else {
                // Buat folder jika belum ada
                $uploadDir = '../uploads/admin/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate nama file: id_aspirasi_timestamp.extension
                $fileExtension = pathinfo($fileFotoAdmin['name'], PATHINFO_EXTENSION);
                $namaFileBaru = $idAspirasi . '_' . time() . '.' . $fileExtension;
                $pathUpload = $uploadDir . $namaFileBaru;
                
                // Hapus foto lama jika ada
                if (!empty($dataAspirasi['foto_admin']) && file_exists($uploadDir . $dataAspirasi['foto_admin'])) {
                    unlink($uploadDir . $dataAspirasi['foto_admin']);
                }
                
                // Upload file baru
                if (move_uploaded_file($fileFotoAdmin['tmp_name'], $pathUpload)) {
                    $namaFileFotoAdmin = $namaFileBaru;
                } else {
                    $pesanError = "Gagal mengupload foto bukti!";
                }
            }
        } else {
            $pesanError = "Foto bukti pengerjaan wajib diupload untuk status Selesai!";
        }
    }
    
    // Jika tidak ada error, update database
    if (empty($pesanError)) {
        $queryUpdateAspirasi = "UPDATE aspirasi SET 
                               status = '$inputStatus', 
                               feedback = '$inputFeedback',
                               foto_admin = '$namaFileFotoAdmin'
                               WHERE id_aspirasi = $idAspirasi";
        
        if (mysqli_query($koneksiDatabase, $queryUpdateAspirasi)) {
            $pesanSukses = "Response berhasil disimpan!";
            // Refresh data
            $hasilDetailAspirasi = mysqli_query($koneksiDatabase, $queryDetailAspirasi);
            $dataAspirasi = mysqli_fetch_assoc($hasilDetailAspirasi);
        } else {
            $pesanError = "Gagal menyimpan response: " . mysqli_error($koneksiDatabase);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Aspirasi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .evidence-photo {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .preview-image {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detail Aspirasi #<?= $dataAspirasi['id_aspirasi'] ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Siswa:</strong></div>
                            <div class="col-md-9"><?= htmlspecialchars($dataAspirasi['nama']) ?> (<?= htmlspecialchars($dataAspirasi['kelas']) ?>)</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>NIS:</strong></div>
                            <div class="col-md-9"><?= htmlspecialchars($dataAspirasi['nis']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Kategori:</strong></div>
                            <div class="col-md-9"><?= htmlspecialchars($dataAspirasi['ket_kategori']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Lokasi:</strong></div>
                            <div class="col-md-9"><?= htmlspecialchars($dataAspirasi['lokasi']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Keterangan:</strong></div>
                            <div class="col-md-9"><?= nl2br(htmlspecialchars($dataAspirasi['ket'])) ?></div>
                        </div>
                        
                        <!-- Tampilkan foto siswa jika ada -->
                        <?php if (!empty($dataAspirasi['foto_siswa'])): ?>
                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Foto Bukti Siswa:</strong></div>
                                <div class="col-md-9">
                                    <img src="../uploads/siswa/<?= htmlspecialchars($dataAspirasi['foto_siswa']) ?>" 
                                         class="evidence-photo" 
                                         alt="Foto Bukti"
                                         onclick="showImageModal(this.src)">
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Tanggal:</strong></div>
                            <div class="col-md-9"><?= date('d M Y H:i', strtotime($dataAspirasi['created_at'])) ?></div>
                        </div>
                        
                        <!-- Tampilkan foto admin jika sudah ada -->
                        <?php if (!empty($dataAspirasi['foto_admin'])): ?>
                            <div class="row mb-3">
                                <div class="col-md-3"><strong>Foto Bukti Saat Ini:</strong></div>
                                <div class="col-md-9">
                                    <img src="../uploads/admin/<?= htmlspecialchars($dataAspirasi['foto_admin']) ?>" 
                                         class="evidence-photo" 
                                         alt="Foto Bukti Admin"
                                         onclick="showImageModal(this.src)">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-reply-fill"></i> Form Response</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?= $pesanError ?></div>
                        <?php endif; ?>
                        <?php if ($pesanSukses): ?>
                            <div class="alert alert-success"><?= $pesanSukses ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required onchange="toggleFotoAdminRequirement()">
                                    <option value="Menunggu" <?= $dataAspirasi['status'] == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="Proses" <?= $dataAspirasi['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Selesai" <?= $dataAspirasi['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="feedback" class="form-label">Feedback <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="feedback" name="feedback" rows="4" required><?= htmlspecialchars($dataAspirasi['feedback']) ?></textarea>
                            </div>
                            
                            <div class="mb-3" id="foto_admin_container">
                                <label for="foto_admin" class="form-label">
                                    <i class="bi bi-camera"></i> 
                                    Foto Bukti Pengerjaan 
                                    <span id="foto_admin_required" class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="foto_admin" name="foto_admin" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> 
                                    Upload foto sebagai bukti pengerjaan. Wajib diisi jika status Selesai.
                                </div>
                                <div id="fotoAdminPreview" class="mt-2"></div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Foto bukti pengerjaan wajib diupload untuk status "Selesai" dan akan ditampilkan ke siswa.
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> Simpan Response
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk preview foto -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" class="img-fluid" src="" alt="Preview">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }
        
        function toggleFotoAdminRequirement() {
            const statusSelect = document.getElementById('status');
            const fotoAdminInput = document.getElementById('foto_admin');
            const requiredSpan = document.getElementById('foto_admin_required');
            
            if (statusSelect.value === 'Selesai') {
                fotoAdminInput.required = true;
                requiredSpan.style.display = 'inline';
            } else {
                fotoAdminInput.required = false;
                requiredSpan.style.display = 'none';
            }
        }
        
        // Preview foto admin sebelum upload
        document.getElementById('foto_admin').addEventListener('change', function(e) {
            const preview = document.getElementById('fotoAdminPreview');
            const file = e.target.files[0];
            
            if (file) {
                // Validasi ukuran file (client-side)
                if (file.size > 5 * 1024 * 1024) {
                    preview.innerHTML = '<div class="alert alert-warning">Ukuran file terlalu besar! Maksimal 5MB.</div>';
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
        
        // Inisialisasi requirement saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFotoAdminRequirement();
        });
    </script>
</body>
</html>