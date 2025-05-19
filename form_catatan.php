<?php
session_start();
include('proses/koneksi.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suasana_hati = $_POST['suasana_hati'];
    $gejala = $_POST['gejala'];
    $keputihan = $_POST['keputihan'];
    $suhu_tubuh = $_POST['suhu_tubuh'];

    // Cek apakah data sudah ada untuk tanggal tersebut
    $stmt = $conn->prepare("SELECT id FROM catatan_menstruasi WHERE user_email = ? AND tanggal = ?");
    $stmt->bind_param("ss", $email, $tanggal);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE catatan_menstruasi SET suasana_hati=?, gejala=?, keputihan=?, suhu_tubuh=?, updated_at=NOW() WHERE user_email=? AND tanggal=?");
        $stmt->bind_param("ssssss", $suasana_hati, $gejala, $keputihan, $suhu_tubuh, $email, $tanggal);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO catatan_menstruasi (user_email, tanggal, suasana_hati, gejala, keputihan, suhu_tubuh) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $tanggal, $suasana_hati, $gejala, $keputihan, $suhu_tubuh);
    }

    if ($stmt->execute()) {
        header("Location: kalender.php");
        exit();
    } else {
        $error = "Gagal menyimpan data!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Catatan Harian</title>
    <style>
        body {
            background-color: #fff0f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #ffe6f0;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #d81b60;
        }

        label {
            font-weight: bold;
        }

        input[type="text"], input[type="date"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            background-color: #d81b60;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #b30047;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #333;
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Catatan Harian - <?= htmlspecialchars($tanggal) ?></h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <label for="suasana_hati">Suasana Hati</label>
        <select name="suasana_hati" required>
            <option value="">-- Pilih --</option>
            <option value="senang">Senang</option>
            <option value="sedih">Sedih</option>
            <option value="marah">Marah</option>
            <option value="normal">Normal</option>
            <option value="lelah">Lelah</option>
            <option value="cemas">Cemas</option>
        </select>

        <label for="gejala">Gejala</label>
        <textarea name="gejala" rows="3" placeholder="Contoh: kram perut, sakit kepala, dll..."></textarea>

        <label for="keputihan">Keputihan</label>
        <input type="text" name="keputihan" placeholder="Contoh: putih kental, bening, dll...">

        <label for="suhu_tubuh">Suhu Tubuh (°C)</label>
        <input type="number" name="suhu_tubuh" step="0.1" min="35" max="42" placeholder="Contoh: 36.5">

        <button type="submit">Simpan Catatan</button>
    </form>

    <a class="back-link" href="kalender.php">← Kembali ke Kalender</a>
</div>
</body>
</html>