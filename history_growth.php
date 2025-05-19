<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'proses/koneksi.php';

$email = $_SESSION['email'];

// Ambil data bayi
$stmt = $conn->prepare("SELECT id, nama FROM bayi WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$bayi = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bayi) {
    header("Location: form_bayi.php");
    exit();
}

// Ambil data pertumbuhan
$data = [];
$stmt = $conn->prepare("SELECT usia_bulan, berat_badan, tinggi_badan, lingkar_kepala, tanggal_pengukuran FROM pertumbuhan_bayi WHERE id_bayi = ? ORDER BY usia_bulan ASC");
$stmt->bind_param("i", $bayi['id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt->close();

$labels = array_map(fn($d) => $d['usia_bulan'] . ' bln', $data);
$weights = array_map(fn($d) => $d['berat_badan'], $data);
$heights = array_map(fn($d) => $d['tinggi_badan'], $data);
$heads = array_map(fn($d) => $d['lingkar_kepala'], $data);
$tanggal = array_map(fn($d) => $d['tanggal_pengukuran'], $data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pertumbuhan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffe3ec;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #e91e63;
            text-align: center;
        }
        canvas {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #fce4ec;
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
    </style>
</head>
<body>
<a href="child_growth.php" class="back-button">← Kembali</a>
<div class="container">
    <h2>Riwayat Pertumbuhan <?= htmlspecialchars($bayi['nama']) ?></h2>

    <?php if (count($data) > 0): ?>
        <canvas id="combinedChart" width="600" height="300"></canvas>
        <canvas id="weightChart" width="600" height="250"></canvas>
        <canvas id="heightChart" width="600" height="250"></canvas>
        <canvas id="headChart" width="600" height="250"></canvas>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Usia (bulan)</th>
                    <th>Berat Badan (kg)</th>
                    <th>Tinggi Badan (cm)</th>
                    <th>Lingkar Kepala (cm)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal_pengukuran']) ?></td>
                    <td><?= $row['usia_bulan'] ?></td>
                    <td><?= $row['berat_badan'] ?></td>
                    <td><?= $row['tinggi_badan'] ?></td>
                    <td><?= $row['lingkar_kepala'] ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: #999;">Belum ada data pertumbuhan.</p>
    <?php endif; ?>
</div>

<?php if (count($data) > 0): ?>
    <script>
    const labels = <?= json_encode($labels) ?>;

    const combinedCtx = document.getElementById('combinedChart').getContext('2d');
    const combinedChart = new Chart(combinedCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: <?= json_encode($weights) ?>,
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.15)',
                    pointBackgroundColor: '#e91e63',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: <?= json_encode($heights) ?>,
                    borderColor: '#ba68c8',
                    backgroundColor: 'rgba(186, 104, 200, 0.1)',
                    pointBackgroundColor: '#ba68c8',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Lingkar Kepala (cm)',
                    data: <?= json_encode($heads) ?>,
                    borderColor: '#f06292',
                    backgroundColor: 'rgba(240, 98, 146, 0.1)',
                    pointBackgroundColor: '#f06292',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Gabungan Pertumbuhan Bayi',
                    color: '#e91e63',
                    font: { size: 18 }
                }
            }
        }
    });

    const makeChart = (id, label, data, borderColor, bgColor) => {
        return new Chart(document.getElementById(id).getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: borderColor,
                    backgroundColor: bgColor,
                    pointBackgroundColor: borderColor,
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: label,
                        color: borderColor,
                        font: { size: 16 }
                    }
                }
            }
        });
    };

    makeChart(
        'weightChart',
        'Berat Badan (kg)',
        <?= json_encode($weights) ?>,
        '#e91e63',
        'rgba(233, 30, 99, 0.15)'
    );
    makeChart(
        'heightChart',
        'Tinggi Badan (cm)',
        <?= json_encode($heights) ?>,
        '#ba68c8',
        'rgba(186, 104, 200, 0.1)'
    );
    makeChart(
        'headChart',
        'Lingkar Kepala (cm)',
        <?= json_encode($heads) ?>,
        '#f06292',
        'rgba(240, 98, 146, 0.1)'
    );
</script>
<?php endif; ?>
</body>
</html>
