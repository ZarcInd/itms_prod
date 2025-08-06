<?php
require_once __DIR__ . "/../log.php";
$pdo = null;

function dbResponse($res, $message = "Success", $data = null)
{
    return ["status" => $res, "message" => $message, "data" => $data];
}

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
    $dsn = 'mysql:host=127.0.0.1:3306;dbname=mtc_mtc_staging_db';
    $username = 'mtc_mtc_staging_app';
    $password = 'Staysafe@01';

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
CREATE TABLE `itms_configs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `imei` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(1) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `interval` varchar(10) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `custom_fields` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
 alter table itms_config
  add column `page` varchar(5) after `duration`,
  add column `timeformat` varchar(5) after `duration`,
  add column `datestring` varchar(5) after `duration`,
  add column `apiKey` varchar(5) after `duration`,
  add column `otastatus` varchar(5) after `duration`,
  add column `ip` varchar(5) after `duration`,
  add column `harshalerts` varchar(5) after `duration`,
  add column `storepacket` varchar(5) after `duration`,
  add column `depo_name` varchar(250) after `duration`,
  add column `fleet_no` varchar(250) after `duration`,
  add column `topic` varchar(5) after `duration`;
*/

// Function to insert data into db into structured way
function insert_data_db($data)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("
            INSERT INTO itms_configs (
                `imei`, 
                `group_id`,
                `config_id`,
                `name`, 
                `status`, 
                `url`, 
                `port`, 
                `interval`, 
                `protocol`, 
                `duration`, 
                `custom_fields`,
                `depo_name`,
                `fleet_no`,
                `page`,
                `timeformat`,
                `datestring`,
                `apiKey`,
                `otastatus`,
                `ip`,
                `harshalerts`,
                `storepacket`,
                `topic`
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?,
                ?, ?
            )
        ");
        // Ensure the data array has the correct number of elements
        // Bind parameters and execute statement
        $stmt->execute([
            $data["imei"],
            $data["group_id"],
            $data["config_id"],
            $data["name"],
            $data["status"],
            $data["url"],
            $data["port"],
            $data["interval"],
            $data["protocol"],
            $data["duration"],
            $data["custom_fields"],
            $data["depo_name"],
            $data["fleet_no"],
            $data["page"],
            $data["timeformat"],
            $data["datestring"],
            $data["apiKey"],
            $data["otastatus"],
            $data["ip"],
            $data["harshalerts"],
            $data["storepacket"],
            $data["topic"],
        ]);

        return dbResponse(true);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return insert_data_db($data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to update data into db into structured way
function update_data_db($id, $imei, $group_id, $data)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for inserting data
        $stmt = $pdo->prepare("
            UPDATE itms_configs set 
            `config_id` = ?,
            `name` = ?,
            `status` = ?,
            `url` = ?,
            `port` = ?,
            `interval` = ?,
            `protocol` = ?,
            `duration` = ?,
            `depo_name` = ?,
            `fleet_no` = ?,
            `custom_fields` = ?,
            `page` = ?,
            `timeformat` = ?,
            `datestring` = ?,
            `apiKey` = ?,
            `otastatus` = ?,
            `ip` = ?,
            `harshalerts` = ?,
            `storepacket` = ?,
            `topic` = ?
            WHERE `id` = ? and (`imei` = ? or `group_id` = ?)
        ");
        // Ensure the data array has the correct number of elements
        // Bind parameters and execute statement
        $stmt->execute([
            $data["config_id"],
            $data["name"],
            $data["status"],
            $data["url"],
            $data["port"],
            $data["interval"],
            $data["protocol"],
            $data["duration"],
            $data["depo_name"],
            $data["fleet_no"],
            $data["custom_fields"],
            $data["page"],
            $data["timeformat"],
            $data["datestring"],
            $data["apiKey"],
            $data["otastatus"],
            $data["ip"],
            $data["harshalerts"],
            $data["storepacket"],
            $data["topic"],
            $id,
            $imei,
            $group_id,
        ]);

        return dbResponse(true);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_data_db($id, $imei, $group_id, $data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to list data from db into structured way
function list_data_db($imei, $group_id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT itms_configs.*, itms_groups.group_name FROM itms_configs LEFT JOIN itms_groups ON itms_configs.group_id = itms_groups.id WHERE imei = ? or group_id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$imei, $group_id]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_data_db($imei, $group_id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to list data from db into structured way
function list_id_data_db($id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT itms_configs.*, itms_groups.group_name FROM itms_configs LEFT JOIN itms_groups ON itms_configs.group_id = itms_groups.id WHERE itms_configs.id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$id]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_id_data_db($id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to list data from db into structured way
function delete_id_data_db($id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for deleting data
        $stmt = $pdo->prepare("DELETE FROM itms_configs WHERE id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$id]);
        return dbResponse(true);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return delete_id_data_db($id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to list data from db into structured way
function list_all_data()
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT itms_configs.*, itms_groups.group_name FROM itms_configs LEFT JOIN itms_groups ON itms_configs.group_id = itms_groups.id");
        // Bind parameters and execute statement
        $stmt->execute([]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return list_all_data();
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}


// Function to list data from any db table into structured way
function fetch_all_from_table($table)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT * FROM `" . $table . "`");
        // Bind parameters and execute statement
        $stmt->execute([]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return fetch_all_from_table($table);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}


// Function to perform generic DB query
function execute_query($query, $data = [])
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($data);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return execute_query($query, $data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}


// Function to perform generic DB query
function execute_insert_query($query, $data = [])
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($data);
        $insertId = $pdo->lastInsertId();
        return dbResponse(true, "Success", ["stmt" => $stmt, "insertId" => $insertId]);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return execute_insert_query($query, $data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to get list of all IMEIs having group ID
function get_imei_by_group($groupId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        // $stmt = $pdo->prepare("SELECT * FROM itms_imei WHERE FIND_IN_SET(?, group_id) > 0");
        $stmt = $pdo->prepare("SELECT * FROM itms_imei WHERE active_group_id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$groupId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_imei_by_group($groupId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to get list of all IMEIs having sub group ID
function get_imei_by_sub_group($subGroupId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        // $stmt = $pdo->prepare("SELECT * FROM itms_imei WHERE FIND_IN_SET(?, sub_group_id) > 0");
        $stmt = $pdo->prepare("SELECT * FROM itms_imei WHERE active_sub_group_id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$subGroupId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_imei_by_group($subGroupId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to get list of all IMEIs having group ID
function get_imei($imei)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT * FROM itms_imei WHERE imei = ?");
        // Bind parameters and execute statement
        $stmt->execute([$imei]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_imei($imei);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}


// Function to get ota from id
function get_ota($ota_id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT * FROM itms_ota WHERE id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$ota_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_ota($ota_id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to get xml_update from id
function get_xml_update($xml_id)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("SELECT * FROM itms_xml WHERE id = ?");
        // Bind parameters and execute statement
        $stmt->execute([$xml_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return dbResponse(true, "Success", $result);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return get_xml_update($xml_id);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to update active group ID for IMEI
function update_imei_active_group($groupId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("UPDATE itms_imei SET active_group_id = ? WHERE FIND_IN_SET(?, group_id) > 0");
        // Bind parameters and execute statement
        $stmt->execute([$groupId, $groupId]);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_imei_active_group($groupId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}


// Function to insert new group ID and update active group ID for IMEI
function update_imei_group($imei, $groupId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("UPDATE itms_imei
                                SET group_id = CASE
                                    WHEN group_id IS NULL THEN ?
                                    WHEN FIND_IN_SET(?, group_id) > 0 THEN group_id
                                    ELSE CONCAT(group_id, ',', ?)
                                END,
                                active_group_id = ?
                                where imei = ?");
        // Bind parameters and execute statement
        $stmt->execute([
            $groupId,
            $groupId,
            $groupId,
            $groupId,
            $imei,
        ]);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_imei_group($imei, $groupId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to insert new sub group ID and update active sub group ID for IMEI
function update_imei_sub_group($imei, $subGroupId, $groupId = null)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        $query = "UPDATE itms_imei
                    SET sub_group_id = CASE
                        WHEN sub_group_id IS NULL THEN ?
                        WHEN FIND_IN_SET(?, sub_group_id) > 0 THEN sub_group_id
                        ELSE CONCAT(sub_group_id, ',', ?)
                    END,
                    active_sub_group_id = ?
                  where imei = ?
                    AND active_group_id = ?"; // Adding active_group_id check to take in current group chages only
        $params = [
            $subGroupId,
            $subGroupId,
            $subGroupId,
            $subGroupId,
            $imei,
            $groupId,
        ];
        if ($groupId) {
            $query = $query . " and FIND_IN_SET(?, group_id) > 0";
            array_push($params, $groupId);
        }
        // Prepared statement for finding data
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($params);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_imei_sub_group($imei, $groupId, $groupId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to update ota_id for IMEI
function update_imei_ota($imei, $subGroupId, $otaId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        $query = "UPDATE itms_imei SET ota_id = ? where imei = ? or FIND_IN_SET(?, sub_group_id) > 0";
        $params = [
            $otaId,
            $imei,
            $subGroupId,
        ];
        // Prepared statement for finding data
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($params);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_imei_ota($imei, $subGroupId, $otaId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to update xml_id for IMEI
function update_imei_xml($imei, $subGroupId, $xmlId)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        $query = "UPDATE itms_imei SET xml_id = ? where imei = ? or FIND_IN_SET(?, sub_group_id) > 0";
        $params = [
            $xmlId,
            $imei,
            $subGroupId,
        ];
        // Prepared statement for finding data
        $stmt = $pdo->prepare($query);
        // Bind parameters and execute statement
        $stmt->execute($params);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return update_imei_xml($imei, $subGroupId, $xmlId);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}

// Function to create or replace imei
function upsert_imei($data)
{
    $pdo = getDBConnection();

    if (is_null($pdo)) {
        logError("Connection to DB failed\n");
        return dbResponse(false, "Connection to DB failed");
    }

    try {
        // Prepared statement for finding data
        $stmt = $pdo->prepare("INSERT INTO itms_imei (imei, depo_name, fleet_no, group_id, active_group_id)
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE
                                    group_id = CASE
                                        WHEN group_id IS NULL THEN ?
                                        WHEN FIND_IN_SET(?, group_id) > 0 THEN group_id
                                        ELSE CONCAT(group_id, ',', ?)
                                    END,
                                    active_group_id = ?, 
                                    active_sub_group_id = NULL"); // Resetting sub_group_id whenever group changes
        // Bind parameters and execute statement
        $stmt->execute([
            $data["imei"],
            $data["depo_name"],
            $data["fleet_no"],
            $data["group_id"],
            $data["group_id"],
            $data["group_id"],
            $data["group_id"],
            $data["group_id"],
            $data["group_id"],
        ]);
        return dbResponse(true, "Success", $stmt);
    } catch (PDOException $e) {
        $exception_message = $e->getMessage();
        if (strpos($exception_message, 'server has gone away') !== false) {
            // connectDB();
            return upsert_imei($data);
        }
        logError("Connection failed: " . $e->getMessage() . "\n");
        return dbResponse(false, $e->getMessage());
    }
}
