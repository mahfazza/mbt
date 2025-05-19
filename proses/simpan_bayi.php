<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$nama = $_POST['nama'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$jenis_kelamin = $_POST['jenis_kelamin'];

$stmt = $conn->prepare("INSERT INTO bayi (nama, tanggal_lahir, jenis_kelamin, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $tanggal_lahir, $jenis_kelamin, $email);
$stmt->execute();

echo "Data bayi berhasil disimpan!";