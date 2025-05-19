<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, nama, tanggal_lahir, jenis_kelamin FROM bayi WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$bayi = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bayi) {
    header("Location: ../form_bayi.php");
    exit();
}

$lahir = new DateTime($bayi['tanggal_lahir']);
$now = new DateTime();
$diff = $now->diff($lahir);
$usia_bulan = $diff->y * 12 + $diff->m;

$berat = floatval($_POST['berat_badan']);
$tinggi = floatval($_POST['tinggi_badan']);
$lingkar = floatval($_POST['lingkar_kepala']);
$tanggal_input = date('Y-m-d');

$stmt = $conn->prepare("INSERT INTO pertumbuhan_bayi 
    (id_bayi, tanggal_pengukuran, usia_bulan, berat_badan, tinggi_badan, lingkar_kepala)
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isiddd", $bayi['id'], $tanggal_input, $usia_bulan, $berat, $tinggi, $lingkar);
$stmt->execute();
$stmt->close();

$payload = json_encode([
    'usia' => $usia_bulan,
    'berat_badan' => $berat,
    'tinggi_badan' => $tinggi,
    'lingkar_kepala' => $lingkar,
    'jenis_kelamin' => $bayi['jenis_kelamin']
]);

$opts = ['http' => [
    'header' => "Content-Type: application/json\r\n",
    'method' => 'POST',
    'content' => $payload
]];

$ctx = stream_context_create($opts);
$res = @file_get_contents('http://localhost:3000/analyze', false, $ctx);
$knn = json_decode($res, true) ?? ['kategori' => 'Error analisis'];

// Ambil riwayat pertumbuhan untuk grafik
$data = [];
$stmt = $conn->prepare("SELECT usia_bulan, berat_badan, tinggi_badan, lingkar_kepala FROM pertumbuhan_bayi WHERE id_bayi = ? ORDER BY usia_bulan ASC");
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
$headCircs = array_map(fn($d) => $d['lingkar_kepala'], $data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Analisis Pertumbuhan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background:#ffe3ec; padding:20px; }
    .card { background:#fff; padding:20px; border-radius:10px; max-width:600px; margin:auto; position:relative; }
    h2 { color:#e91e63; }
    .field { margin:10px 0; }
    .field strong { display:inline-block; width:140px; }
    .btn {
      display:inline-block;
      margin-top:20px;
      padding:10px 20px;
      background:#e91e63;
      color:#fff;
      text-decoration:none;
      border-radius:5px;
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
    canvas { margin-top: 30px; }
  </style>
</head>
<body>
<a href="../form_growth.php" class="back-button">← Kembali</a>
  <div class="card">
    <h2>Hasil Analisis Pertumbuhan</h2>
    <div class="field"><strong>Nama Bayi:</strong> <?= htmlspecialchars($bayi['nama']) ?></div>
    <div class="field"><strong>Usia:</strong> <?= $usia_bulan ?> bulan</div>
    <div class="field"><strong>Berat:</strong> <?= $berat ?> kg</div>
    <div class="field"><strong>Tinggi:</strong> <?= $tinggi ?> cm</div>
    <div class="field"><strong>Lingkar Kepala:</strong> <?= $lingkar ?> cm</div>
    <div class="field"><strong>Kategori:</strong> <?= htmlspecialchars($knn['kategori']) ?></div>
    <canvas id="growthChart" width="400" height="200"></canvas>
    <a href="../form_growth.php" class="btn">Ulangi Analisis</a>
  </div>

  <script>
  const ctx = document.getElementById('growthChart').getContext('2d');
  const growthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels) ?>,
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
          data: <?= json_encode($headCircs) ?>,
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
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Grafik Pertumbuhan Bayi',
          font: {
            size: 18,
            weight: 'bold'
          },
          color: '#e91e63'
        },
        legend: {
          labels: {
            color: '#444',
            font: {
              size: 14
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Nilai',
            color: '#e91e63',
            font: {
              weight: 'bold'
            }
          },
          ticks: {
            color: '#555'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Usia (bulan)',
            color: '#e91e63',
            font: {
              weight: 'bold'
            }
          },
          ticks: {
            color: '#555'
          }
        }
      }
    }
  });
</script>