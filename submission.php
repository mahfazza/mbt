<?php
session_start();
include('proses/koneksi.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Proses form ketika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    if (empty($judul) || empty($tanggal) || empty($deskripsi)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        $email = $_SESSION['email'];
        $stmt = $conn->prepare("INSERT INTO pengingat (judul, date, deskripsi, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $judul, $tanggal, $deskripsi, $email);

        if ($stmt->execute()) {
            $success = "Jadwal berhasil ditambahkan!";
        } else {
            $error = "Gagal menyimpan jadwal.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #d63384;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            background-color: #e91e63;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
        .error {
            color: red;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #e91e63;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Jadwal Baru</h2>
        <?php if ($success): ?>
            <p class="message"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="judul">Judul</label>
            <input type="text" name="judul" id="judul" required>

            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" required>

            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" required></textarea>

            <button type="submit">Simpan Jadwal</button>
        </form>
        <a href="pengingat.php" class="back-link">← Kembali ke Kalender</a>
    </div>
</body>
</html>
