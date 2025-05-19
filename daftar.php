<?php
include 'proses/koneksi.php';

$error = '';
$success = '';
$nama = $email = ''; // default value

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $foto = $_FILES['foto'];
    $fotoName = '';
    $upload_dir = 'uploads/';

    // Cek konfirmasi password
    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error = "Password harus mengandung huruf besar, kecil, angka & simbol, minimal 8 karakter.";
    } else {
        // Cek apakah email sudah terdaftar
        $cek = $conn->prepare("SELECT email FROM profile WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Validasi dan upload foto
            if ($foto['error'] === 0) {
                $allowedExt = ['jpg', 'jpeg', 'png'];
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $allowedExt)) {
                    $fotoName = uniqid() . '.' . $ext;
                    $target = $upload_dir . $fotoName;

                    if (!move_uploaded_file($foto['tmp_name'], $target)) {
                        $error = "Gagal mengunggah foto.";
                    }
                } else {
                    $error = "Format foto harus JPG, JPEG, atau PNG.";
                }
            } else {
                $error = "Mohon unggah foto profil.";
            }

            // Simpan ke database
            if ($error === '') {
                $stmt = $conn->prepare("INSERT INTO profile (nama, email, password, foto) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nama, $email, $password, $fotoName);

                if ($stmt->execute()) {
                    echo "<script>
                        window.onload = function() {
                            document.getElementById('toast').classList.add('show');
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 3000);
                        }
                    </script>";
                } else {
                    $error = "Pendaftaran gagal. Coba lagi.";
                }

                $stmt->close();
            }
        }

        $cek->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Halaman Daftar</title>
    <style>
        body, textarea, label, h1, h2, h3, h4, h5, h6, p, small {
            font-family: 'Times New Roman';
        }

        a {
            font-family: 'Times New Roman', Times, serif;
        }

        button, .btn {
            font-family: 'Quicksand', sans-serif;
        }
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
        input[type="file"] {
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
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
        .msg {
            margin-top: 10px;
            font-size: 14px;
        }
        .error { color: red; }
        .success { color:rgb(139, 216, 255); }
    </style>
</head>
<body>
    <a href="landing_page.html" class="back-button">← Kembali</a>
    <div class="container">
        <img src="assets/ikon/logoheader.png" alt="Mom and Baby Logo" class="logo">
        <h2 style="color:#e91e63;">Form Pendaftaran</h2>
        <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="nama">Nama</label>
            <input type="text" name="nama" required value="<?= htmlspecialchars($nama) ?>">

            <label for="email">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">

            <label for="password">Kata Sandi</label>
            <input type="password" name="password" id="password" required>

            <label for="konfirmasi">Konfirmasi Sandi</label>
            <input type="password" name="konfirmasi" required>

            <small>* Password minimal 8 karakter, kombinasi huruf besar, kecil, angka dan simbol.</small>

            <label for="foto">Foto Profil</label>
            <input type="file" name="foto" accept="image/*" required>

            <button class="btn" type="submit">Daftar</button>
        </form>
        <p class="toggle-link">
            <a href="login.php" style="text-decoration: none; color: #e91e63;">Sudah Punya Akun? Login</a>
        </p>

        <?php if ($error): ?>
            <p class="msg error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="msg success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </div>

    <div id="toast">🎉 Registrasi berhasil! Silakan login...</div>

    <script>
    function validateForm() {
        const password = document.getElementById("password").value;
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!strongRegex.test(password)) {
            alert("Password harus mengandung huruf besar, kecil, angka, simbol dan minimal 8 karakter.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>