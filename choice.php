<?php
session_start();
include 'proses/koneksi.php';
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT nama, foto FROM profile WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika tidak ada foto, gunakan default
$foto = (!empty($user['foto'])) ? $user['foto'] : 'default.png';
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Your Choice</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo {
            width: 200px;
            margin-bottom: 10px;
            display: block;
        }
        .title {
            color: #e91e63;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            align-self: flex-start;
        }
        .subtitle {
            align-self: flex-start;
            margin-top: 0;
        }
        .option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #f8bbd0;
            border-radius: 10px;
            margin: 10px 0;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            box-sizing: border-box;
        }
        .option img {
            width: 40px;
            margin-right: 10px;
        }
        .option.selected {
            border-color: #e91e63;
            background-color: #ffe3ec;
        }
        .button {
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
        .button:hover {
            background-color: #d81b60;
        }

        .logout-btn {
            background-color: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            border-radius: 50%;
        }

        .logout-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e91e63;
        }

        .logout-btn:hover {
            background-color: #f8cdd9;
        }
        .logout-btn img {
            width: 35px;
            height: 35px;
        }

        .top-right {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-pic {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e91e63;
        }

    </style>

    <script>
        function selectOption(element, route = null) {
            document.querySelectorAll('.option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');

            if (route) {
                window.location.href = route;
            } else {
                alert("Fitur ini sedang dalam pengembangan.");
            }
        }
    </script>
</head>
<body>

    <!-- Foto profil dan tombol logout -->
    <div class="top-right">
        <a href="edit_profile.php">
            <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="Foto Profil" class="profile-pic">
        </a>
        <form action="logout.php" method="post">
            <button class="logout-btn" type="submit">
                <img src="assets/ikon/logout.png" alt="Logout">
    </button>
        </form>
    </div>

    <div class="container">
        <img src="assets/ikon/logoheader.png" alt="Mom and Baby Logo" class="logo">
        <h1 class="title">Make Your Choice</h1>
        <p class="subtitle">1 goal selected</p>
        <div class="option" onclick="selectOption(this, 'cek_user_menstruasi.php')">
            <img src="assets/ikon/trackingperiod.png" alt="Track My Period">
            Pelacakan Periode Menstruasi
        </div>

        <div class="option" onclick="selectOption(this, null)">
            <img src="assets/ikon/medicalconsultation.png" alt="Pregnancy Tracking">
            Medical Consultation
        </div>
        <div class="option" onclick="selectOption(this, 'child_growth.php')">
            <img src="assets/ikon/childgrowth.png" alt="Child Growth">
            Child Growth
        </div>
        <div class="option" onclick="selectOption(this, 'pengingat.php')">
            <img src="assets/ikon/importantschedule.png" alt="important schedule">
            Important Schedule
        </div>
    </div>
</body>
</html>