<?php

// ------------------------
// CONFIGURATION
// ------------------------

$host           = 'localhost';
$username       = 'itms_primeedg';
$password       = 'oq7aFmbxA2OEJpkt';
$database       = 'itms_primeedg';
$partitionDate  = '20250515'; // Format: YYYYMMDD (partition key value)

// ------------------------
// CONNECT TO DATABASE
// ------------------------

$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Prevent script timeout and set high memory if needed
set_time_limit(0);           // No time limit
ini_set('memory_limit', '-1'); // Unlimited memory (precaution, not used heavily due to streaming)

// ------------------------
// SET CSV HEADERS
// ------------------------

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"export_$partitionDate.csv\"");

// Open output stream to browser
$output = fopen('php://output', 'w');

// ------------------------
// WRITE COLUMN HEADERS
// ------------------------

fputcsv($output, ['device_id', 'firmware_version', 'date',]); // Update with actual column names

// ------------------------
// UNBUFFERED QUERY (partition-safe)
// ------------------------

$sql = "SELECT device_id, firmware_version, date 
        FROM itms_can_data 
        WHERE partition_key = $partitionDate";

$mysqli->real_query($sql);
$result = $mysqli->use_result();

// ------------------------
// STREAM RESULTS TO CSV
// ------------------------

$rowCount = 0;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
    $rowCount++;

    // Optional: flush every 1000 rows
    if ($rowCount % 1000 == 0) {
        fflush($output);
    }
}

// ------------------------
// CLEAN UP
// ------------------------

fclose($output);
$result->close();
$mysqli->close();
exit;

?>