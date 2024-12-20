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

// Jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    // Update status pengaduan
    $update_sql = "UPDATE pengaduan SET status = '$status' WHERE id = '$id_pengaduan'";
    if ($conn->query($update_sql) === TRUE) {
        header("Location: kelola_pengaduan.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Pengaduan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
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
            <a class="navbar-brand" href="#">Update Status Pengaduan</a>
        </nav>

        <div class="container mt-4">
            <h3>Update Status Pengaduan</h3>

            <form method="POST">
                <div class="form-group">
                    <label for="status">Status Pengaduan:</label>
                    <select class="form-control" id="status" name="status">
                        <option value="Diterima" <?php if ($pengaduan['status'] == 'Diterima') echo 'selected'; ?>>Diterima</option>
                        <option value="Diproses" <?php if ($pengaduan['status'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
                        <option value="Selesai" <?php if ($pengaduan['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update Status</button>
                <a href="kelola_pengaduan.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
