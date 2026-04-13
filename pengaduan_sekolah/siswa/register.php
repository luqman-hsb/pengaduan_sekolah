<?php
session_start();
require_once '../koneksi.php';

if (isset($_SESSION['nis'])) {
    header("Location: dashboard.php");
    exit();
}

$pesanError = '';
$pesanSukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputNis = mysqli_real_escape_string($koneksiDatabase, $_POST['nis']);
    $inputNama = mysqli_real_escape_string($koneksiDatabase, $_POST['nama']);
    $inputKelas = mysqli_real_escape_string($koneksiDatabase, $_POST['kelas']);
    $inputPassword = $_POST['password'];
    $inputKonfirmasiPassword = $_POST['konfirmasi_password'];

    // Validasi
    if (empty($inputNis) || empty($inputNama) || empty($inputKelas) || empty($inputPassword)) {
        $pesanError = "Semua field harus diisi!";
    } elseif ($inputPassword !== $inputKonfirmasiPassword) {
        $pesanError = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek NIS sudah terdaftar atau belum
        $queryCekNis = "SELECT nis FROM siswa WHERE nis = '$inputNis'";
        $hasilCekNis = mysqli_query($koneksiDatabase, $queryCekNis);
        
        if (mysqli_num_rows($hasilCekNis) > 0) {
            $pesanError = "NIS sudah terdaftar!";
        } else {
            // Hash password
            $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);
            
            // Insert data siswa
            $queryInsertSiswa = "INSERT INTO siswa (nis, nama, kelas, password) VALUES ('$inputNis', '$inputNama', '$inputKelas', '$hashedPassword')";
            
            if (mysqli_query($koneksiDatabase, $queryInsertSiswa)) {
                $pesanSukses = "Registrasi berhasil! Silakan login.";
            } else {
                $pesanError = "Registrasi gagal: " . mysqli_error($koneksiDatabase);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Siswa - LAPOR7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register Siswa</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?= $pesanError ?></div>
                        <?php endif; ?>
                        <?php if ($pesanSukses): ?>
                            <div class="alert alert-success"><?= $pesanSukses ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="kelas" class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Contoh: XII RPL 1" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="login.php">Sudah punya akun? Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>