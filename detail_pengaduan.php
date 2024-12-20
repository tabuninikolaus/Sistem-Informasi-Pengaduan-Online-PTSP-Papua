<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include 'config/db.php';

// Mengambil ID pengaduan dari URL
$id_pengaduan = $_GET['id'];

// Query untuk mendapatkan detail pengaduan
$sql = "SELECT * FROM pengaduan WHERE id = '$id_pengaduan'";
$result = $conn->query($sql);
$pengaduan = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
</head>
<body>
    <div class="sidebar">
        <h2>PTSP Papua</h2>
        <ul>
            <li><a href="dashboard_pengaduan.php">Dashboard</a></li>
            <li><a href="kelola_pengaduan.php">Kelola Pengaduan</a></li>
            <li><a href="pengguna.php">Pengguna</a></li>
            <li><a href="statistik.php">Statistik</a></li>
            <li><a href="pengaturan.php">Pengaturan</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Detail Pengaduan</a>
        </nav>

        <div class="container mt-4">
            <h3>Detail Pengaduan</h3>

            <div class="form-group">
                <label for="nama_pengadu">Nama Pengadu:</label>
                <input type="text" class="form-control" id="nama_pengadu" value="<?php echo $pengaduan['nama_pengadu']; ?>" disabled>
            </div>

            <div class="form-group">
                <label for="email_pengadu">Email Pengadu:</label>
                <input type="email" class="form-control" id="email_pengadu" value="<?php echo $pengaduan['email_pengadu']; ?>" disabled>
            </div>

            <div class="form-group">
                <label for="kategori_pengaduan">Kategori Pengaduan:</label>
                <input type="text" class="form-control" id="kategori_pengaduan" value="<?php echo $pengaduan['kategori_pengaduan']; ?>" disabled>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea class="form-control" id="deskripsi" rows="5" disabled><?php echo $pengaduan['deskripsi']; ?></textarea>
            </div>

            <?php if ($pengaduan['dokumen']) { ?>
                <div class="form-group">
                    <label>Dokumen:</label>
                    <a href="download.php?file=<?php echo $pengaduan['dokumen']; ?>" class="btn btn-primary">Download Dokumen</a>
                </div>
            <?php } ?>

            <a href="kelola_pengaduan.php" class="btn btn-secondary">Kembali ke Daftar Pengaduan</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
