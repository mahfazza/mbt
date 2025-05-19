<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];
$tanggal = $_POST['tanggal_menstruasi'];

$sql = "INSERT INTO menstruasi (user_email, tanggal_menstruasi, status) 
        VALUES (?, ?, 'setuju')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $tanggal);

if ($stmt->execute()) {
    header("Location: ../kalender.php");
    exit();
} else {
    echo "Gagal menyimpan data: " . $conn->error;
}
?>