<?php
session_start();
session_unset(); // Hapus semua data sesi
session_destroy(); // Hancurkan sesi
header('Location: login.php'); // Redirect ke login
exit();
?>
