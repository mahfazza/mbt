<?php
session_start();
include('proses/koneksi.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Menstruasi</title>
    <link rel="stylesheet" href="assets/fullcalendar/dist/fullcalendar.min.css">
    <script src="assets/fullcalendar/jquery.min.js"></script>
    <script src="assets/fullcalendar/moment.js"></script>
    <script src="assets/fullcalendar/dist/fullcalendar.min.js"></script>
    <style>
        body {
            background-color: #fff0f5;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffe6f0;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #e60073;
            text-align: center;
            margin-bottom: 20px;
        }

        #calendar {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
        }

        .fc-event {
            border: none !important;
            color: white !important;
            font-weight: bold;
        }

        .btn-menstruasi {
            display: block;
            width: fit-content;
            margin: 20px auto;
            background-color: #ff3385;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
            text-align: center;
        }

        .btn-menstruasi:hover {
            background-color: #e60073;
        }

        .catatan-section {
            margin-top: 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8d7da;
            color: #d81b60;
            padding: 10px;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #fce4ec;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Kalender Menstruasi</h2>

    <div id="calendar"></div>

    <a href="form_input.php" class="btn-menstruasi">Tanggal Menstruasi</a>

    <!-- Catatan Harian -->
    <div class="catatan-section">
        <h3>Catatan Harian Menstruasi</h3>
        <?php
        $query = "SELECT tanggal, suasana_hati, gejala, keputihan, suhu_tubuh FROM catatan_menstruasi WHERE user_email = ? ORDER BY tanggal DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Tanggal</th>
                    <th>Suasana Hati</th>
                    <th>Gejala</th>
                    <th>Keputihan</th>
                    <th>Suhu Tubuh (°C)</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['suasana_hati']) ?></td>
                        <td><?= htmlspecialchars($row['gejala']) ?></td>
                        <td><?= htmlspecialchars($row['keputihan']) ?></td>
                        <td><?= htmlspecialchars($row['suhu_tubuh']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Tidak ada catatan harian yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function () {
    const events = [];

    // Prediksi
    $.getJSON('proses/get_prediksi.php', function (dataPrediksi) {
        dataPrediksi.forEach(item => {
            events.push({
                title: "Prediksi",
                start: item.tanggal_prediksi,
                allDay: true,
                color: '#ff80ab'
            });
        });

        // Data aktual
        $.getJSON('proses/get_menstruasi.php', function (dataAktual) {
            dataAktual.forEach(item => {
                const startDate = moment(item.tanggal_menstruasi);
                for (let i = 0; i < 7; i++) {
                    events.push({
                        title: "Menstruasi Hari ke-" + (i + 1),
                        start: startDate.clone().add(i, 'days').format('YYYY-MM-DD'),
                        allDay: true,
                        color: '#d81b60'
                    });
                }
            });

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek'
                },
                editable: false,
                eventLimit: true,
                events: events,
                dayClick: function(date) {
                    const selectedDate = date.format('YYYY-MM-DD');
                    window.location.href = 'form_catatan.php?tanggal=' + selectedDate;
                }
            });
        });
    });
});
</script>
</body>
</html>