<?php
// Pengaturan sesi untuk meningkatkan keamanan dan masa hidup sesi
ini_set('session.gc_maxlifetime', 31536000); // Masa hidup data sesi (1 tahun)
ini_set('session.cookie_lifetime', 31536000); // Masa hidup cookie sesi (1 tahun)

session_set_cookie_params([
    'lifetime' => 31536000, // Masa hidup cookie (1 tahun)
    'path' => '/', // Akses untuk seluruh aplikasi
    'domain' => '', // Kosongkan untuk domain utama
    'secure' => isset($_SERVER['HTTPS']), // Gunakan true jika situs menggunakan HTTPS
    'httponly' => true, // Melindungi cookie dari akses JavaScript
]);

session_start(); // Mulai sesi

// Validasi akses: pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php'); // Jika belum login atau bukan admin, redirect ke halaman login
    exit();
}

require_once 'vendor/autoload.php';
include 'config/db.php'; // Menghubungkan ke database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ambil data pengaduan
$sql = "SELECT * FROM pengaduan WHERE status NOT IN ('Diproses', 'Selesai') ORDER BY tanggal_pengaduan DESC";
$result = $conn->query($sql);

// Fungsi untuk mengirim email
function sendEmail($toEmail, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fleksibel027@gmail.com'; // Email pengirim
        $mail->Password = 'vzlk jwhc lmdr phgd'; // Password email pengirim
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('fleksibel027@gmail.com', 'Admin PTSP Papua');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Jika status pengaduan diperbarui
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pengaduan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $status = $_POST['status'];
    $alasan = $_POST['alasan'];

    // Ambil email pengadu
    $sql_email = "SELECT email_pengadu FROM pengaduan WHERE id = '$id_pengaduan'";
    $result_email = $conn->query($sql_email);
    $email_pengadu = $result_email->fetch_assoc()['email_pengadu'];

    // Update status dan alasan di database
    $sql_update = "UPDATE pengaduan SET status = '$status', alasan = '$alasan' WHERE id = '$id_pengaduan'";
    if ($conn->query($sql_update) === TRUE) {
        // Kirim email hanya jika status adalah 'Diterima' atau 'Ditolak'
        if ($status == 'Diterima' || $status == 'Ditolak') {
            $subject = "Status Pengaduan Anda Telah Diperbarui";
            $message = "<p>Halo,</p>
                        <p>Pengaduan Anda dengan ID <b>$id_pengaduan</b> telah diperbarui menjadi <b>$status</b>.</p>
                        <p>Alasan: $alasan</p>
                        <p>Terima kasih telah menggunakan layanan kami.</p>";
            sendEmail($email_pengadu, $subject, $message);
        }
        echo "<script>alert('Status berhasil diperbarui. Email telah dikirim ke pengadu.'); window.location.href = 'dashboard_pengaduan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Jika pengaduan didistribusikan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['distribusi_pengaduan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];

    // Ambil email pengadu
    $sql_email = "SELECT email_pengadu FROM pengaduan WHERE id = '$id_pengaduan'";
    $result_email = $conn->query($sql_email);
    $email_pengadu = $result_email->fetch_assoc()['email_pengadu'];

    // Update status menjadi 'Terdistribusi'
    $sql_update = "UPDATE pengaduan SET status = 'Terdistribusi' WHERE id = '$id_pengaduan'";
    if ($conn->query($sql_update) === TRUE) {
        // Kirim email pemberitahuan
        $subject = "Pengaduan Anda Telah Didistribusikan";
        $message = "<p>Halo,</p>
                    <p>Pengaduan Anda dengan ID <b>$id_pengaduan</b> telah didistribusikan untuk proses lebih lanjut.</p>
                    <p>Terima kasih telah menggunakan layanan kami.</p>";
        sendEmail($email_pengadu, $subject, $message);

        echo "<script>alert('Pengaduan berhasil didistribusikan. Email telah dikirim ke pengadu.'); window.location.href = 'dashboard_pengaduan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Jika pengaduan dihapus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_pengaduan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];

    $sql_delete = "DELETE FROM pengaduan WHERE id = '$id_pengaduan'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Pengaduan berhasil dihapus.'); window.location.href = 'dashboard_pengaduan.php';</script>";
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
    <title>Dashboard Pengaduan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_dashboard_pengaduan.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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
        <li><a href="statistik.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        <li><a href="export_csv.php"><i class="fas fa-file-csv"></i> Unduh Laporan CSV</a></li>
        <li><a href="export_pdf.php"><i class="fas fa-file-pdf"></i> Unduh Laporan PDF</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
<!-- Main Content -->
<div class="main-content">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Dashboard Seksi Pengaduan</a>
    </nav>
<div class="container mt-5">
    <h3 class="text-center">Dashboard Pengaduan</h3>
    <div class="table-responsive mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Detail</th>
                    <th>Distribusi</th>
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
                            <!-- Tombol Detail -->
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#detailModal"
                                    data-id="<?php echo $row['id']; ?>" 
                                    data-nama="<?php echo $row['nama_pengadu']; ?>" 
                                    data-email="<?php echo $row['email_pengadu']; ?>" 
                                    data-kategori="<?php echo $row['kategori_pengaduan']; ?>" 
                                    data-deskripsi="<?php echo $row['deskripsi']; ?>" 
                                    data-dokumen="<?php echo $row['dokumen']; ?>" 
                                    data-tanggal="<?php echo $row['tanggal_pengaduan']; ?>">
                                Detail
                            </button>
                        </td>
                        <td>
                            <!-- Tombol Distribusi untuk status 'Diterima' -->
                            <?php if ($row['status'] === 'Diterima') { ?>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#distribusiModal"
                                        data-id="<?php echo $row['id']; ?>">
                                    Distribusi
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                            <!-- Tombol Hapus untuk status 'Ditolak' atau 'Terdistribusi' -->
                            <?php if ($row['status'] === 'Ditolak' || $row['status'] === 'Terdistribusi') { ?>
                                <form action="dashboard_pengaduan.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pengaduan" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="hapus_pengaduan" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengaduan ini?')">
                                        Hapus
                                    </button>
                                </form>
                            <?php } else { ?>
                                <!-- Tombol Perbarui Status untuk status lain -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#statusModal"
                                        data-id="<?php echo $row['id']; ?>" 
                                        data-status="<?php echo $row['status']; ?>">
                                    Perbarui Status
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>


        </table>
    </div>
</div>


 <!-- Modal Detail -->
 <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pengaduan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>ID Pengaduan:</strong> <span id="detail-id"></span></p>
                    <p><strong>Nama Pengadu:</strong> <span id="detail-nama"></span></p>
                    <p><strong>Email Pengadu:</strong> <span id="detail-email"></span></p>
                    <p><strong>Kategori:</strong> <span id="detail-kategori"></span></p>
                    <p><strong>Deskripsi:</strong> <span id="detail-deskripsi"></span></p>
                    <p><strong>Dokumen:</strong>
                        <span id="detail-dokumen"></span>
                    </p>
                    <p><strong>Tanggal Pengaduan:</strong> <span id="detail-tanggal"></span></p>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Perbarui Status -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perbarui Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="dashboard_pengaduan.php">
                    <input type="hidden" name="id_pengaduan" id="status-id">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alasan</label>
                        <textarea class="form-control" name="alasan" id="alasan"></textarea>
                    </div>
                    <button type="submit" name="update_pengaduan" class="btn btn-primary">Perbarui</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Distribusi -->
<div class="modal fade" id="distribusiModal" tabindex="-1" role="dialog" aria-labelledby="distribusiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="distribusiModalLabel">Konfirmasi Distribusi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mendistribusikan pengaduan dengan ID <span id="distribusi-id"></span>?</p>
                <form action="dashboard_pengaduan.php" method="POST">
                    <input type="hidden" name="id_pengaduan" id="input-distribusi-id">
                    <button type="submit" name="distribusi_pengaduan" class="btn btn-success">Ya, Distribusikan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script> 
$('#detailModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);

    var dokumen = button.data('dokumen'); // Dokumen dari kolom database
    var dokumenPaths = dokumen ? dokumen.split(',') : []; // Split jika multiple dokumen

    modal.find('#detail-id').text(button.data('id'));
    modal.find('#detail-nama').text(button.data('nama'));
    modal.find('#detail-email').text(button.data('email'));
    modal.find('#detail-kategori').text(button.data('kategori'));
    modal.find('#detail-deskripsi').text(button.data('deskripsi'));

    // Format tanggal
    var rawDate = button.data('tanggal');
    var formattedDate = new Date(rawDate).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    modal.find('#detail-tanggal').text(formattedDate);

    // Menampilkan dokumen
    var dokumenHtml = '';
    if (dokumenPaths.length > 0) {
        dokumenPaths.forEach(function (filePath, index) {
            var fileName = filePath.split('/').pop(); // Ambil nama file saja
            dokumenHtml += `
                <p>
                    Dokumen ${index + 1}: 
                    <a href="${filePath}" target="_blank" class="btn btn-sm btn-primary">View</a>
                    <a href="${filePath}" download="${fileName}" class="btn btn-sm btn-success">Download</a>
                </p>`;
        });
    } else {
        dokumenHtml = '<span class="text-danger">Tidak ada dokumen</span>';
    }
    modal.find('#detail-dokumen').html(dokumenHtml);
});

    // Modal Perbarui Status
    $('#statusModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        modal.find('#status-id').val(button.data('id'));
        modal.find('#status').val(button.data('status'));
        modal.find('#alasan').val('');
    });
    // Modal Distribusi
    $('#distribusiModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id'); // Ambil ID pengaduan dari tombol
        var modal = $(this);

        // Masukkan ID ke modal
        modal.find('#distribusi-id').text(id);
        modal.find('#input-distribusi-id').val(id);
    });
</script>
</body>
</html>
