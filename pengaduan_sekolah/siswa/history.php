<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nisSiswa = $_SESSION['nis'];
$namaSiswa = $_SESSION['nama_siswa'];

// Ambil semua aspirasi siswa
$queryHistoryAspirasi = "SELECT a.*, k.ket_kategori 
                         FROM aspirasi a 
                         JOIN kategori k ON a.id_kategori = k.id_kategori 
                         WHERE a.nis = '$nisSiswa' 
                         ORDER BY a.created_at DESC";
$hasilHistoryAspirasi = mysqli_query($koneksiDatabase, $queryHistoryAspirasi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Aspirasi - Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .aspirasi-photo {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .aspirasi-photo:hover {
            transform: scale(1.05);
        }
        
        .modal-photo {
            max-width: 100%;
            max-height: 80vh;
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
        <h3><i class="bi bi-clock-history"></i> History Pengaduan</h3>
        <hr>

        <?php if (mysqli_num_rows($hasilHistoryAspirasi) > 0): ?>
            <?php while ($dataAspirasi = mysqli_fetch_assoc($hasilHistoryAspirasi)): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>ID: #<?= $dataAspirasi['id_aspirasi'] ?></strong>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <?= date('d M Y H:i', strtotime($dataAspirasi['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Kategori:</strong> <?= htmlspecialchars($dataAspirasi['ket_kategori']) ?></p>
                                <p><strong>Lokasi:</strong> <?= htmlspecialchars($dataAspirasi['lokasi']) ?></p>
                                <p><strong>Keterangan:</strong> <?= nl2br(htmlspecialchars($dataAspirasi['ket'])) ?></p>
                                
                                <!-- Tampilkan foto siswa jika ada -->
                                <?php if (!empty($dataAspirasi['foto_siswa'])): ?>
                                    <div class="mt-3">
                                        <strong><i class="bi bi-camera"></i> Foto Bukti:</strong><br>
                                        <img src="../uploads/siswa/<?= htmlspecialchars($dataAspirasi['foto_siswa']) ?>" 
                                             class="aspirasi-photo mt-2" 
                                             alt="Foto Bukti"
                                             onclick="showImageModal(this.src)">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <strong>Status:</strong>
                                    <?php
                                    $badgeClass = '';
                                    if ($dataAspirasi['status'] == 'Menunggu') $badgeClass = 'warning';
                                    elseif ($dataAspirasi['status'] == 'Proses') $badgeClass = 'info';
                                    else $badgeClass = 'success';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= $dataAspirasi['status'] ?></span>
                                </div>
                                
                                <!-- Tampilkan feedback dan foto admin jika ada -->
                                <?php if (!empty($dataAspirasi['feedback'])): ?>
                                    <div class="alert alert-info mt-3">
                                        <strong><i class="bi bi-chat-dots"></i> Feedback Admin:</strong><br>
                                        <?= nl2br(htmlspecialchars($dataAspirasi['feedback'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Tampilkan foto admin sebagai bukti pengerjaan -->
                                <?php if (!empty($dataAspirasi['foto_admin'])): ?>
                                    <div class="mt-3">
                                        <strong><i class="bi bi-check-circle"></i> Bukti Pengerjaan:</strong><br>
                                        <img src="../uploads/admin/<?= htmlspecialchars($dataAspirasi['foto_admin']) ?>" 
                                             class="aspirasi-photo mt-2" 
                                             alt="Foto Bukti Admin"
                                             onclick="showImageModal(this.src)">
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($dataAspirasi['updated_at']) && $dataAspirasi['updated_at'] != $dataAspirasi['created_at']): ?>
                                    <small class="text-muted d-block mt-2">
                                        Terakhir update: <?= date('d M Y H:i', strtotime($dataAspirasi['updated_at'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada aspirasi yang dibuat.
                <a href="form_aspirasi.php" class="alert-link">Buat aspirasi sekarang</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal untuk menampilkan foto full size -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" class="modal-photo" src="" alt="Preview">
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
    </script>
</body>
</html>