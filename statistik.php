<?php
// Menghubungkan ke database
require_once 'config/db.php';

// Mengambil data statistik pengaduan
$sql_total_pengaduan = "SELECT COUNT(*) AS total FROM pengaduan";
$result_total = $conn->query($sql_total_pengaduan);
$total_pengaduan = $result_total->fetch_assoc()['total'];

$sql_terima = "SELECT COUNT(*) AS terima FROM pengaduan WHERE status = 'Diterima'";
$result_terima = $conn->query($sql_terima);
$terima = $result_terima->fetch_assoc()['terima'];

$sql_tolak = "SELECT COUNT(*) AS tolak FROM pengaduan WHERE status = 'Ditolak'";
$result_tolak = $conn->query($sql_tolak);
$tolak = $result_tolak->fetch_assoc()['tolak'];

// Statistik berdasarkan kategori
$sql_perizinan = "SELECT COUNT(*) AS perizinan FROM pengaduan WHERE kategori_pengaduan = 'Perizinan'";
$result_perizinan = $conn->query($sql_perizinan);
$perizinan = $result_perizinan->fetch_assoc()['perizinan'];

$sql_nonperizinan = "SELECT COUNT(*) AS nonperizinan FROM pengaduan WHERE kategori_pengaduan = 'Nonperizinan'";
$result_nonperizinan = $conn->query($sql_nonperizinan);
$nonperizinan = $result_nonperizinan->fetch_assoc()['nonperizinan'];

// Grafik berdasarkan kategori
$sql_kategori = "SELECT kategori_pengaduan, COUNT(*) AS jumlah FROM pengaduan GROUP BY kategori_pengaduan";
$result_kategori = $conn->query($sql_kategori);
$data_kategori = [];
while ($row = $result_kategori->fetch_assoc()) {
    $data_kategori[] = $row;
}

// Grafik berdasarkan status
$sql_status = "SELECT status, COUNT(*) AS jumlah FROM pengaduan GROUP BY status";
$result_status = $conn->query($sql_status);
$data_status = [];
while ($row = $result_status->fetch_assoc()) {
    $data_status[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Pengaduan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .sidebar {
            width: 250px;
            background-color: #141625;
            color: #fff;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #fff;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #1e2139;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            overflow-y: auto;
        }

        .chart-section {
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .chart-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        h1, h4 {
            color: #6f42c1;
            margin-bottom: 20px;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>PTSP Papua</h2>
        <ul>
            <li><a href="dashboard_pengaduan.php">Dashboard</a></li>
            <li><a href="export_csv.php"><i class="fas fa-file-csv"></i> Cetak Laporan CSV</a></li>
            <li><a href="export_pdf.php"><i class="fas fa-file-pdf"></i> Cetak Laporan PDF</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="chart-section">
            <div class="chart-container">
                <h4>Jumlah Pengaduan per Kategori</h4>
                <canvas id="chartKategori"></canvas>
            </div>
        </div>

        <div class="chart-section">
            <div class="chart-container">
                <h4>Jumlah Pengaduan per Status</h4>
                <canvas id="chartStatus"></canvas>
            </div>
        </div>

        <div class="chart-section">
            <div class="chart-container">
                <h4>Laporan Trend Statistik Pengaduan 2024</h4>
                <canvas id="chartHorizontal"></canvas>
            </div>
        </div>
    </div>

    <script>
        var kategoriLabels = <?php echo json_encode(array_column($data_kategori, 'kategori_pengaduan')); ?>;
        var kategoriData = <?php echo json_encode(array_column($data_kategori, 'jumlah')); ?>;

        var statusLabels = <?php echo json_encode(array_column($data_status, 'status')); ?>;
        var statusData = <?php echo json_encode(array_column($data_status, 'jumlah')); ?>;

        var horizontalLabels = ['Total Pengaduan', 'Diterima', 'Ditolak', 'Perizinan', 'Nonperizinan'];
        var horizontalData = [
            <?php echo $total_pengaduan; ?>,
            <?php echo $terima; ?>,
            <?php echo $tolak; ?>,
            <?php echo $perizinan; ?>,
            <?php echo $nonperizinan; ?>
        ];

        var ctxKategori = document.getElementById('chartKategori').getContext('2d');
        new Chart(ctxKategori, {
            type: 'bar',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    label: 'Jumlah Pengaduan per Kategori',
                    data: kategoriData,
                    backgroundColor: kategoriLabels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`),
                    borderColor: kategoriLabels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        var ctxStatus = document.getElementById('chartStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)']
                }]
            },
            options: {
                responsive: true
            }
        });

        var ctxHorizontal = document.getElementById('chartHorizontal').getContext('2d');
        new Chart(ctxHorizontal, {
            type: 'bar',
            data: {
                labels: horizontalLabels,
                datasets: [{
                    label: 'Statistik Pengaduan',
                    data: horizontalData,
                    backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 192, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)']
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
