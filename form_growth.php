<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'proses/koneksi.php';

$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM bayi WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$data_bayi = $result->fetch_assoc();

if (!$data_bayi) {
    header("Location: form_bayi.php");
    exit();
}

// Hitung usia dalam bulan
$tanggal_lahir = new DateTime($data_bayi['tanggal_lahir']);
$sekarang = new DateTime();
$selisih = $sekarang->diff($tanggal_lahir);
$usia_bulan = ($selisih->y * 12) + $selisih->m;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pertumbuhan Bayi</title>
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
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 450px;
        }
        h2 {
            text-align: center;
            color: #e91e63;
            margin-bottom: 10px;
        }
        .info-bayi {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #555;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #e91e63;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #d81b60;
        }
        .field-display label {
          display: block;
          margin-top: 15px;
          font-weight: bold;
        }

      .display-box {
          width: 100%;
          padding: 10px;
          margin-top: 5px;
          border-radius: 5px;
          background-color: #f9f9f9;
          border: 1px solid #ddd;
          font-size: 16px;
          color: #333;
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
<a href="child_growth.php" class="back-button">← Kembali</a>
    <div class="form-container">
        <h2>Input Pertumbuhan Bayi</h2>
                <div class="field-display">
            <label>Nama Bayi:</label>
            <div class="display-box"><?= htmlspecialchars($data_bayi['nama']) ?></div>

            <label>Usia Bayi:</label>
            <div class="display-box"><?= $usia_bulan ?> bulan</div>
        </div>
        <form action="proses/analyze_growth.php" method="POST">
            <input type="hidden" name="usia_bulan" value="<?= $usia_bulan ?>">
            <label>Berat Badan (kg):</label>
            <input type="number" name="berat_badan" step="0.01" required>
            <label>Tinggi Badan (cm):</label>
            <input type="number" name="tinggi_badan" step="0.1" required>
            <label>Lingkar Kepala (cm):</label>
            <input type="number" name="lingkar_kepala" step="0.1" required>
            <button type="submit">Analisis Pertumbuhan</button>
        </form>
    </div>
</body>
</html>