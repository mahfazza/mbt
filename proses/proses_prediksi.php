<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil semua input dari form dengan validasi sederhana
    $LengthofCycle = isset($_POST['LengthofCycle']) ? floatval($_POST['LengthofCycle']) : null;
    $MeanCycleLength = isset($_POST['MeanCycleLength']) ? floatval($_POST['MeanCycleLength']) : null;
    $EstimatedDayofOvulation = isset($_POST['EstimatedDayofOvulation']) ? floatval($_POST['EstimatedDayofOvulation']) : null;
    $LengthofLutealPhase = isset($_POST['LengthofLutealPhase']) ? floatval($_POST['LengthofLutealPhase']) : null;
    $TotalDaysofFertility = isset($_POST['TotalDaysofFertility']) ? floatval($_POST['TotalDaysofFertility']) : null;
    $LengthofMenses = isset($_POST['LengthofMenses']) ? floatval($_POST['LengthofMenses']) : null;
    $MeanMensesLength = isset($_POST['MeanMensesLength']) ? floatval($_POST['MeanMensesLength']) : null;
    $Age = isset($_POST['Age']) ? floatval($_POST['Age']) : null;
    $BMI = isset($_POST['BMI']) ? floatval($_POST['BMI']) : null;

    $email = $_SESSION['email'];

    // Validasi semua data wajib ada
    if (
        is_null($LengthofCycle) || is_null($MeanCycleLength) || is_null($EstimatedDayofOvulation) ||
        is_null($LengthofLutealPhase) || is_null($TotalDaysofFertility) || is_null($LengthofMenses) ||
        is_null($MeanMensesLength) || is_null($Age) || is_null($BMI)
    ) {
        echo "Data tidak lengkap. Harap isi semua form.";
        exit();
    }

    $data = [
        'LengthofCycle' => $LengthofCycle,
        'MeanCycleLength' => $MeanCycleLength,
        'EstimatedDayofOvulation' => $EstimatedDayofOvulation,
        'LengthofLutealPhase' => $LengthofLutealPhase,
        'TotalDaysofFertility' => $TotalDaysofFertility,
        'LengthofMenses' => $LengthofMenses,
        'MeanMensesLength' => $MeanMensesLength,
        'Age' => $Age,
        'BMI' => $BMI
    ];

    // Kirim data ke Flask API
    $ch = curl_init('http://127.0.0.1:5000/predict');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch);
        curl_close($ch);
        exit();
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result === null) {
        echo "Gagal decode response dari API.";
        exit();
    }

    if (isset($result['prediction'])) {
        $prediction_days = intval($result['prediction']);
        $prediction_date = date('Y-m-d', strtotime("+$prediction_days days"));
        $confidence = floatval($result['confidence']);

        include 'koneksi.php';

        // Simpan data prediksi lengkap ke database sesuai tabel yang ada
        $stmt = $conn->prepare("INSERT INTO prediksi_menstruasi (user_email, cycle_length, menstruation_duration, tanggal_prediksi, confidence) VALUES (?, ?, ?, ?, ?)");

        // Karena di form dan data tidak ada menstruation_duration, kasih default 0 atau null jika boleh
        $menstruation_duration = 0;

        $stmt->bind_param("siisd", $email, $LengthofCycle, $menstruation_duration, $prediction_date, $confidence);

        if ($stmt->execute()) {
    // Simpan info prediksi ke sesi jika perlu
    $_SESSION['prediksi_sukses'] = "Prediksi berhasil: $prediction_date (Confidence: " . round($confidence, 2) . "%)";
    header("Location: ../kalender.php");
    exit();
} else {
    echo "Gagal menyimpan prediksi ke database: " . $stmt->error;
}

        $stmt->close();
        $conn->close();
    } else {
        echo "Terjadi kesalahan dari API: " . (isset($result['error']) ? $result['error'] : 'Tidak ada detail error');
    }
} else {
    echo "Metode request tidak valid.";
}
?>