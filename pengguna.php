<?php


require_once 'config/db.php'; // Koneksi ke database

// Ambil data pengguna dari database
$sql = "SELECT * FROM users ORDER BY created_at DESC";  // Menampilkan pengguna berdasarkan abjad
$result = $conn->query($sql);

// Tambah Pengguna Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_pengguna'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Menyimpan password terenkripsi
    
    // Insert pengguna baru ke dalam database
    $sql_insert = "INSERT INTO users (username, email, role, password) VALUES ('$username', '$email', '$role', '$password')";
    
    if ($conn->query($sql_insert) === TRUE) {
        // Redirect ke halaman pengguna
        echo "<script>alert('Pengguna berhasil ditambahkan!'); window.location.href = 'pengguna.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Edit Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_pengguna'])) {
    $id_pengguna = $_POST['id_pengguna'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // Update pengguna
    $sql_update = "UPDATE users SET username = '$username', email = '$email', role = '$role' WHERE id = '$id_pengguna'";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Data pengguna berhasil diperbarui!'); window.location.href = 'pengguna.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Hapus Pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_pengguna'])) {
    $id_pengguna = $_POST['id_pengguna'];
    
    // Hapus pengguna
    $sql_delete = "DELETE FROM users WHERE id = '$id_pengguna'";
    
    if ($conn->query($sql_delete) === TRUE) {
        // Redirect ke halaman pengguna
        echo "<script>alert('Pengguna berhasil dihapus!'); window.location.href = 'pengguna.php';</script>";
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
    <title>Kelola Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
</head>
<body>

    <!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-title">PTSP Papua</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard_pengaduan.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="pengguna.php"><i class="fas fa-users"></i> Kelola User</a></li>
    </ul>
</div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Kelola Pengguna</a>
        </nav>

        <div class="container mt-4">
            <h3>Daftar Pengguna</h3>

            <!-- Tabel Daftar Pengguna -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pengguna</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <!-- Tombol Edit Pengguna -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-nama="<?php echo $row['username']; ?>"
                                        data-email="<?php echo $row['email']; ?>"
                                        data-role="<?php echo $row['role']; ?>"
                                >Edit</button>

                                <!-- Tombol Hapus Pengguna -->
                                <form action="pengguna.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pengguna" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="hapus_pengguna" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Button untuk menambah pengguna -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#tambahModal">Tambah Pengguna Baru</button>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="pengguna.php" method="POST">
                        <input type="hidden" name="id_pengguna" id="edit-id">
                        <div class="form-group">
                            <label for="edit-nama">Username</label>
                            <input type="text" class="form-control" id="edit-nama" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role">Role</label>
                            <select class="form-control" id="edit-role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="Perizinan">Perizinan</option>
                                <option value="nonperizinan">Nonperizinan</option>
                            </select>
                        </div>
                        <button type="submit" name="edit_pengguna" class="btn btn-primary">Perbarui Pengguna</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="pengguna.php" method="POST">
                        <div class="form-group">
                            <label for="tambah-username">Username</label>
                            <input type="text" class="form-control" id="tambah-username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="tambah-email">Email</label>
                            <input type="email" class="form-control" id="tambah-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="tambah-role">Role</label>
                            <select class="form-control" id="tambah-role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="Perizinan">Perizinan</option>
                                <option value="nonperizinan">Nonperizinan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tambah-password">Password</label>
                            <input type="password" class="form-control" id="tambah-password" name="password" required>
                        </div>
                        <button type="submit" name="tambah_pengguna" class="btn btn-primary">Tambah Pengguna</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Script untuk memasukkan data ke modal edit pengguna
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var username = button.data('username');
            var email = button.data('email');
            var role = button.data('role');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-username').val(username);
            modal.find('#edit-email').val(email);
            modal.find('#edit-role').val(role);
        });
    </script>
</body>
</html>
