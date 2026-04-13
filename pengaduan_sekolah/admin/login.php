<?php
session_start();
require_once '../koneksi.php';

if (isset($_SESSION['admin_username'])) {
    header("Location: dashboard.php");
    exit();
}

$pesanError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = mysqli_real_escape_string($koneksiDatabase, $_POST['username']);
    $inputPassword = $_POST['password'];

    $queryLoginAdmin = "SELECT * FROM admin WHERE username = '$inputUsername'";
    $hasilLoginAdmin = mysqli_query($koneksiDatabase, $queryLoginAdmin);
    
    if (mysqli_num_rows($hasilLoginAdmin) > 0) {
        $dataAdmin = mysqli_fetch_assoc($hasilLoginAdmin);
        
        if (password_verify($inputPassword, $dataAdmin['password'])) {
            $_SESSION['admin_username'] = $dataAdmin['username'];
            $_SESSION['admin_id'] = $dataAdmin['id_admin'];
            header("Location: dashboard.php");
            exit();
        } else {
            $pesanError = "Password salah!";
        }
    } else {
        $pesanError = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - LAPOR7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Login Admin</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?= $pesanError ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Login</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="../index.php">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>