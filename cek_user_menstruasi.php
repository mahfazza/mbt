<?php
session_start();
include('proses/koneksi.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Cek apakah user sudah pernah input prediksi
$query = "SELECT COUNT(*) AS total FROM prediksi_menstruasi WHERE user_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data['total'] > 0) {
    // User lama → langsung ke kalender
    header("Location: kalender.php");
} else {
    // User baru → ke form prediksi
    header("Location: form_prediksi.php");
}
exit();