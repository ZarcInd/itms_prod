<?php

$address = '0.0.0.0';
$port = 1047;

$pdo = null;

function connectDB()
{
    global $pdo;

    // Database connection
    $dsn = 'mysql:host=127.0.0.1:3306;dbname=itms_staging_db';
    $username = 'itms_staging_app';
    $password = 'Staysafe@01';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "DB Connection Success\n";
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage() . "\n";
    }
}

connectDB();

// Function to return null if current value is not int
function intOrNull($val)
{
    $res = filter_var($val, FILTER_VALIDATE_INT);
    if ($res === false) return null;
    return $res;
}

// Function to return null if current value is not int
function decimalOrNull($val)
{
    $res = filter_var($val, FILTER_VALIDATE_FLOAT);
    if ($res === false) return null;
    return $res;
}

// Function to insert data into db into structured way
function insert_data_db($data_string)
{
    global $pdo;

    if (is_null($pdo)) {
        return "Connection to DB failed\n";
    }

    try {
        // Remove trailing `#` since we have int as last column
        $data_string = rtrim($data_string, "#");
        $data_array = explode(',', $data_string);
        $device_type = "UNKNOWN";
        if (count($data_array) >= 3) {
            $device_type = $data_array[2];
        }

        if ($device_type == "VTS") {
            // Prepared statement for inserting data
            $stmt = $pdo->prepare("
                INSERT INTO itms_data (
                    packet_header, mode, device_type, packet_type, firmware_version,
                    device_id, ignition, driver_id, time, date, 
                    gps, lat, lat_dir, lon, lon_dir, 
                    speed_knots, network, route_no, speed_kmh, odo_meter, 
                    Led_health_1, Led_health_2, Led_health_3, Led_health_4
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?
                )
            ");

            // Ensure the data array has the correct number of elements
            if (count($data_array) >= 20) {
                // Bind parameters and execute statement
                $stmt->execute([
                    $data_array[0],
                    $data_array[1],
                    $data_array[2],
                    $data_array[3],
                    $data_array[4],
                    $data_array[5],
                    $data_array[6],
                    intOrNull($data_array[7]),
                    $data_array[8],
                    $data_array[9],
                    $data_array[10],
                    decimalOrNull($data_array[11]),
                    $data_array[12],
                    decimalOrNull($data_array[13]),
                    $data_array[14],
                    intOrNull($data_array[15]),
                    intOrNull($data_array[16]),
                    $data_array[17],
                    decimalOrNull($data_array[18]),
                    decimalOrNull($data_array[19]),
                    intOrNull($data_array[20] ?? ""),
                    intOrNull($data_array[21] ?? ""),
                    intOrNull($data_array[22] ?? ""),
                    intOrNull($data_array[23] ?? "")
                ]);

                return "Data inserted successfully.\n";
            } else {
                return "Invalid data format.\n";
            }
        } else if ($device_type == "CAN") {
            // Prepared statement for inserting data
            $stmt = $pdo->prepare("
                INSERT INTO itms_can_data (
                    packet_header, mode, device_type, packet_type, firmware_version,
                    device_id, time, date, speed_kmh, oil_pressure
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?
                )
            ");

            // Ensure the data array has the correct number of elements
            if (count($data_array) >= 10) {
                // Bind parameters and execute statement
                $stmt->execute([
                    $data_array[0],
                    $data_array[1],
                    $data_array[2],
                    $data_array[3],
                    $data_array[4],
                    $data_array[5],
                    $data_array[6],
                    $data_array[7],
                    intOrNull($data_array[8]),
                    intorNull($data_array[9])
                ]);

                return "Data inserted successfully.\n";
            } else {
                return "Invalid data format.\n";
            }
        } else {
            return "Unknown data packet\n" . $data_string . "\n";
        }
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            connectDB();
            return insert_data_db($data_string);
        }
        return "Connection failed: " . $e->getMessage() . "\n";
    }
}


// Function to insert data into db
function insert_raw_data_db($data_string)
{
    global $pdo;

    if (is_null($pdo)) {
        return "Connection to DB failed\n";
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("
            INSERT INTO raw_data_logs (
                raw_data
            ) VALUES (
                ?
            )
        ");

        $stmt->execute([$data_string]);

        return "Data inserted successfully.\n";
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            connectDB();
            return insert_raw_data_db($data_string);
        }
        return "Connection failed: " . $e->getMessage() . "\n";
    }
}



// Create a TCP/IP socket

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if ($sock === false) {

    die("Could not create socket: " . socket_strerror(socket_last_error()) . "\n");
}

# To reuse the address
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);

// Bind the socket to address and port

$result = socket_bind($sock, $address, $port);

if ($result === false) {

    die("Could not bind to socket: " . socket_strerror(socket_last_error($sock)) . "\n");
}


// Start listening for connections

$result = socket_listen($sock, 5);

if ($result === false) {

    die("Could not set up socket listener: " . socket_strerror(socket_last_error($sock)) . "\n");
}


echo "Server is listening on $address:$port...\n";


while (true) {

    // Accept incoming connections

    $client = socket_accept($sock);

    if ($client === false) {

        echo "Could not accept incoming connection: " . socket_strerror(socket_last_error($sock)) . "\n";

        continue;
    }

    // Read the input from the client – 1024 bytes
    $input = socket_read($client, 1024);

    if ($input === false) {

        echo "Could not read input: " . socket_strerror(socket_last_error($client)) . "\n";

        socket_close($client);

        continue;
    }


    // Clean up the input string

    $input = trim($input);

    // $output = insert_data_db($input);
    $output = insert_raw_data_db($input);

    echo $output;

    $output2 = insert_data_db($input);

    echo $output2;

    socket_write($client, $output . "\n\n" . $output2, strlen($output . "\n\n" . $output2));


    // Close the client socket

    socket_close($client);
}


// Close the master socket

socket_close($sock);
