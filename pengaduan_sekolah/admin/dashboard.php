<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// Filter
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterKategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Build query dengan filter
$queryAmbilAspirasi = "SELECT a.*, s.nama, s.kelas, k.ket_kategori 
                       FROM aspirasi a 
                       JOIN siswa s ON a.nis = s.nis 
                       JOIN kategori k ON a.id_kategori = k.id_kategori 
                       WHERE 1=1";

if (!empty($filterStatus)) {
    $queryAmbilAspirasi .= " AND a.status = '$filterStatus'";
}

if (!empty($filterKategori)) {
    $queryAmbilAspirasi .= " AND a.id_kategori = '$filterKategori'";
}

$queryAmbilAspirasi .= " ORDER BY 
    CASE 
        WHEN a.status = 'Menunggu' THEN 1
        WHEN a.status = 'Proses' THEN 2
        WHEN a.status = 'Selesai' THEN 3
    END, 
    a.created_at DESC";

$hasilAmbilAspirasi = mysqli_query($koneksiDatabase, $queryAmbilAspirasi);

// Ambil kategori untuk filter
$queryKategoriFilter = "SELECT * FROM kategori ORDER BY ket_kategori";
$hasilKategoriFilter = mysqli_query($koneksiDatabase, $queryKategoriFilter);

// Statistik
$queryTotalMenunggu = "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Menunggu'";
$hasilTotalMenunggu = mysqli_query($koneksiDatabase, $queryTotalMenunggu);
$dataTotalMenunggu = mysqli_fetch_assoc($hasilTotalMenunggu);

$queryTotalProses = "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Proses'";
$hasilTotalProses = mysqli_query($koneksiDatabase, $queryTotalProses);
$dataTotalProses = mysqli_fetch_assoc($hasilTotalProses);

$queryTotalSelesai = "SELECT COUNT(*) as total FROM aspirasi WHERE status = 'Selesai'";
$hasilTotalSelesai = mysqli_query($koneksiDatabase, $queryTotalSelesai);
$dataTotalSelesai = mysqli_fetch_assoc($hasilTotalSelesai);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LAPOR7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="laporan.php">
                    <i class="bi bi-file-text"></i> Laporan
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Selamat Datang, Admin</h3>
        <hr>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5>Menunggu</h5>
                        <h2><?= $dataTotalMenunggu['total'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Proses</h5>
                        <h2><?= $dataTotalProses['total'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Selesai</h5>
                        <h2><?= $dataTotalSelesai['total'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Filter Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" <?= $filterStatus == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Proses" <?= $filterStatus == 'Proses' ? 'selected' : '' ?>>Proses</option>
                            <option value="Selesai" <?= $filterStatus == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Filter Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php while ($kategori = mysqli_fetch_assoc($hasilKategoriFilter)): ?>
                                <option value="<?= $kategori['id_kategori'] ?>" <?= $filterKategori == $kategori['id_kategori'] ? 'selected' : '' ?>>
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

        <!-- List Aspirasi -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Daftar Aspirasi Masuk</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($hasilAmbilAspirasi) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($dataAspirasi = mysqli_fetch_assoc($hasilAmbilAspirasi)): ?>
                                    <tr>
                                        <td>#<?= $dataAspirasi['id_aspirasi'] ?></td>
                                        <td><?= htmlspecialchars($dataAspirasi['nama']) ?></td>
                                        <td><?= htmlspecialchars($dataAspirasi['kelas']) ?></td>
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
                                        <td><?= date('d/m/Y', strtotime($dataAspirasi['created_at'])) ?></td>
                                        <td>
                                            <a href="response.php?id=<?= $dataAspirasi['id_aspirasi'] ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-reply"></i> Response
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada aspirasi yang ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>