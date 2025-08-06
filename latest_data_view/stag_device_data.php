<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost"; // Change if needed
$username = "itms_staging_app"; // Your database username
$password = "Staysafe@01"; // Your database password
$dbname = "itms_staging_db"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current IST time and IST start of the day (12:00 AM IST)
$ist_now = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
$ist_start_of_day = new DateTime("today", new DateTimeZone("Asia/Kolkata"));

// Convert IST start of day (12:00 AM IST) to UTC (18:30 UTC of previous day)
$start_time_utc = (clone $ist_start_of_day)->setTimezone(new DateTimeZone("UTC"))->format("Y-m-d H:i:s");
$end_time_utc = (clone $ist_now)->setTimezone(new DateTimeZone("UTC"))->format("Y-m-d H:i:s");

// Query to get first, last received packet and total packets
$sql = "
    SELECT 
        device_id, 
        MIN(created_at) AS first_received_time, 
        MAX(created_at) AS last_received_time, 
        COUNT(*) AS total_packets_received
    FROM itms_data
    WHERE created_at BETWEEN '$start_time_utc' AND '$end_time_utc'
    GROUP BY device_id
    ORDER BY device_id ASC";

$result = $conn->query($sql);

// Store device data in an array
$device_data = [];
while ($row = $result->fetch_assoc()) {
    $device_data[$row['device_id']] = [
        "first_received_time" => $row["first_received_time"],
        "last_received_time" => $row["last_received_time"],
        "total_packets_received" => $row["total_packets_received"],
        "active_hours" => 0 // Placeholder for active time calculation
    ];
}

// Query to get active periods (based on SO packets)
$active_periods_sql = "
    SELECT device_id, created_at 
    FROM itms_data
    WHERE packet_type = 'SO' 
    AND created_at BETWEEN '$start_time_utc' AND '$end_time_utc'
    ORDER BY device_id, created_at ASC";

$active_result = $conn->query($active_periods_sql);

// Calculate active time for each device
$active_times = [];
while ($row = $active_result->fetch_assoc()) {
    $device_id = $row["device_id"];
    $so_time = strtotime($row["created_at"]);

    if (!isset($active_times[$device_id])) {
        $active_times[$device_id] = ["last_data_time" => null, "total_active_seconds" => 0];
    }

    if ($active_times[$device_id]["last_data_time"] !== null) {
        $active_seconds = $so_time - $active_times[$device_id]["last_data_time"];
        if ($active_seconds > 0) {
            $active_times[$device_id]["total_active_seconds"] += $active_seconds;
        }
    }

    $active_times[$device_id]["last_data_time"] = $so_time;
}

// Assign calculated active hours to devices
foreach ($active_times as $device_id => $time_data) {
    if (isset($device_data[$device_id])) {
        $device_data[$device_id]["active_hours"] = round($time_data["total_active_seconds"] / 3600, 2);
    }
}

// Display Data
if (!empty($device_data)) {
    echo "<table border='1'>
            <tr>
                <th>S.No.</th>
                <th>Device ID</th>
                <th>First Data Received (IST)</th>
                <th>Last Data Received (IST)</th>
                <th>Packets Received (Today)</th>
                <th>Active Hours</th>
                <th>Expected Packets</th>
                <th>Efficiency (%)</th>
            </tr>";

    $serial_number = 1;
    foreach ($device_data as $device_id => $data) {
        // Convert timestamps to IST
        $first_received_dt = new DateTime($data["first_received_time"], new DateTimeZone("UTC"));
        $first_received_dt->setTimezone(new DateTimeZone("Asia/Kolkata"));

        $last_received_dt = new DateTime($data["last_received_time"], new DateTimeZone("UTC"));
        $last_received_dt->setTimezone(new DateTimeZone("Asia/Kolkata"));

        // Active hours
        $active_hours = $data["active_hours"];

        // Expected packets (1 packet every 10 seconds → 360 packets per hour)
        $expected_packets = round($active_hours * 360);

        // Efficiency calculation
        $efficiency = ($expected_packets > 0) ? round(($data["total_packets_received"] / $expected_packets) * 100, 2) : 0;

        echo "<tr>
                <td>" . $serial_number . "</td>
                <td>" . htmlspecialchars($device_id) . "</td>
                <td>" . htmlspecialchars($first_received_dt->format("Y-m-d H:i:s")) . "</td>
                <td>" . htmlspecialchars($last_received_dt->format("Y-m-d H:i:s")) . "</td>
                <td>" . htmlspecialchars($data["total_packets_received"]) . "</td>
                <td>" . htmlspecialchars(number_format($active_hours, 2)) . "</td>
                <td>" . htmlspecialchars($expected_packets) . "</td>
                <td>" . htmlspecialchars($efficiency) . "%</td>
              </tr>";
        $serial_number++;
    }
    echo "</table>";
} else {
    echo "No data available since 12:00 AM IST.";
}

$conn->close();
?>