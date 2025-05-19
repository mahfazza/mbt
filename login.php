<?php
session_start();
include 'proses/koneksi.php';

$error = ''; // Variabel untuk menyimpan pesan kesalahan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user dari database
    $stmt = $conn->prepare("SELECT * FROM profile WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password (langsung dibandingkan dengan tanggal lahir atau yang disimpan)
        if ($password === $user['password']) {
            $_SESSION['email'] = $email;
            header("Location: choice.php"); // Redirect ke halaman dashboard
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Masuk</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #ffe3ec;
            flex-direction: column;
            width: 100vw;
        }
        .container {
            width: 90%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: left;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex; /* Pastikan terlihat */
            flex-direction: column;
            align-items: center;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color:rgb(229, 44, 115);
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #d81b60;
        }
        .option img {
            width: 40px;
            margin-right: 10px;
        }
        .logo {
            width: 200px;
            margin-bottom: 10px;
            display: block;
        }
        h2 {
            color: #c42158;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-right: 60px;
            border: 1px solid #eee;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            background-color: #e91e63;
            color: white;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #d81b60;
        }
        .toggle-link {
            margin-top: 10px;
            cursor: pointer;
            color: #c42158;
            text-decoration: none;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <a href="landing_page.html" class="back-button">← Kembali</a>
    <div class="container">
        <img src="assets/ikon/logoheader.png" alt="Mom and Baby Logo" class="logo">
        <h2>Masuk</h2>
        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="email@gmail.com" required>
            
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="********" required>
            
            <button class="btn" type="submit">Masuk</button>
        </form>
        <p class="toggle-link">
            <a href="daftar.php" style="text-decoration: none; color: #e91e63;">Belum Punya Akun? Daftar</a>
        </p>

        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>