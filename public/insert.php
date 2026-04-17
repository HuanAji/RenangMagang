<?php
// === PANGGIL FILE KONEKSI DB ===
include 'db.php';

// Cek Content-Type
$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

// === MODE JSON ===
if (stripos($contentType, "application/json") !== false) {
    $raw = file_get_contents("php://input");
    $json = json_decode($raw, true);

    if (!$json) {
        echo "❌ JSON tidak terbaca.";
        exit;
    }

    foreach ($json as $playerKey => $playerData) {
        if (isset($playerData['waktu_ms'], $playerData['waktu_detik'], $playerData['waktu_menit'], $playerData['waktu_format'])) {
            // Ambil nomor player dari key "player1", "player2", dst
            preg_match('/player(\d+)/', $playerKey, $matches);
            $playerNum = $matches[1] ?? $playerKey;

            $ms     = $playerData['waktu_ms'];
            $detik  = $playerData['waktu_detik'];
            $menit  = $playerData['waktu_menit'];
            $format = $playerData['waktu_format'];

            $stmt = $conn->prepare("INSERT INTO hasil_lomba 
                (player, waktu_ms, waktu_detik, waktu_menit, waktu_format) 
                VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sidds", $playerNum, $ms, $detik, $menit, $format);
                if (!$stmt->execute()) {
                    error_log("Insert JSON gagal: " . $stmt->error);
                    echo "❌ Insert JSON gagal: " . $stmt->error;
                }
                $stmt->close();
            } else {
                error_log("Prepare JSON gagal: " . $conn->error);
                echo "❌ Prepare JSON gagal: " . $conn->error;
            }
        }
    }

    echo "✅ Data JSON berhasil disimpan.";
    exit;
}

// === MODE FORM-DATA (per player) ===
$player       = $_POST['player']       ?? '';
$waktu_ms     = $_POST['waktu_ms']     ?? 0;
$waktu_detik  = $_POST['waktu_detik']  ?? 0;
$waktu_menit  = $_POST['waktu_menit']  ?? 0;
$waktu_format = $_POST['waktu_format'] ?? '';

if ($player === '' || $waktu_format === '') {
    echo "❌ Data tidak lengkap.";
    exit;
}

$sql = "INSERT INTO hasil_lomba 
    (player, waktu_ms, waktu_detik, waktu_menit, waktu_format) 
    VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sidds", $player, $waktu_ms, $waktu_detik, $waktu_menit, $waktu_format);
    if ($stmt->execute()) {
        echo "✅ Data berhasil disimpan.";
    } else {
        echo "❌ Gagal eksekusi query: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "❌ Gagal prepare statement: " . $conn->error;
}

$conn->close();
?>
