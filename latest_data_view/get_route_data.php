<?php
declare(strict_types=1);

date_default_timezone_set('UTC');

// Error handling
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=mtc_primeedg;charset=utf8mb4",
        "mtc_primeedg",
        "oq7aFmbxA2OEJpkt",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Inputs
    $deviceId = $_GET['device_id'] ?? '';
    $date     = $_GET['date'] ?? '';
    $from     = $_GET['from'] ?? '00:00';
    $to       = $_GET['to'] ?? '23:59';

    // Normalize times
    if (strlen($from) === 5) $from .= ':00';
    if (strlen($to) === 5)   $to   .= ':00';

    // Sanitize date: allow yyyy-mm-dd or yyyymmdd
    $date = preg_replace('/[^0-9]/', '', $date);

    $validDate = preg_match('/^\d{8}$/', $date) === 1;
    $validTime = static fn(string $t): bool =>
        (bool)preg_match('/^\d{2}:\d{2}:\d{2}$/', $t);

    if (!$deviceId || !$validDate || !$validTime($from) || !$validTime($to)) {
        echo json_encode(['points' => [], 'error' => 'invalid_input']);
        exit;
    }

    // NMEA conversion
    function convertToDecimal($val, ?string $dir): ?float {
        if ($val === null || $val === '' || !is_numeric($val)) return null;
        $fval = (float)$val;
        $deg = floor($fval / 100);
        $min = $fval - ($deg * 100);
        $decimal = $deg + ($min / 60.0);
        if ($dir === 'S' || $dir === 'W') $decimal *= -1;
        return round($decimal, 6);
    }

    // Vehicle metadata
    $vehicleStmt = $pdo->prepare(
        "SELECT vehicle_no, vehicle_code, depot, route_name
         FROM vehicles
         WHERE device_id = :device_id
         LIMIT 1"
    );
    $vehicleStmt->execute(['device_id' => $deviceId]);
    $vehicleInfo = $vehicleStmt->fetch() ?: null;

    // Telemetry points
    $stmt = $pdo->prepare(
        "SELECT lat, lat_dir, lon, lon_dir, `date`, `time`, speed_kmh
         FROM itms_data
         WHERE device_id = :device_id
           AND partition_key = :date
           AND `time` BETWEEN :from AND :to
           AND lat IS NOT NULL AND lon IS NOT NULL
         ORDER BY `time` ASC"
    );
    $stmt->execute([
        'device_id' => $deviceId,
        'date'      => (int)$date,   
        'from'      => $from,
        'to'        => $to,
    ]);

    $rows = $stmt->fetchAll();

    $data = [];
    $startTime = $endTime = null;

    foreach ($rows as $i => $row) {
        $lat = convertToDecimal($row['lat'], $row['lat_dir'] ?? null);
        $lon = convertToDecimal($row['lon'], $row['lon_dir'] ?? null);

        if ($lat !== null && $lon !== null) {
            $timestamp = trim((string)$row['date']) . ' ' . trim((string)$row['time']);
            $data[] = [
                'lat'       => $lat,
                'lon'       => $lon,
                'timestamp' => $timestamp, // UTC
                'speed'     => isset($row['speed_kmh']) ? (float)$row['speed_kmh'] : null,
            ];
            if ($i === 0) $startTime = $row['time'];
            $endTime = $row['time'];
        }
    }

    echo json_encode([
        'points'     => $data,
        'start_time' => $startTime,
        'end_time'   => $endTime,
        'vehicle'    => $vehicleInfo,
    ]);
} catch (Throwable $e) {
    error_log('API error: ' . $e->getMessage() . ' in ' .
              $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo json_encode(['points' => [], 'error' => 'server_error']);
}
