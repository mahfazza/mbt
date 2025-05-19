<?php
session_start();
include('koneksi.php');

$email = $_SESSION['email'];
$result = $conn->query("SELECT tanggal_prediksi FROM prediksi_menstruasi WHERE user_email = '$email' ORDER BY created_at DESC");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = ['tanggal_prediksi' => $row['tanggal_prediksi']];
}

header('Content-Type: application/json');
echo json_encode($data);
?>