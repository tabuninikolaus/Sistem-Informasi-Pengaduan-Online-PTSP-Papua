<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Pengaduan - PTSP Papua</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(to bottom right, #1e90ff, #87cefa);
            font-family: 'Arial', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #1e90ff;
        }
        .btn-primary {
            background-color: #1e90ff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #104e8b;
        }
        .btn-secondary {
            background-color: transparent;
            border: 2px solid #1e90ff;
            color: #1e90ff;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-secondary:hover {
            background-color: #1e90ff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajukan Pengaduan Anda</h1>
        <form action="submit_pengaduan.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Pengadu:</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Pengadu:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori Pengaduan:</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih kategori...</option>
                    <option value="perizinan">Perizinan</option>
                    <option value="nonperizinan">Non Perizinan</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Pengaduan:</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan pengaduan Anda" required></textarea>
            </div>

            <div class="mb-3">
                <label for="dokumen" class="form-label">Dokumen Pendukung:</label>
                <input type="file" class="form-control" id="dokumen" name="dokumen[]" multiple>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Kembali ke Beranda</a>
                <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
