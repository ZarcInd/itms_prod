<?php

require_once __DIR__ . "/database.php";

use function stats\get_raw_data;

// Function to get date as integer
function getDateAsInteger($year, $month, $day)
{
    return
        intval($year) * 10000 +
        intval($month) * 100 +
        intval($day);
}

// Function to calculate partition key as integer
function getPartitionKey($utc_date)
{
    if (empty($utc_date)) {
        return null;
    }
    $utc_date = str_replace("/", "-", $utc_date);
    $utc_date = explode("-", $utc_date);
    if (count($utc_date) != 3) {
        return null;
    }
    try {
        // YYYY, MM, DD format
        if (strlen($utc_date[0]) == 4) {
            return getDateAsInteger($utc_date[0], $utc_date[1], $utc_date[2]);
        }
        // DD, MM, YYYY format
        else if (strlen($utc_date[2]) == 4) {
            return getDateAsInteger($utc_date[2], $utc_date[1], $utc_date[0]);
        }
    } catch (\Throwable $e) {
        logError("Error parsing data for partition key");
    }

    return null;
}

function convertISTtoUTC($datetime)
{
    $ist_datetime = new DateTime($datetime, new DateTimeZone("Asia/Kolkata"));

    // Convert to UTC
    $ist_datetime->setTimezone(new DateTimeZone("UTC"));

    return $ist_datetime->format("Y-m-d H:i:s");
}

// Get filters from request
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$deviceID = $_GET['device_id'] ?? '';

// Validate mandatory fields
if (!$startDate) {
    die("Start Date and End Date are required.");
}

$startDateTime = convertISTtoUTC($startDate . " 00:00:00");
$endDateTime = convertISTtoUTC($endDate . " 23:59:59");
$partitionKeyLB = getPartitionKey($startDate);
$partitionKeyUB = getPartitionKey($endDate);

$data = get_raw_data($startDateTime, $endDateTime, $partitionKeyLB, $partitionKeyUB, $deviceID);

if ($data === false) {
    die("Something went wrong");
}

// Set headers to download CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="raw_data_' . $startDate . '_' . $endDate . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

ob_start();
$output = fopen('php://output', 'w');
fputcsv($output, ["packet_header", "mode", "device_type", "packet_type", "firmware_version", "device_id", "ignition", "driver_id", "time", "date", "gps", "lat", "lat_dir", "lon", "lon_dir", "speed_knots", "network", "route_no", "speed_kmh", "odo_meter", "Led_health_1", "Led_health_2", "Led_health_3", "Led_health_4", "Server_time"]);

// Fetch and write data
while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
    unset($row["id"]);
    fputcsv($output, $row); // Write data rows
}

fclose($output);
ob_end_flush();
