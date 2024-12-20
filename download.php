<?php
include 'config/db.php';

// Mendapatkan nama file dari URL
$file = $_GET['file'];

// Cek jika file ada
$file_path = 'uploads/' . $file;
