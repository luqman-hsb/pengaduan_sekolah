<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

$filterStatusLaporan = isset($_GET['status']) ? $_GET['status'] : '';
$filterKategoriLaporan = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Build query laporan
$queryLaporanAspirasi = "SELECT a.*, s.nama, s.kelas, k.ket_kategori 
                         FROM aspirasi a 
                         JOIN siswa s ON a.nis = s.nis 
                         JOIN kategori k ON a.id_kategori = k.id_kategori 
                         WHERE 1=1";

if (!empty($filterStatusLaporan)) {
    $queryLaporanAspirasi .= " AND a.status = '$filterStatusLaporan'";
}

if (!empty($filterKategoriLaporan)) {
    $queryLaporanAspirasi .= " AND a.id_kategori = '$filterKategoriLaporan'";
}

$queryLaporanAspirasi .= " ORDER BY a.created_at DESC";
$hasilLaporanAspirasi = mysqli_query($koneksiDatabase, $queryLaporanAspirasi);

// Ambil kategori untuk filter
$queryKategoriLaporan = "SELECT * FROM kategori ORDER BY ket_kategori";
$hasilKategoriLaporan = mysqli_query($koneksiDatabase, $queryKategoriLaporan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aspirasi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <span class="navbar-text text-white">
                <i class="bi bi-file-text"></i> Laporan Aspirasi
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Filter Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" <?= $filterStatusLaporan == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Proses" <?= $filterStatusLaporan == 'Proses' ? 'selected' : '' ?>>Proses</option>
                            <option value="Selesai" <?= $filterStatusLaporan == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Filter Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php 
                            mysqli_data_seek($hasilKategoriLaporan, 0);
                            while ($kategori = mysqli_fetch_assoc($hasilKategoriLaporan)): 
                            ?>
                                <option value="<?= $kategori['id_kategori'] ?>" <?= $filterKategoriLaporan == $kategori['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kategori['ket_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Hasil Laporan</h5>
                <button onclick="window.print()" class="btn btn-light btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($hasilLaporanAspirasi) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($dataLaporan = mysqli_fetch_assoc($hasilLaporanAspirasi)): 
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d/m/Y', strtotime($dataLaporan['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['nis']) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['nama']) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['kelas']) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['ket_kategori']) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['lokasi']) ?></td>
                                        <td><?= htmlspecialchars($dataLaporan['ket']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = '';
                                            if ($dataLaporan['status'] == 'Menunggu') $badgeClass = 'warning';
                                            elseif ($dataLaporan['status'] == 'Proses') $badgeClass = 'info';
                                            else $badgeClass = 'success';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>"><?= $dataLaporan['status'] ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($dataLaporan['feedback'] ?: '-') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data yang ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>