<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Data Bayi</title>
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
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #e91e63;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, select {
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
        <h2>Input Data Bayi</h2>
        <form action="proses/simpan_bayi.php" method="POST">
            <label for="nama">Nama Bayi:</label>
            <input type="text" name="nama" id="nama" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" required>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>

            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>