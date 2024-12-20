<?php
ini_set('session.gc_maxlifetime', 31536000); // Masa hidup data sesi
ini_set('session.cookie_lifetime', 31536000); // Masa hidup cookie sesi
session_set_cookie_params([
    'lifetime' => 31536000,
    'path' => '/',
    'domain' => '', // Biarkan kosong untuk domain utama
    'secure' => isset($_SERVER['HTTPS']), // Gunakan true jika situs menggunakan HTTPS
    'httponly' => true, // Melindungi cookie dari akses JavaScript
]);
session_start();

// Validasi akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'nonperizinan') {
    header('Location: login.php'); // Redirect ke halaman login jika sesi tidak valid
    exit();
}

require_once 'vendor/autoload.php';
include 'config/db.php'; // Menghubungkan ke database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ambil data nonperizinan dari tabel pengaduan dengan status relevan
$sql = "SELECT * FROM pengaduan WHERE kategori_pengaduan = 'nonperizinan' AND status IN ('Terdistribusi', 'Diproses', 'Selesai') ORDER BY tanggal_pengaduan DESC";
$result = $conn->query($sql);

// Fungsi untuk mengirim email menggunakan PHPMailer
function sendEmail($toEmail, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fleksibel027@gmail.com';
        $mail->Password = 'vzlk jwhc lmdr phgd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('fleksibel027@gmail.com', 'Admin PTSP Papua');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}
// Jika status pengaduan diperbarui
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_nonperizinan'])) {
    $id_pengaduan = $_POST['id_nonperizinan'];
    $status = $_POST['status'];
    $alasan = $_POST['alasan'];
    $tanggal_selesai = ($status == 'Selesai') ? date('Y-m-d') : NULL; // Set tanggal jika status selesai

    // Update status, alasan, dan tanggal_selesai_pengaduan di database
    $sql_update = "UPDATE pengaduan 
                   SET status = '$status', 
                       alasan = '$alasan', 
                       tanggal_selesai_pengaduan = " . ($tanggal_selesai ? "'$tanggal_selesai'" : "NULL") . " 
                   WHERE id = '$id_pengaduan'";
    if ($conn->query($sql_update) === TRUE) {
        // Kirim email pemberitahuan ke pengadu
        $sql_email = "SELECT email_pengadu, nama_pengadu FROM pengaduan WHERE id = '$id_pengaduan'";
        $result_email = $conn->query($sql_email);
        if ($result_email->num_rows > 0) {
            $data_email = $result_email->fetch_assoc();
            $email_pengadu = $data_email['email_pengadu'];
            $nama_pengadu = $data_email['nama_pengadu'];

            $subject = "Status Pengaduan Anda: $status";
            $message = "
                <p>Yth. $nama_pengadu,</p>
                <p>Status pengaduan Anda telah diperbarui menjadi: <strong>$status</strong>.</p>
                <p><strong>Alasan:</strong> $alasan</p>
                <p>Terima kasih atas perhatian Anda.</p>
            ";
            sendEmail($email_pengadu, $subject, $message);
        }

        echo "<script>alert('Status dan alasan pengaduan berhasil diperbarui.'); window.location.href = 'dashboard_nonperizinan.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}



// Jika tombol hapus diklik
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_nonperizinan'])) {
    $id_nonperizinan = $_POST['id_nonperizinan'];
    $sql_delete = "DELETE FROM pengaduan WHERE id = '$id_nonperizinan'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Pengaduan berhasil dihapus!'); window.location.href = 'dashboard_nonperizinan.php';</script>";
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
    <title>Dashboard Nonperizinan</title>
    <link rel="stylesheet" href="css/style_dashboard_nonperizinan.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>PTSP Papua</h2>
        <ul>
        <li><a href="export_csv_nonperizinan.php"><i class="fas fa-file-csv"></i> Unduh CSV</a></li>
        <li><a href="export_pdf_nonperizinan.php"><i class="fas fa-file-pdf"></i> Unduh PDF</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Dashboard Nonperizinan</a>
        </nav>

        <div class="container mt-4">
            <h3>Daftar Pengaduan Nonperizinan</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Detail</th>
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
                                <?php if ($row['status'] === 'Terdistribusi' || $row['status'] === 'Diproses') { ?>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#statusModal"
                                            data-id="<?php echo $row['id']; ?>" data-status="<?php echo $row['status']; ?>">
                                        Perbarui Status
                                    </button>
                                <?php } elseif ($row['status'] === 'Selesai') { ?>
                                    <form action="dashboard_nonperizinan.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_nonperizinan" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_nonperizinan" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">
                                            Hapus
                                        </button>
                                    </form>
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
                    <h5 class="modal-title">Perbarui Status Nonperizinan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="dashboard_nonperizinan.php" method="POST">
                        <input type="hidden" name="id_nonperizinan" id="status-id">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Diproses">Diproses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                        <button type="submit" name="update_nonperizinan" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
       // Modal Detail
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
            modal.find('#detail-tanggal').text(button.data('tanggal'));

            // Menampilkan dokumen
            var dokumenHtml = '';
            if (dokumenPaths.length > 0) {
                dokumenPaths.forEach(function (filePath, index) {
                    dokumenHtml += `
                        <p>
                            Dokumen ${index + 1}: 
                            <a href="${filePath}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            <a href="${filePath}" download class="btn btn-sm btn-success">Download</a>
                        </p>`;
                });
            } else {
                dokumenHtml = '<span class="text-danger">Tidak ada dokumen</span>';
            }
            modal.find('#detail-dokumen').html(dokumenHtml);
        });
        // Perbarui Status Modal
        $('#statusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            modal.find('#status-id').val(button.data('id'));
            modal.find('#status').val(button.data('status'));
            modal.find('#keterangan').val('');
        });
    </script>
</body>
</html>
