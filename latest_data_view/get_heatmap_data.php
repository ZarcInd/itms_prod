<?php
$host = "localhost";
$db = "mtc_primeedg";
$user = "mtc_primeedg";
$pass = "oq7aFmbxA2OEJpkt";

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

// Validate date input
$date = $_GET['date'] ?? null;
if (!$date || !preg_match('/^\d{8}$/', $date)) {
    echo json_encode(['error' => 'Invalid date']);
    exit;
}

// Fetch rows
$sql = "
SELECT lat, lat_dir, lon, lon_dir
FROM itms_data
WHERE 
  packet_type = 'SP'
  AND partition_key = :date
  AND lat IS NOT NULL AND lon IS NOT NULL
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':date', (int)$date, PDO::PARAM_INT);
$stmt->execute();

// Convert NMEA to decimal
function convertToDecimal($value, $dir) {
    $value = floatval($value);
    $deg = floor($value / 100);
    $min = $value - ($deg * 100);
    $decimal = $deg + ($min / 60);
    if ($dir === 'S' || $dir === 'W') $decimal *= -1;
    return round($decimal, 6);
}

$data = [];

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $lat = convertToDecimal($row['lat'], $row['lat_dir']);
    $lon = convertToDecimal($row['lon'], $row['lon_dir']);

    if ($lat && $lon) {
        $data[] = [
            'lat' => $lat,
            'lon' => $lon,
            'intensity' => 0.1 // optional, used in heatmap or clusters
        ];
    }
}

// Output
header('Content-Type: application/json');
echo json_encode(array_values($data));
