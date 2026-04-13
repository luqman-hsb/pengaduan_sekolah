<?php
session_start();
require_once '../koneksi.php';

if (isset($_SESSION['nis'])) {
    header("Location: dashboard.php");
    exit();
}

$pesanError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputNis = mysqli_real_escape_string($koneksiDatabase, $_POST['nis']);
    $inputPassword = $_POST['password'];

    $queryLogin = "SELECT * FROM siswa WHERE nis = '$inputNis'";
    $hasilLogin = mysqli_query($koneksiDatabase, $queryLogin);
    
    if (mysqli_num_rows($hasilLogin) > 0) {
        $dataSiswa = mysqli_fetch_assoc($hasilLogin);
        
        if (password_verify($inputPassword, $dataSiswa['password'])) {
            $_SESSION['nis'] = $dataSiswa['nis'];
            $_SESSION['nama_siswa'] = $dataSiswa['nama'];
            $_SESSION['kelas_siswa'] = $dataSiswa['kelas'];
            header("Location: dashboard.php");
            exit();
        } else {
            $pesanError = "Password salah!";
        }
    } else {
        $pesanError = "NIS tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - LAPOR7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Login Siswa</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?= $pesanError ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="register.php">Belum punya akun? Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>