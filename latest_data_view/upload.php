<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";// Change if needed
$username = "itms_staging_app"; // Your database username
$password = "Staysafe@01"; // Your database password
$database  = "itms_staging_db"; // Change to your database name

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["submit"])) {
    if ($_FILES["csv_file"]["error"] == 0) {
        $fileName = $_FILES["csv_file"]["tmp_name"];

        // Open the file
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            fgetcsv($handle); // Skip the header row

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Assign CSV values to variables
                $imei = $conn->real_escape_string($data[0]);
                $name = $conn->real_escape_string($data[1]);
                $status = $conn->real_escape_string($data[2]);
                $url = $conn->real_escape_string($data[3]);
                $port = $conn->real_escape_string($data[4]);
                $interval = $conn->real_escape_string($data[5]);
                $protocol = $conn->real_escape_string($data[6]);
                $duration = $conn->real_escape_string($data[7]);
                $custom_fields = $conn->real_escape_string($data[8]);
                $depo_name = $conn->real_escape_string($data[9]);
                $fleet_no = $conn->real_escape_string($data[10]);
                $created_at = $conn->real_escape_string($data[11]);
                $updated_at = $conn->real_escape_string($data[12]);

                // Insert into MySQL table
                $query = "INSERT INTO itms_configs (imei, name, status, url, port, `interval`, protocol, duration, custom_fields, depo_name, fleet_no, created_at, updated_at) 
        VALUES ('$imei', '$name', '$status', '$url', '$port', '$interval', '$protocol', '$duration', '$custom_fields', '$depo_name', '$fleet_no', NOW(), NOW())";

                if (!$conn->query($query)) {
                    echo "Error: " . $conn->error . "<br>";
                }
            }
            fclose($handle);
            echo "CSV file successfully imported!";
        }
    } else {
        echo "Error uploading file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload CSV File</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" required>
        <input type="submit" name="submit" value="Upload CSV">
    </form>
</body>
</html>
