<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'config/db.php'; // Koneksi ke database

// Ambil data pengaduan dari database
$sql = "SELECT * FROM pengaduan ORDER BY tanggal_pengaduan DESC";  // Menampilkan pengaduan terbaru
$result = $conn->query($sql);

// Update status pengaduan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pengaduan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $status = $_POST['status'];
    $alasan = $_POST['alasan'];

    // Update status pengaduan
    $sql_update = "UPDATE pengaduan SET status = '$status', alasan = '$alasan' WHERE id = '$id_pengaduan'";
    
    if ($conn->query($sql_update) === TRUE) {
        // Redirect ke halaman kelola pengaduan
        echo "<script>alert('Status pengaduan berhasil diperbarui!'); window.location.href = 'kelola_pengaduan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Hapus pengaduan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_pengaduan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    
    // Hapus pengaduan
    $sql_delete = "DELETE FROM pengaduan WHERE id = '$id_pengaduan'";
    
    if ($conn->query($sql_delete) === TRUE) {
        // Redirect ke halaman kelola pengaduan
        echo "<script>alert('Pengaduan berhasil dihapus!'); window.location.href = 'kelola_pengaduan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengaduan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="logo.png" alt="Logo" class="logo">
            <h2>PTSP Papua</h2>
        </div>
        <ul>
            <li><a href="dashboard_pengaduan.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="kelola_pengaduan.php"><i class="fas fa-bell"></i> Kelola Pengaduan</a></li>
            <li><a href="pengguna.php"><i class="fas fa-users"></i> Pengguna</a></li>
            <li><a href="statistik.php"><i class="fas fa-chart-line"></i> Statistik</a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cogs"></i> Pengaturan</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Kelola Pengaduan</a>
        </nav>

        <div class="container mt-4">
            <h3>Daftar Pengaduan</h3>

            <!-- Tabel Daftar Pengaduan -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pengaduan</th>
                        <th>Nama Pengadu</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nama_pengadu']; ?></td>
                            <td><?php echo $row['kategori_pengaduan']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <!-- Tombol Update Status -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#statusModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-nama="<?php echo $row['nama_pengadu']; ?>"
                                        data-status="<?php echo $row['status']; ?>"
                                        data-alasan="<?php echo $row['alasan']; ?>"
                                >Perbarui Status</button>

                                <!-- Tombol Hapus Pengaduan -->
                                <form action="kelola_pengaduan.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pengaduan" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="hapus_pengaduan" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Update Status Pengaduan -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Perbarui Status Pengaduan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="kelola_pengaduan.php" method="POST">
                        <input type="hidden" name="id_pengaduan" id="status-id">
                        <div class="form-group">
                            <label for="status">Status Pengaduan</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Diproses">Diproses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="alasan">Alasan</label>
                            <textarea class="form-control" id="alasan" name="alasan" rows="3"></textarea>
                        </div>
                        <button type="submit" name="update_pengaduan" class="btn btn-primary">Perbarui Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Script untuk memasukkan data ke modal status
        $('#statusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var status = button.data('status');
            var alasan = button.data('alasan');

            var modal = $(this);
            modal.find('#status-id').val(id);
            modal.find('#status').val(status);
            modal.find('#alasan').val(alasan);
        });
    </script>
</body>
</html>
