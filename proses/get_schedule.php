<?php
session_start();
include('koneksi.php');
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode([]); // kosongkan jika belum login
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM pengingat WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'schedule_name' => $row['judul'],
        'date' => $row['date'],
        'description' => $row['deskripsi']
    ];
}

echo json_encode($data);
?>