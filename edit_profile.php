<?php
session_start();
include 'proses/koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email_lama = $_SESSION['email'];

$stmt = $conn->prepare("SELECT nama, email, password, foto FROM profile WHERE email = ?");
$stmt->bind_param("s", $email_lama);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$nama = $user['nama'];
$email = $user['email'];
$password = $user['password'];
$foto = $user['foto'] ?? 'default.png';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaBaru = $_POST['nama'];
    $emailBaru = $_POST['email'];
    $passwordBaru = $_POST['password'];
    $fotoBaru = $_FILES['foto'];
    $fotoName = $foto;

    if ($fotoBaru['error'] == 0) {
        $ext = pathinfo($fotoBaru['name'], PATHINFO_EXTENSION);
        $fotoName = uniqid() . '.' . $ext;
        move_uploaded_file($fotoBaru['tmp_name'], 'uploads/' . $fotoName);
    }

    $update = $conn->prepare("UPDATE profile SET nama = ?, email = ?, password = ?, foto = ? WHERE email = ?");
    $update->bind_param("sssss", $namaBaru, $emailBaru, $passwordBaru, $fotoName, $email_lama);

    if ($update->execute()) {
        $_SESSION['email'] = $emailBaru;
        header("Location: choice.php");
        exit();
    } else {
        $error = "Gagal memperbarui profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffe3ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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

        .card {
            background: white;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 400px;
        }

        h2 {
            color: #ad1457;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            font-size: 13px; /* diperkecil sedikit */
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }


        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            background-color: #e91e63;
            color: white;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c2185b;
        }

        .profile-pic {
            display: block;
            margin: 0 auto 15px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e91e63;
        }

        .msg {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <a href="choice.php" class="back-button">← Kembali</a>
    <div class="card">
        <h2>Edit Profil</h2>
        <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="Foto Profil" class="profile-pic">

        <form method="post" enctype="multipart/form-data">
            <label for="nama">Nama</label>
            <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($nama) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" value="<?= htmlspecialchars($password) ?>" required>

            <label for="foto">Foto Profil</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <button type="submit">Simpan</button>
        </form>

        <?php if ($error): ?>
            <p class="msg error"><?= $error ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="msg success"><?= $success ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
