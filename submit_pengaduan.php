<?php
require 'vendor/PHPMailer-master/src/Exception.php';
require 'vendor/PHPMailer-master/src/PHPMailer.php';
require 'vendor/PHPMailer-master/src/SMTP.php';
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    // Tangani upload beberapa dokumen
    $uploadDir = 'uploads/';
    $uploadedFiles = []; // Untuk menyimpan path file yang berhasil diunggah

    if (!empty($_FILES['dokumen']['name'][0])) { // Pastikan ada file yang diunggah
        foreach ($_FILES['dokumen']['name'] as $key => $fileName) {
            $fileTmp = $_FILES['dokumen']['tmp_name'][$key];
            $fileSize = $_FILES['dokumen']['size'][$key];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validasi tipe file
            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'xlsx'];
            if (!in_array($fileType, $allowedTypes)) {
                die("Ekstensi file $fileName tidak valid.");
            }

            // Validasi ukuran file (maksimal 10MB)
            if ($fileSize > 10485760) {
                die("Ukuran file $fileName terlalu besar (maks 10MB).");
            }

            // Membuat nama file unik
            $uniqueFileName = uniqid() . '.' . $fileType;
            $filePath = $uploadDir . $uniqueFileName;

            // Pindahkan file ke direktori tujuan
            if (move_uploaded_file($fileTmp, $filePath)) {
                $uploadedFiles[] = $filePath; // Simpan path file yang berhasil diunggah
            } else {
                die("Gagal mengunggah file $fileName.");
            }
        }
    }

    // Gabungkan path file yang berhasil diunggah menjadi satu string
    $filePaths = implode(',', $uploadedFiles);

    $sql = "INSERT INTO pengaduan (nama_pengadu, email_pengadu, kategori_pengaduan, deskripsi, dokumen, status)
    VALUES ('$nama', '$email', '$kategori', '$deskripsi', '$filePaths', 'Masuk')";

    if ($conn->query($sql) === TRUE) {
        $pengaduanId = $conn->insert_id;

        // Kirim email konfirmasi
        $subject = "Konfirmasi Penginputan Pengaduan";
        $message = "Pengaduan Anda telah dikirim. Berikut adalah ID Pengaduan Anda untuk lakukan Pelacakan aduan anda: $pengaduanId";

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'fleksibel027@gmail.com';
            $mail->Password = 'vzlk jwhc lmdr phgd';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('fleksibel027@gmail.com', 'Admin PTSP Papua');
            $mail->addAddress($email, $nama);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($message);

            $mail->send();
        } catch (Exception $e) {
            echo "Gagal mengirim email: {$mail->ErrorInfo}";
        }

        // Tampilkan modal sukses dengan ID pengaduan
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
            <title>Pengaduan Berhasil</title>
        </head>
        <body>
            <div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
                <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                        <div class='modal-header bg-success text-white'>
                            <h5 class='modal-title' id='successModalLabel'>Pengaduan Berhasil</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body text-center'>
                            <p class='fs-5'>Pengaduan berhasil diajukan!</p>
                            <p><strong>ID Pengaduan Anda: $pengaduanId</strong></p>
                        </div>
                        <div class='modal-footer justify-content-center'>
                            <a href='index.php' class='btn btn-primary'>Tutup</a>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            </script>
        </body>
        </html>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
