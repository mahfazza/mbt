<?php
session_start();
include('proses/koneksi.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['email']; // karena yang disimpan di session adalah email
$sql = "SELECT * FROM profile WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Important Schedule</title>
    <link rel="stylesheet" href="style.css">
    <!-- New: Lokal -->
    <link rel="stylesheet" href="assets/fullcalendar/dist/fullcalendar.min.css">
    <script src="assets/fullcalendar/jquery.min.js"></script>
    <script src="assets/fullcalendar/moment.js"></script>
    <script src="assets/fullcalendar/dist/fullcalendar.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <style>
        h1 {
            color: #ad1457;
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
        /* Warna latar belakang halaman */
        body {
            background-color: #ffe6f0;
            font-family: 'Quicksand', sans-serif;
            color: #333;
        }

        /* Header Judul */
        h2 {
            color: #d63384;
            text-align: center;
            margin-top: 20px;
        }

        /* Warna dan gaya box kalender */
        #calendar {
            max-width: 900px;
            margin: 40px auto;
            background-color:rgb(254, 180, 205);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgb(255, 255, 255);
        }

        /* Header kalender */
        .fc-toolbar-title {
            color: #d63384;
        }

        /* Tombol navigasi kalender */
        .fc-button {
            background-color: #f78fb3 !important;
            border: none !important;
            color: #ad1457 !important;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* Warna event */
        .fc-event {
            background-color: #ad1457 !important;
            border: none !important;
        }

        .fc-daygrid-event-dot {
            border-color: #d63384 !important;
        }
        /* Warna teks tanggal di dalam kalender */
        .fc-day-number {
            color:rgb(60, 4, 32) !important;
            font-weight: bold;
        }
        .new-schedule-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #c2185b; /* Pink tua */
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .new-schedule-button:hover {
            background-color: #ad1457; /* Pink tua lebih gelap saat hover */
        }
        #agenda-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff0f5 !important;
        box-shadow: 0 0 8px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
        margin-top: 30px;
        }

        #agenda-table thead {
            background-color: #f8bbd0 !important;
            color: #6a1b45 !important;
        }

        #agenda-table th, 
        #agenda-table td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #fce4ec !important;
        }

        #agenda-table tbody tr:nth-child(even) {
            background-color: #fdeef4 !important;
        }

        #agenda-table tbody tr:hover {
            background-color: #fce4ec !important;
            transition: 0.3s;
        }
    </style>

</head>
<body>
    <header class="header">
    <a href="choice.php" class="back-button">← Kembali</a>
        <div class="header-icons" style="position: absolute; top: 10px; right: 20px; display: flex; gap: 15px;">
            <div class="dropdown">
                <div id="profile-dropdown" class="dropdown-content">
                    <button onclick="logout()">Log Out</button>
                </div>           
            </div>
        </div>
    </header>

    <main>
        <h1>Important Schedule</h1>
        <div id="calendar"></div>

        <table id="agenda-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <a href="submission.php" class="new-schedule-button">New Schedule</a>
    </main>

    <script>
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        document.addEventListener("click", function(event) {
            let dropdown = document.getElementById("profile-dropdown");
            let profileIcon = document.querySelector(".profile-icon");

            if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });

        function logout() {
            alert("Anda telah keluar!");
            window.location.href = "login.php";
        }

        $(document).ready(function () {
            const agendaTableBody = $("#agenda-table tbody");
            agendaTableBody.html('');

            $.getJSON('proses/get_schedule.php', function (agenda) {
                if (agenda && agenda.length > 0) {
                    $('#calendar').fullCalendar({
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay,listWeek'
                        },
                        defaultView: 'month',
                        editable: false,
                        eventLimit: true,
                        events: agenda.map(event => ({
                            title: event.schedule_name,
                            start: event.date,
                            description: event.description
                        })),
                        eventClick: function (event) {
                            alert("Agenda: " + event.title + "\nDeskripsi: " + event.description);
                        }
                    });

                    agenda.forEach((event, index) => {
                        let row = `<tr>
                            <td>${index + 1}</td>
                            <td>${event.date}</td>
                            <td>${event.schedule_name}</td>
                            <td>${event.description}</td>
                        </tr>`;
                        agendaTableBody.append(row);
                    });
                }
            }).fail(function () {
                console.error("Error loading agenda data");
            });
        });
    </script>
</body>
</html>