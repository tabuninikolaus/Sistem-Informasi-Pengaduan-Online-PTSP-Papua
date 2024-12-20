<?php
require 'libs/fpdf/fpdf.php';
include 'config/db.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Data Pengaduan', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);

// Header kolom
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Nama Pengadu', 1);
$pdf->Cell(60, 10, 'Email', 1);
$pdf->Cell(50, 10, 'Status', 1);
$pdf->Ln();

$query = "SELECT id, nama_pengadu, email_pengadu, status 
          FROM pengaduan 
          WHERE kategori_pengaduan = 'nonperizinan' 
          AND status IN ('Diproses', 'Selesai', 'Terdistribusi')";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $pdf->SetFont('Arial', '', 10);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 10, $row['id'], 1);
        $pdf->Cell(50, 10, $row['nama_pengadu'], 1);
        $pdf->Cell(60, 10, $row['email_pengadu'], 1);
        $pdf->Cell(50, 10, $row['status'], 1);
        $pdf->Ln();
    }
}

$pdf->Output();
$conn->close();
?>
