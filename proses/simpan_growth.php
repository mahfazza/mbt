<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];

// Ambil ID bayi dari email
$query = $conn->prepare("SELECT id FROM bayi WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$data_bayi = $result->fetch_assoc();

if (!$data_bayi) {
    echo "Data bayi tidak ditemukan.";
    exit();
}

$id_bayi = $data_bayi['id'];

// Ambil input
$berat = $_POST['berat'];
$tinggi = $_POST['tinggi'];
$lingkar_kepala = $_POST['lingkar_kepala'];
$tanggal = date('Y-m-d');

// Hitung usia bulan
$tanggal_lahir = new DateTime($data_bayi['tanggal_lahir']);
$sekarang = new DateTime();
$selisih = $sekarang->diff($tanggal_lahir);
$usia_bulan = ($selisih->y * 12) + $selisih->m;

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO pertumbuhan_bayi (id_bayi, tanggal_pengukuran, usia_bulan, berat_badan, tinggi_badan, lingkar_kepala) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isiddd", $id_bayi, $tanggal, $usia_bulan, $berat, $tinggi, $lingkar_kepala);
$stmt->execute();

// Simpan ke session (bisa juga pakai redirect GET param)
$_SESSION['analisis_data'] = [
    'usia_bulan' => $usia_bulan,
    'berat' => $berat,
    'tinggi' => $tinggi,
    'lingkar_kepala' => $lingkar_kepala
];

// Redirect ke analisis
header("Location: ../analyze_growth.php");
exit();
?>