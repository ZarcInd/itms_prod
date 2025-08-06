<?php
// --- PHP download logic ---
if (isset($_GET['date']) && isset($_GET['token'])) {
    $date = $_GET['date'];
    $token = $_GET['token'];

    // Send cookie to tell JS that download started
    setcookie("downloadToken", $token, time() + 60, "/");

    // Database connections - replace with your credentials
    $conn_staging = new mysqli('localhost', 'itms_staging_app', 'Staysafe@01', 'itms_staging_db');
    if ($conn_staging->connect_error) {
        die("Staging DB connection failed: " . $conn_staging->connect_error);
    }

    $conn_prime = new mysqli('localhost', 'itms_primeedg', 'oq7aFmbxA2OEJpkt', 'itms_primeedg');
    if ($conn_prime->connect_error) {
        die("Prime DB connection failed: " . $conn_prime->connect_error);
    }

    $safe_date = $conn_staging->real_escape_string($date);

    $stats_sql = "SELECT device_id, data_date FROM itms_device_stats WHERE data_date = '$safe_date'";
    $stats_result = $conn_staging->query($stats_sql);

    if ($stats_result && $stats_result->num_rows > 0) {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=device_export_$date.csv");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Device ID', 'Data Date', 'Depot Name', 'Firmware Version']);

        while ($row = $stats_result->fetch_assoc()) {
            $device_id = $conn_prime->real_escape_string($row['device_id']);

            // Get depot name
            $depot = '';
            $depot_sql = "SELECT depot FROM devices WHERE device_id = '$device_id' LIMIT 1";
            $depot_result = $conn_prime->query($depot_sql);
            if ($depot_result && $depot_result->num_rows > 0) {
                $depot = $depot_result->fetch_assoc()['depot'];
            }

            // Get firmware version
            $firmware = '';
            $fw_sql = "SELECT firmware_version FROM itms_data_update WHERE device_id = '$device_id' LIMIT 1";
            $fw_result = $conn_prime->query($fw_sql);
            if ($fw_result && $fw_result->num_rows > 0) {
                $firmware = $fw_result->fetch_assoc()['firmware_version'];
            }

            fputcsv($output, [
                $row['device_id'],
                $row['data_date'],
                $depot,
                $firmware
            ]);
        }

        fclose($output);
        exit;
    } else {
        echo "<script>alert('No data found for this date.'); window.location='live_device_export.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Device Data Export</title>
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        input[type="date"], button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .loader {
            display: none;
            margin: 30px auto;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        #loadingText {
            text-align: center;
            font-weight: bold;
            color: #555;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
    </style>
    <link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  rel="stylesheet"
/>
</head>
<body>
<div class="container">
    <h2>Download Communicating Device Data <i class="fa-solid fa-tower-cell"></i></h2>
    <form id="downloadForm">
        <label for="date">Select Date</label>
        <input type="date" name="date" required />
        <button type="submit">Download CSV</button>
    </form>

    <div class="loader" id="loader"></div>
    <div id="loadingText">Preparing download, please wait...</div>
</div>

<script>
document.getElementById('downloadForm').addEventListener('submit', function(e){
    e.preventDefault();

    const date = this.date.value;
    if (!date) {
        alert('Please select a date');
        return;
    }

    const token = Date.now(); // unique token

    // Show loader
    document.getElementById('loader').style.display = 'block';
    document.getElementById('loadingText').style.display = 'block';

    // Poll every 500ms for the cookie set by PHP when download starts
    const interval = setInterval(() => {
        if (document.cookie.includes("downloadToken=" + token)) {
            clearInterval(interval);

            // Hide loader when cookie detected
            document.getElementById('loader').style.display = 'none';
            document.getElementById('loadingText').style.display = 'none';

            // Delete the cookie so it can be reused
            document.cookie = "downloadToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    }, 500);

    // Trigger file download with token and date params
    window.location.href = `live_device_export.php?date=${encodeURIComponent(date)}&token=${token}`;
});
</script>
</body>
</html>
