<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pengaduan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/lacak_pengaduan.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #1e90ff, #87cefa);
            color: #ffffff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        main .container {
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            text-align: center;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 6px;
            padding: 10px;
        }

        .btn-primary {
            background-color: #1e90ff;
            border-color: #1e90ff;
            transition: 0.3s;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #104e8b;
            border-color: #104e8b;
        }

        .btn-back {
            background-color: transparent;
            border: 2px solid #ffffff;
            color: #ffffff;
            font-size: 16px;
            padding: 8px 16px;
            border-radius: 20px;
            transition: 0.3s;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #ffffff;
            color: #1e90ff;
        }

        footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <header>
                <h1>Lacak Pengaduan Anda</h1>
                <p>Masukkan ID Pengaduan atau Email Anda untuk melacak status.</p>
            </header>
            <form action="proses_lacak.php" method="GET">
                <div class="form-group">
                    <label for="pengaduan_id">Masukkan ID Pengaduan atau Email</label>
                    <input type="text" class="form-control" id="pengaduan_id" name="pengaduan_id" placeholder="ID atau Email Pengaduan" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Lacak</button>
            </form>
            <a href="index.php" class="btn btn-back">Back to Home</a>
        </div>
    </main>
</body>
</html>
