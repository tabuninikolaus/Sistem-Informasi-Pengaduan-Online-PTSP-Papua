<?php
require_once 'config/db.php';

if (isset($_GET['pengaduan_id'])) {
    $pengaduan_id = $conn->real_escape_string($_GET['pengaduan_id']);

    // Cek ID atau Email di database
    $sql = "SELECT * FROM pengaduan WHERE id = '$pengaduan_id' OR email_pengadu = '$pengaduan_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hasil Pelacakan</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background: linear-gradient(to bottom right, #1e90ff, #87cefa);
                    color: #ffffff;
                    height: 100vh;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .container {
                    background-color: rgba(255, 255, 255, 0.9);
                    color: #333;
                    border-radius: 8px;
                    padding: 30px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                    max-width: 600px;
                    text-align: center;
                }

                h1 {
                    font-size: 2rem;
                    font-weight: bold;
                    margin-bottom: 20px;
                }

                .table {
                    margin-bottom: 20px;
                }

                .btn-primary, .btn-secondary {
                    border-radius: 20px;
                    padding: 10px 20px;
                    font-size: 16px;
                    margin-top: 10px;
                }

                .btn-primary:hover {
                    background-color: #104e8b;
                }

                .btn-secondary {
                    background-color: transparent;
                    border: 2px solid #1e90ff;
                    color: #1e90ff;
                    transition: 0.3s;
                }

                .btn-secondary:hover {
                    background-color: #1e90ff;
                    color: #ffffff;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Status Pengaduan Anda</h1>
                <table class="table table-bordered">
                    <tr>
                        <th>ID Pengaduan</th>
                        <td><?= $data['id'] ?></td>
                    </tr>
                    <tr>
                        <th>Nama Pengadu</th>
                        <td><?= $data['nama_pengadu'] ?></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><?= $data['kategori_pengaduan'] ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= $data['status'] ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengaduan</th>
                        <td><?= $data['tanggal_pengaduan'] ?></td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?= $data['alasan'] ?></td>
                    </tr>
                </table>
                <a href="lacak_pengaduan.php" class="btn btn-secondary">Kembali</a>
                <a href="index.php" class="btn btn-primary">Beranda</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<script>alert('Data tidak ditemukan! Pastikan ID atau Email benar.'); window.location.href = 'lacak_pengaduan.php';</script>";
    }
} else {
    header("Location: lacak_pengaduan.php");
    exit();
}
?>
