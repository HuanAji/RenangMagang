<?php
header('Content-Type: application/json');

date_default_timezone_set('Asia/Jakarta');

// ============================
// KONFIGURASI DATABASE
// ============================
$dbHost = 'localhost';
$dbName = 'nama_database_anda';
$dbUser = 'username_database';
$dbPass = 'password_database';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal',
        'error' => $e->getMessage()
    ]);
    exit;
}

// ============================
// BACA JSON INPUT
// ============================
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON tidak valid'
    ]);
    exit;
}

$type = $data['type'] ?? null;
$race = $data['race'] ?? null;

if (!$type || !$race) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Field type atau race tidak ada'
    ]);
    exit;
}

$eventNumber = trim($race['event_number'] ?? '');
$eventName   = trim($race['event_name'] ?? '');
$gender      = trim($race['gender'] ?? '');
$heat        = trim($race['heat'] ?? '');

if ($eventNumber === '' || $gender === '' || $heat === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'event_number, gender, dan heat wajib diisi'
    ]);
    exit;
}

// ============================
// HELPER
// ============================
function findOrCreateRace(PDO $pdo, string $eventNumber, string $eventName, string $gender, string $heat): int
{
    $stmt = $pdo->prepare("
        SELECT id
        FROM race_sessions
        WHERE event_number = :event_number
          AND gender = :gender
          AND heat = :heat
        LIMIT 1
    ");
    $stmt->execute([
        ':event_number' => $eventNumber,
        ':gender' => $gender,
        ':heat' => $heat
    ]);

    $row = $stmt->fetch();
    if ($row) {
        return (int)$row['id'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO race_sessions (
            event_number,
            event_name,
            gender,
            heat,
            status,
            created_at,
            updated_at
        ) VALUES (
            :event_number,
            :event_name,
            :gender,
            :heat,
            'pending',
            NOW(),
            NOW()
        )
    ");
    $stmt->execute([
        ':event_number' => $eventNumber,
        ':event_name' => $eventName,
        ':gender' => $gender,
        ':heat' => $heat
    ]);

    return (int)$pdo->lastInsertId();
}

function jsonResponse(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    $pdo->beginTransaction();

    $raceId = findOrCreateRace($pdo, $eventNumber, $eventName, $gender, $heat);

    // ============================
    // EVENT: START
    // ============================
    if ($type === 'start') {
        $deviceMillis = isset($data['device_millis']) ? (int)$data['device_millis'] : 0;

        $stmt = $pdo->prepare("
            UPDATE race_sessions
            SET
                event_name = :event_name,
                status = 'running',
                started_at = NOW(),
                device_start_millis = :device_start_millis,
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':event_name' => $eventName,
            ':device_start_millis' => $deviceMillis,
            ':id' => $raceId
        ]);

        $pdo->commit();

        jsonResponse(200, [
            'success' => true,
            'message' => 'Race start diterima',
            'race_id' => $raceId,
            'type' => 'start'
        ]);
    }

    // ============================
    // EVENT: FINISH
    // ============================
    if ($type === 'finish') {
        $lane = isset($data['lane']) ? (int)$data['lane'] : 0;
        $waktuMs = isset($data['waktu_ms']) ? (int)$data['waktu_ms'] : 0;
        $waktuDetikTotal = isset($data['waktu_detik_total']) ? (float)$data['waktu_detik_total'] : 0;
        $waktuMenitTotal = isset($data['waktu_menit_total']) ? (float)$data['waktu_menit_total'] : 0;
        $waktuFormat = trim($data['waktu_format'] ?? '');

        if ($lane < 1 || $lane > 8) {
            $pdo->rollBack();
            jsonResponse(400, [
                'success' => false,
                'message' => 'Lane harus 1 sampai 8'
            ]);
        }

        $stmt = $pdo->prepare("
            SELECT id
            FROM race_results
            WHERE race_id = :race_id AND lane = :lane
            LIMIT 1
        ");
        $stmt->execute([
            ':race_id' => $raceId,
            ':lane' => $lane
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("
                UPDATE race_results
                SET
                    waktu_ms = :waktu_ms,
                    waktu_detik_total = :waktu_detik_total,
                    waktu_menit_total = :waktu_menit_total,
                    waktu_format = :waktu_format,
                    finish_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':waktu_ms' => $waktuMs,
                ':waktu_detik_total' => $waktuDetikTotal,
                ':waktu_menit_total' => $waktuMenitTotal,
                ':waktu_format' => $waktuFormat,
                ':id' => $existing['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO race_results (
                    race_id,
                    lane,
                    waktu_ms,
                    waktu_detik_total,
                    waktu_menit_total,
                    waktu_format,
                    finish_at,
                    created_at,
                    updated_at
                ) VALUES (
                    :race_id,
                    :lane,
                    :waktu_ms,
                    :waktu_detik_total,
                    :waktu_menit_total,
                    :waktu_format,
                    NOW(),
                    NOW(),
                    NOW()
                )
            ");
            $stmt->execute([
                ':race_id' => $raceId,
                ':lane' => $lane,
                ':waktu_ms' => $waktuMs,
                ':waktu_detik_total' => $waktuDetikTotal,
                ':waktu_menit_total' => $waktuMenitTotal,
                ':waktu_format' => $waktuFormat
            ]);
        }

        $stmt = $pdo->prepare("
            UPDATE race_sessions
            SET status = 'running', updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([':id' => $raceId]);

        $pdo->commit();

        jsonResponse(200, [
            'success' => true,
            'message' => 'Finish lane diterima',
            'race_id' => $raceId,
            'type' => 'finish',
            'lane' => $lane
        ]);
    }

    // ============================
    // EVENT: END
    // ============================
    if ($type === 'end') {
        $results = $data['results'] ?? [];

        if (is_array($results)) {
            foreach ($results as $item) {
                $lane = isset($item['lane']) ? (int)$item['lane'] : 0;
                $waktuMs = isset($item['waktu_ms']) ? (int)$item['waktu_ms'] : 0;
                $waktuDetikTotal = isset($item['waktu_detik_total']) ? (float)$item['waktu_detik_total'] : 0;
                $waktuMenitTotal = isset($item['waktu_menit_total']) ? (float)$item['waktu_menit_total'] : 0;
                $waktuFormat = trim($item['waktu_format'] ?? '');

                if ($lane < 1 || $lane > 8) {
                    continue;
                }

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM race_results
                    WHERE race_id = :race_id AND lane = :lane
                    LIMIT 1
                ");
                $stmt->execute([
                    ':race_id' => $raceId,
                    ':lane' => $lane
                ]);
                $existing = $stmt->fetch();

                if ($existing) {
                    $stmt = $pdo->prepare("
                        UPDATE race_results
                        SET
                            waktu_ms = :waktu_ms,
                            waktu_detik_total = :waktu_detik_total,
                            waktu_menit_total = :waktu_menit_total,
                            waktu_format = :waktu_format,
                            updated_at = NOW()
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':waktu_ms' => $waktuMs,
                        ':waktu_detik_total' => $waktuDetikTotal,
                        ':waktu_menit_total' => $waktuMenitTotal,
                        ':waktu_format' => $waktuFormat,
                        ':id' => $existing['id']
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO race_results (
                            race_id,
                            lane,
                            waktu_ms,
                            waktu_detik_total,
                            waktu_menit_total,
                            waktu_format,
                            created_at,
                            updated_at
                        ) VALUES (
                            :race_id,
                            :lane,
                            :waktu_ms,
                            :waktu_detik_total,
                            :waktu_menit_total,
                            :waktu_format,
                            NOW(),
                            NOW()
                        )
                    ");
                    $stmt->execute([
                        ':race_id' => $raceId,
                        ':lane' => $lane,
                        ':waktu_ms' => $waktuMs,
                        ':waktu_detik_total' => $waktuDetikTotal,
                        ':waktu_menit_total' => $waktuMenitTotal,
                        ':waktu_format' => $waktuFormat
                    ]);
                }
            }
        }

        $stmt = $pdo->prepare("
            UPDATE race_sessions
            SET
                event_name = :event_name,
                status = 'finished',
                ended_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':event_name' => $eventName,
            ':id' => $raceId
        ]);

        $pdo->commit();

        jsonResponse(200, [
            'success' => true,
            'message' => 'Race end diterima',
            'race_id' => $raceId,
            'type' => 'end'
        ]);
    }

    $pdo->rollBack();
    jsonResponse(400, [
        'success' => false,
        'message' => 'Type event tidak dikenali'
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    jsonResponse(500, [
        'success' => false,
        'message' => 'Terjadi kesalahan server',
        'error' => $e->getMessage()
    ]);
}