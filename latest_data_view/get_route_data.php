<?php
date_default_timezone_set('UTC');
$pdo = new PDO("mysql:host=localhost;dbname=mtc_primeedg;charset=utf8mb4", "mtc_primeedg", "oq7aFmbxA2OEJpkt");


$deviceId = $_GET['device_id'] ?? '';
$date = $_GET['date'] ?? '';
$from = $_GET['from'] ?? '00:00';
$to = $_GET['to'] ?? '23:59';

if (strlen($from) === 5) $from .= ':00';
if (strlen($to) === 5) $to .= ':00';

if (!$deviceId || !preg_match('/^\d{8}$/', $date)) {
    echo json_encode(['points' => []]);
    exit;
}

$vehicleStmt = $pdo->prepare("SELECT vehicle_no, vehicle_code, depot, route_name FROM vehicles WHERE device_id = :device_id LIMIT 1");
$vehicleStmt->execute(['device_id' => $deviceId]);
$vehicleInfo = $vehicleStmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
  SELECT lat, lat_dir, lon, lon_dir, date, time, speed_kmh
  FROM itms_data
  WHERE device_id = :device_id
    AND partition_key = :date
    AND time BETWEEN :from AND :to
    AND lat IS NOT NULL AND lon IS NOT NULL
  ORDER BY time ASC
");

$stmt->execute([
  'device_id' => $deviceId,
  'date' => (int)$date,
  'from' => $from,
  'to' => $to
]);

function convertToDecimal($val, $dir) {
    $val = floatval($val);
    $deg = floor($val / 100);
    $min = $val - ($deg * 100);
    $decimal = $deg + $min / 60;
    if ($dir === 'S' || $dir === 'W') $decimal *= -1;
    return round($decimal, 6);
}

$data = [];
$startTime = null;
$endTime = null;

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $i => $row) {
    $lat = convertToDecimal($row['lat'], $row['lat_dir']);
    $lon = convertToDecimal($row['lon'], $row['lon_dir']);
    if ($lat && $lon) {
         $timestamp = $row['date'] . ' ' . $row['time'];;  // UTC
        $data[] = [
            'lat' => $lat,
            'lon' => $lon,
            'timestamp' => $timestamp,
            'speed' => (float) $row['speed_kmh']
        ];
        if ($i === 0) $startTime = $row['time'];
        $endTime = $row['time'];
    }
}

echo json_encode([
    'points' => $data,
    'start_time' => $startTime,
    'end_time' => $endTime,
    'vehicle' => $vehicleInfo
]);
