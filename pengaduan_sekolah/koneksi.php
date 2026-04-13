<?php
$hostDatabase = "localhost";
$usernameDatabase = "root";
$passwordDatabase = "";
$namaDatabase = "pengaduan_sekolah";

$koneksiDatabase = mysqli_connect($hostDatabase, $usernameDatabase, $passwordDatabase, $namaDatabase);

// Check connection
if (!$koneksiDatabase) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>