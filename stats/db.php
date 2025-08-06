<?php

namespace stats;

use PDO;
use PDOException;

require_once __DIR__ . "/../log.php";
$pdo = null;

function connectDB()
{
    global $pdo;

    // Database connection
    $dsn = 'mysql:host=127.0.0.1:3306;dbname=mtc_primeedg';
    $username = 'mtc_primeedg';
    $password = 'oq7aFmbxA2OEJpkt';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        logMsg("DB Connection Success\n");
    } catch (PDOException $e) {
        logError('Connection failed: ' . $e->getMessage() . "\n");
    }
}

function getDBConnection()
{
    // Database connection
    $dsn = 'mysql:host=127.0.0.1:3306;dbname=mtc_primeedg';
    $username = 'mtc_primeedg';
    $password = 'oq7aFmbxA2OEJpkt';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        logMsg("DB Connection Success\n");
        return $pdo;
    } catch (PDOException $e) {
        logError('Connection failed: ' . $e->getMessage() . "\n");
        return null;
    }
}

connectDB();

/*
CREATE TABLE `itms_device_stats` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `device_id` bigint NOT NULL,
  `data_date` date NOT NULL,
  `first_pkt` datetime NOT NULL, 
  `first_so_pkt` datetime DEFAULT NULL,
  `last_pkt` datetime NOT NULL,
  `last_so_pkt` datetime DEFAULT NULL,
  `online_time` int NOT NULL,
  `pkt_count` int DEFAULT NULL,
  `so_pkt_cnt` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `SEARCH1` (`device_id`),
  KEY `SEARCH2` (`data_date`),
  KEY `SEARCH3` (`device_id`,`data_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
*/

// Function to insert data into db into structured way
function insert_data_db($data)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("
            INSERT INTO itms_device_stats (
                `device_id`, `data_date`, `first_pkt`, `first_so_pkt`, `last_pkt`, `last_so_pkt`, `online_time`, `pkt_count`, `so_pkt_cnt`
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");
        // Ensure the data array has the correct number of elements
        // Bind parameters and execute statement
        $stmt->execute([
            $data["device_id"],
            $data["data_date"],
            $data["first_pkt"],
            $data["first_so_pkt"],
            $data["last_pkt"],
            $data["last_so_pkt"],
            $data["online_time"],
            $data["pkt_count"],
            $data["so_pkt_cnt"],
        ]);

        return true;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return insert_data_db($data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

// Function to update data into db into structured way
function update_data_db($id, $data)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("
            UPDATE itms_device_stats set `first_so_pkt` = ?, `last_pkt` = ?, `last_so_pkt` = ?, `online_time` = ?, `pkt_count` = ?, `so_pkt_cnt` = ?
            WHERE `id` = ?
        ");
        // Ensure the data array has the correct number of elements
        // Bind parameters and execute statement
        $stmt->execute([
            $data["first_so_pkt"],
            $data["last_pkt"],
            $data["last_so_pkt"],
            $data["online_time"],
            $data["pkt_count"],
            $data["so_pkt_cnt"],
            $id,
        ]);

        return true;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_data_db($id, $data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

// Function to list data from db into structured way
function list_data_db($date)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("SELECT * FROM itms_device_stats WHERE data_date = ?");
        // Bind parameters and execute statement
        $stmt->execute([$date]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_data_db($date);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

// Function to list data from db into structured way
function list_id_data_db($device_id, $date)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("SELECT * FROM itms_device_stats WHERE device_id = ? and data_date = ?");
        // Bind parameters and execute statement
        $stmt->execute([$device_id, $date]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_id_data_db($device_id, $date);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

// Function to list data from db into structured way
function list_id_only_data_db($device_id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("SELECT * FROM itms_device_stats WHERE device_id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$device_id,]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_id_only_data_db($device_id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

// Function to list data from db into structured way
function list_all_data()
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("SELECT * FROM itms_device_stats");
        // Bind parameters and execute statement
        $stmt->execute([]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_all_data();
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}

function get_raw_data($start_datetime, $end_datetime, $partitionKeyLB, $partitionKeyUB, $device_id = null)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return false;
    }

    try {
        // Prepared statement for inserting data
        $query = "SELECT * FROM itms_data where STR_TO_DATE(CONCAT(date, ' ', time), '%d/%m/%Y %H:%i:%s') between ? and ? and partition_key between ? and ?";
        $params = [$start_datetime, $end_datetime, $partitionKeyLB, $partitionKeyUB,];
        if (!empty($device_id)) {
            $query = $query . " and device_id = ?";
            $params[] = $device_id;
        }
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($params);

        return $stmt;
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_raw_data($start_datetime, $end_datetime, $partitionKeyLB, $partitionKeyUB, $device_id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return false;
    }
}
