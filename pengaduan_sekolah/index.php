<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan Sarana Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .school-image-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 40px;
            color: white;
            text-align: center;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .school-image-placeholder i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .school-image-placeholder h3 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .school-image-placeholder p {
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .school-description {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            border-left: 5px solid #0d6efd;
        }
        
        .school-description h4 {
            color: #0d6efd;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .school-description ul {
            list-style: none;
            padding-left: 0;
        }
        
        .school-description ul li {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .school-description ul li:last-child {
            border-bottom: none;
        }
        
        .school-description ul li i {
            color: #0d6efd;
            margin-right: 10px;
        }
        
        .feature-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .btn-photo-upload {
            background: rgba(255,255,255,0.2);
            border: 2px dashed rgba(255,255,255,0.5);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-photo-upload:hover {
            background: rgba(255,255,255,0.3);
            border-color: white;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-building"></i> LAPOR7
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="siswa/login.php">Login Siswa</a>
                <a class="nav-link" href="siswa/register.php">Register Siswa</a>
                <a class="nav-link" href="admin/login.php">Login Admin</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 mb-4">Selamat Datang</h1>
                <p class="lead">Sistem Pengaduan Sarana dan Prasarana Sekolah</p>
                <hr class="my-4">
                <p>Sampaikan aspirasi Anda untuk perbaikan sarana sekolah yang lebih baik</p>
                <div class="mt-4">
                    <a href="siswa/login.php" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-person"></i> Area Siswa
                    </a>
                    <a href="admin/login.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-shield"></i> Area Admin
                    </a>
                </div>
            </div>
        </div>

        <!-- School Photo Placeholder Section -->
        <div class="row mb-5">
            <div class="col-md-12">
                <img src="assets/jobfairsmk7.jpg" class="img-fluid rounded d-block mx-auto" alt="Foto Sekolah"/>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-pencil-square text-primary" style="font-size: 40px;"></i>
                        <h5 class="card-title mt-3">Mudah Melapor</h5>
                        <p class="card-text">Siswa dapat dengan mudah melaporkan kerusakan atau masalah sarana sekolah</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-primary" style="font-size: 40px;"></i>
                        <h5 class="card-title mt-3">Pantau Status</h5>
                        <p class="card-text">Pantau status laporan dan feedback dari admin secara real-time</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check text-primary" style="font-size: 40px;"></i>
                        <h5 class="card-title mt-3">Respon Cepat</h5>
                        <p class="card-text">Admin akan merespon setiap laporan dengan cepat dan tepat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- School Description Section -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="school-description">
                    <h4>
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Tentang Sekolah SMK Negeri 7 Batam
                    </h4>
                    <p class="lead">
                        SMK Negeri 7 Batam merupakan salah satu Sekolah Menengah Kejuruan (SMK) yang ada di Kota Batam, Provinsi Kepulauan Riau. Sekolah ini didirikan untuk memenuhi kebutuhan akan pendidikan vokasi yang berkualitas, khususnya dalam mendukung pengembangan industri dan teknologi di wilayah Batam, yang merupakan kawasan industri terbesar di Indonesia bagian barat.
                    </p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-star-fill text-warning me-2"></i>Visi</h5>
                            <p>Menjadi sekolah menengah kejuruan unggul yang mencetak tenaga kerja terampil dan berdaya saing tinggi dalam bidang keahlian industri dan teknologi, serta berkarakter dan berakhlak mulia.</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-flag-fill text-success me-2"></i>Misi</h5>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> Menyelenggarakan pendidikan dan pelatihan yang berkualitas di bidang kejuruan yang relevan dengan perkembangan industri dan teknologi.</li>
                                <li><i class="bi bi-check-circle-fill"></i> Menghasilkan lulusan yang memiliki keterampilan dan pengetahuan yang kompeten, siap bekerja, serta memiliki etika profesional yang baik.</li>
                                <li><i class="bi bi-check-circle-fill"></i> Membangun kerja sama yang erat dengan dunia industri dan dunia usaha (DUDI) untuk mendukung peningkatan kompetensi dan penyerapan tenaga kerja.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5><i class="bi bi-geo-alt-fill text-danger me-2"></i>Alamat</h5>
                            <p>PERUM SEKAWAN PEMKO, Kec. Batam Kota, Kota Batam, Prov. Kepulauan Riau</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; <?= date('Y') ?> LAPOR7. All rights reserved.
            </p>
            <p class="text-muted">
                <small>Dikembangkan untuk meningkatkan kualitas sarana dan prasarana pendidikan</small>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>