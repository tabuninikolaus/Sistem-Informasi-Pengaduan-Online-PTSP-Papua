<?php
require 'libs/fpdf/fpdf.php';
include 'config/db.php';

class PDF extends FPDF {
    // Header bagian atas PDF
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Data Pengaduan Kategori Perizinan', 0, 1, 'C'); // Judul PDF
        $this->Ln(10);
    }

    // Footer bagian bawah PDF
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

// Header kolom tabel
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Nama Pengadu', 1);
$pdf->Cell(60, 10, 'Email', 1);
$pdf->Cell(50, 10, 'Status', 1);
$pdf->Ln();

// Query untuk mengambil data pengaduan dengan kategori 'perizinan'
$query = "SELECT id, nama_pengadu, email_pengadu, status 
          FROM pengaduan 
          WHERE kategori_pengaduan = 'perizinan' 
          AND status IN ('Diproses', 'Selesai', 'Terdistribusi')";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Mengatur font untuk data
    $pdf->SetFont('Arial', '', 10);

    // Menampilkan data baris per baris
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 10, $row['id'], 1);
        $pdf->Cell(50, 10, $row['nama_pengadu'], 1);
        $pdf->Cell(60, 10, $row['email_pengadu'], 1);
        $pdf->Cell(50, 10, $row['status'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data pengaduan dengan kategori "perizinan".', 0, 1, 'C');
}

$pdf->Output();
$conn->close();
?>
