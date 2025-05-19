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
    <title>Input Tanggal Menstruasi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffe6f0;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #e60073;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        button {
            background-color: #ff3385;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #e60073;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #e60073;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Input Tanggal Menstruasi</h2>
        
        <form action="proses/proses_input.php" method="POST">
            <label for="tanggal_menstruasi">Tanggal Menstruasi Pertama:</label>
            <input type="date" id="tanggal_menstruasi" name="tanggal_menstruasi" required>

            <button type="submit">Simpan</button>
        </form>

        <a href="kalender.php">← Kembali ke Kalender</a>
    </div>
</body>
</html>