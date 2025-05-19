<?php
session_start();
include('koneksi.php');

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT tanggal_menstruasi FROM menstruasi WHERE user_email = ? AND status = 'setuju'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);