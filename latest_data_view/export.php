<?php
require_once 'config.php';

set_time_limit(0);
ini_set('memory_limit', '2048M');

$action = $_GET['action'] ?? '';
$date = $_GET['date'] ?? '';
$chunk = (int)($_GET['chunk'] ?? 0);
$recordsPerChunk = 500000;

$exportDir = __DIR__ . "/tmp_exports/$date";
if (!is_dir($exportDir)) mkdir($exportDir, 0777, true);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("DB error");

// INIT: Get total count
if ($action === 'init') {
    $start = date('Y-m-d 00:00:00', strtotime($date));
    $end = date('Y-m-d 23:59:59', strtotime($date));

    $sql = "SELECT COUNT(*) AS total FROM itms_data WHERE created_at BETWEEN '$start' AND '$end'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total = (int)$row['total'];
    $chunks = ceil($total / $recordsPerChunk);

    echo json_encode(['total' => $total, 'chunks' => $chunks]);
    exit;
}

// CHUNK EXPORT
if ($action === 'chunk') {
    $start = date('Y-m-d 00:00:00', strtotime($date));
    $end = date('Y-m-d 23:59:59', strtotime($date));

    $offset = $chunk * $recordsPerChunk;

    $sql = "SELECT * FROM itms_data 
            WHERE created_at BETWEEN '$start' AND '$end' 
            ORDER BY device_id, id 
            LIMIT $recordsPerChunk OFFSET $offset";

    $result = $conn->query($sql);
    $filename = "$exportDir/chunk_" . ($chunk + 1) . ".csv";
    $fp = fopen($filename, 'w');

    if ($result && $result->num_rows > 0) {
        // Header
        $headers = array_keys($result->fetch_assoc());
        fputcsv($fp, $headers);
        $result->data_seek(0);

        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }
    }

    fclose($fp);
    echo "OK";
    exit;
}
