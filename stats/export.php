<?php
// Set download cookie
setcookie("fileDownload", "true", 0, "/");

// Validate input
if (!isset($_GET['date']) || !isset($_GET['device_id'])) {
    die("Missing required parameters.");
}

$date = $_GET['date'];
$device_id = $_GET['device_id'];

// Convert date to partition format
$date = date('Ymd', strtotime($date));

// Connect to DB
$conn = new mysqli("localhost", "itms_primeedg", "oq7aFmbxA2OEJpkt", "itms_primeedg");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute query using date as partition key
$stmt = $conn->prepare("
    SELECT packet_header, mode, device_type, packet_type, firmware_version,
           device_id, time, date, speed_kmh,created_at
    FROM itms_data
    WHERE partition_key = ? AND device_id = ?
");
$stmt->bind_param("ss", $date, $device_id);
$stmt->execute();
$result = $stmt->get_result(); 

setcookie("fileDownload", "true", 0, "/");

// Set headers for CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="export_' . $device_id . '_' . $date . '.csv"');

// Output CSV
$output = fopen('php://output', 'w');
fputcsv($output, ["packet_header", "mode", "device_type", "packet_type", "firmware_version", "device_id","time", "date", "speed_kmh","server_time",]); // headers

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$stmt->close();
$conn->close();
exit;