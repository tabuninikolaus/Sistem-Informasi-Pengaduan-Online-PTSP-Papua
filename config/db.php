<?php
// Koneksi ke database MySQL
$servername = "localhost";
$username = "root";  // Biasanya "root" jika tidak mengubah konfigurasi
$password = "";      // Biasanya kosong jika tidak diatur
$dbname = "pengaduan_ptsp";  // Nama database yang akan digunakan

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
