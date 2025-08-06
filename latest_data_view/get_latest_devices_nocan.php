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

// Query to get latest data only for today (12:00 AM IST onward)
$sql = "
    SELECT 
        d.device_id, 
        MIN(CONVERT_TZ(d.created_at, '+00:00', '+05:30')) AS first_received_time, 
        MAX(CONVERT_TZ(d.created_at, '+00:00', '+05:30')) AS last_received_time, 
        COUNT(*) AS total_packets_received
    FROM itms_data d
    WHERE d.created_at BETWEEN '$start_time_utc' AND '$end_time_utc'
    GROUP BY d.device_id
    ORDER BY d.device_id ASC";  // Keeps device_id static

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>S.No.</th>
                <th>Device ID</th>
                <th>First Data Received (IST)</th>
                <th>Last Data Received (IST)</th>
                <th>Packets Received (Today)</th>
                <th>Active Hours</th>
                
            </tr>";
           // <th>Expected Packets</th>
             //   <th>Efficiency (%)</th>

    $serial_number = 1; // Initialize serial number
    while ($row = $result->fetch_assoc()) {
        $first_received = $row["first_received_time"];
        $last_received = $row["last_received_time"];
        $total_packets_received = intval($row["total_packets_received"]);

        // Convert timestamps to DateTime objects
        $first_received_dt = new DateTime($first_received);
        $last_received_dt = new DateTime($last_received);

        // Calculate active hours (difference between first and last received packet)
        $active_hours = round(($last_received_dt->getTimestamp() - $first_received_dt->getTimestamp()) / 3600, 2);

        // Calculate expected packets (1 packet every 10 seconds → 360 packets per hour)
        $expected_packets = round($active_hours * 360); 

        // Calculate efficiency
        $efficiency = ($expected_packets > 0) ? round(($total_packets_received / $expected_packets) * 100, 2) : 0;

        echo "<tr>
                <td>" . $serial_number . "</td>
                <td>" . htmlspecialchars($row["device_id"]) . "</td>
                <td>" . htmlspecialchars($first_received) . "</td>
                <td>" . htmlspecialchars($last_received) . "</td>
                <td>" . htmlspecialchars($total_packets_received) . "</td>
                <td>" . htmlspecialchars(number_format($active_hours, 2)) . "</td>
                
              </tr>";
             // <td>" . htmlspecialchars($expected_packets) . "</td>
//<td>" . htmlspecialchars($efficiency) . "%</td>
        $serial_number++; // Increment serial number
    }
    echo "</table>";
} else {
    echo "No data available since 12:00 AM IST.";
}

$conn->close();
?>
