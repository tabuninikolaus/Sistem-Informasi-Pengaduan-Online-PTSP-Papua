<?php
session_start();
include 'config/db.php'; // Koneksi ke database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengecek user
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {
            header('Location: dashboard_pengaduan.php');
        } elseif ($user['role'] == 'perizinan') {
            header('Location: dashboard_perizinan.php');
        } elseif ($user['role'] == 'nonperizinan') {
            header('Location: dashboard_nonperizinan.php');
        }
    } else {
        // Tampilkan pesan kesalahan dengan JavaScript alert
        echo "<script>
                alert('Username atau Password salah!');
                window.location.href = 'login.php'; // Kembali ke halaman login
              </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* CSS untuk mengatur latar belakang dan layout */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff; /* Warna latar belakang biru muda */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container login */
        .login-container {
            background-color: #ffffff; /* Putih untuk kontras */
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Maksimal lebar */
            text-align: center;
        }

        /* Heading */
        h2 {
            color: #4fa3d1; /* Biru muda */
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Input field */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4fa3d1; /* Warna biru muda saat fokus */
            outline: none;
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background-color: #4fa3d1; /* Warna biru muda */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3b8cbf; /* Efek hover biru lebih gelap */
        }

        /* Error message */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
