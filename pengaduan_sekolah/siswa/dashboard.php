<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nisSiswa = $_SESSION['nis'];
$namaSiswa = $_SESSION['nama_siswa'];
$kelasSiswa = $_SESSION['kelas_siswa'];

// Hitung statistik
$queryTotalAspirasi = "SELECT COUNT(*) as total FROM aspirasi WHERE nis = '$nisSiswa'";
$hasilTotalAspirasi = mysqli_query($koneksiDatabase, $queryTotalAspirasi);
$dataTotalAspirasi = mysqli_fetch_assoc($hasilTotalAspirasi);
$totalAspirasi = $dataTotalAspirasi['total'];

$queryAspirasiSelesai = "SELECT COUNT(*) as total FROM aspirasi WHERE nis = '$nisSiswa' AND status = 'Selesai'";
$hasilAspirasiSelesai = mysqli_query($koneksiDatabase, $queryAspirasiSelesai);
$dataAspirasiSelesai = mysqli_fetch_assoc($hasilAspirasiSelesai);
$totalSelesai = $dataAspirasiSelesai['total'];

$queryAspirasiTerbaru = "SELECT a.*, k.ket_kategori FROM aspirasi a 
                         JOIN kategori k ON a.id_kategori = k.id_kategori 
                         WHERE a.nis = '$nisSiswa' 
                         ORDER BY a.created_at DESC LIMIT 5";
$hasilAspirasiTerbaru = mysqli_query($koneksiDatabase, $queryAspirasiTerbaru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - LAPOR7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-person-circle"></i> Dashboard Siswa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="form_aspirasi.php">
                    <i class="bi bi-plus-circle"></i> Buat Aspirasi
                </a>
                <a class="nav-link" href="history.php">
                    <i class="bi bi-clock-history"></i> History
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h3>Selamat Datang, <?= htmlspecialchars($namaSiswa) ?> (<?= htmlspecialchars($kelasSiswa) ?>)</h3>
                <hr>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Aspirasi</h5>
                        <h2><?= $totalAspirasi ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Aspirasi Selesai</h5>
                        <h2><?= $totalSelesai ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Dalam Proses</h5>
                        <h2><?= $totalAspirasi - $totalSelesai ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Aspirasi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($hasilAspirasiTerbaru) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($dataAspirasi = mysqli_fetch_assoc($hasilAspirasiTerbaru)): ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($dataAspirasi['created_at'])) ?></td>
                                                <td><?= htmlspecialchars($dataAspirasi['ket_kategori']) ?></td>
                                                <td><?= htmlspecialchars($dataAspirasi['lokasi']) ?></td>
                                                <td>
                                                    <?php
                                                    $badgeClass = '';
                                                    if ($dataAspirasi['status'] == 'Menunggu') $badgeClass = 'warning';
                                                    elseif ($dataAspirasi['status'] == 'Proses') $badgeClass = 'info';
                                                    else $badgeClass = 'success';
                                                    ?>
                                                    <span class="badge bg-<?= $badgeClass ?>"><?= $dataAspirasi['status'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Belum ada aspirasi yang dibuat.</p>
                        <?php endif; ?>
                        <a href="form_aspirasi.php" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Buat Aspirasi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>