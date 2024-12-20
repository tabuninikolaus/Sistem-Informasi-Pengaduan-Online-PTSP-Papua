<?php
// Menyertakan file konfigurasi database
include 'config/db.php';

// Menetapkan header agar file diunduh sebagai CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_Perizinan_peengaduan.csv');

// Membuka output stream untuk menulis data CSV
$output = fopen('php://output', 'w');

// Menulis header kolom ke file CSV
fputcsv($output, ['ID', 'Nama Pengadu', 'Email Pengadu', 'Kategori Pengaduan', 'Deskripsi', 'Status', 'Tanggal Pengaduan']);

// Query untuk mengambil data dari tabel pengaduan
$query = "SELECT id, nama_pengadu, email_pengadu, kategori_pengaduan, deskripsi, status, tanggal_pengaduan FROM pengaduan  WHERE kategori_pengaduan = 'perizinan' 
          AND status IN ('Diproses', 'Selesai', 'Terdistribusi')";
$result = $conn->query($query);

// Mengecek apakah ada data
if ($result->num_rows > 0) {
    // Menulis setiap baris data ke file CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // Jika tidak ada data, tambahkan pesan ke file CSV
    fputcsv($output, ['Tidak ada data pengaduan yang tersedia']);
}

// Menutup output stream
fclose($output);

// Menutup koneksi database
$conn->close();
?>
