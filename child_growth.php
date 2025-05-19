<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'proses/koneksi.php';

// Ambil email dari session
$email = $_SESSION['email'];

// Cek apakah ada data bayi untuk user ini
$query = $conn->prepare("SELECT * FROM bayi WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$data_bayi = $result->fetch_assoc();

if (!$data_bayi) {
    // Jika belum ada data bayi, arahkan ke form input
    header("Location: form_bayi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Child Growth</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe3ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            color: #e91e63;
            margin-bottom: 20px;
        }
        .baby-info {
            text-align: left;
            margin-bottom: 20px;
        }
        .baby-info p {
            margin: 8px 0;
        }
        .btn {
            display: inline-block;
            margin: 8px 5px;
            padding: 8px 18px;
            background-color: #e91e63;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #d81b60;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color: #ad1457;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #c2185b;
        }
    </style>
</head>
<body>
<a href="choice.php" class="back-button">← Kembali</a>
<div class="container">
    <h2>Data Bayi Anda</h2>
    <div class="baby-info">
        <p><strong>Nama:</strong> <?= htmlspecialchars($data_bayi['nama']) ?></p>
        <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($data_bayi['tanggal_lahir']) ?></p>
        <p><strong>Jenis Kelamin:</strong> <?= $data_bayi['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
    </div>
    <a href="form_growth.php" class="btn">Analisis Pertumbuhan Bayi</a>
    <a href="history_growth.php" class="btn">Lihat Riwayat Pertumbuhan</a>
</div>
</body>
</html>