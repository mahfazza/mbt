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
    <meta charset="UTF-8" />
    <title>Form Prediksi Menstruasi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffe6f0;
            margin: 0;
            padding: 40px 0; /* beri ruang atas bawah */
            display: flex;
            justify-content: center;
            align-items: flex-start; /* ganti dari center ke flex-start */
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            margin-top: 60px; /* dorong sedikit ke bawah */
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
            text-align: left;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            box-sizing: border-box;
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
            width: 100%;
            font-weight: 600;
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

        @media (max-height: 700px) {
            body {
                align-items: flex-start;
            }

            .container {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Prediksi Menstruasi</h2>

        <form action="proses/proses_prediksi.php" method="POST" autocomplete="off">
            <label for="LengthofCycle">Panjang Siklus (hari):</label>
            <input type="number" id="LengthofCycle" name="LengthofCycle" required min="1" />

            <label for="MeanCycleLength">Rata-rata Panjang Siklus (hari):</label>
            <input type="number" id="MeanCycleLength" name="MeanCycleLength" step="0.01" required min="1" />

            <label for="EstimatedDayofOvulation">Hari Perkiraan Ovulasi:</label>
            <input type="number" id="EstimatedDayofOvulation" name="EstimatedDayofOvulation" required min="1" />

            <label for="LengthofLutealPhase">Panjang Fase Luteal (hari):</label>
            <input type="number" id="LengthofLutealPhase" name="LengthofLutealPhase" required min="1" />

            <label for="TotalDaysofFertility">Total Hari Kesuburan (hari):</label>
            <input type="number" id="TotalDaysofFertility" name="TotalDaysofFertility" required min="1" />

            <label for="LengthofMenses">Panjang Menses (hari):</label>
            <input type="number" id="LengthofMenses" name="LengthofMenses" required min="1" />

            <label for="MeanMensesLength">Rata-rata Panjang Menses (hari):</label>
            <input type="number" id="MeanMensesLength" name="MeanMensesLength" step="0.01" required min="1" />

            <label for="Age">Usia (tahun):</label>
            <input type="number" id="Age" name="Age" required min="1" max="120" />

            <label for="BMI">BMI:</label>
            <input type="number" id="BMI" name="BMI" step="0.01" required min="10" max="60" />

            <button type="submit">Prediksi</button>
        </form>

        <a href="kalender.php">Kembali ke Kalender</a>
    </div>
</body>
</html>