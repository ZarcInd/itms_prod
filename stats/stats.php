<?php

use function stats\list_id_data_db;
use function stats\list_data_db;
use function stats\list_all_data;
use function stats\list_id_only_data_db;
use function stats\update_data_db;
use function stats\insert_data_db;

require_once __DIR__ . "/db.php";

function getDateTimeDiff($datetime1, $datetime2)
{
    $diffInSeconds = $datetime2->getTimestamp() - $datetime1->getTimestamp();
    return $diffInSeconds;
}

function getPktDateTime($utc_date, $utc_time)
{
    $utc_date = str_replace("/", "-", $utc_date);
    $utc_datetime = new DateTime("$utc_date $utc_time", new DateTimeZone("UTC"));
    return $utc_datetime;
}

function convertUTCDateTimeToISTDate($utc_datetime)
{
    // Convert to IST
    $utc_datetime->setTimezone(new DateTimeZone("Asia/Kolkata"));
    // Extract the IST date
    $ist_date = $utc_datetime->format("Y-m-d");
    return $ist_date;
}

/*
$data = [
    "device_type" => "VTS",
    "pkt_type" => $data_array[3],
    "device_id" => $data_array[5],
    "time" => $data_array[8],
    "date" => $data_array[9],
];
*/
function updateVTSData($pktData)
{
    if ($pktData === null) {
        logMsg("Got null data to update in VTS device Stats");
        return;
    }

    $pktsTimeDiffThreshold = 15 * 60; // 15 Minutes
    $device_id = $pktData["device_id"];
    $pktDateTime = getPktDateTime($pktData["date"], $pktData["time"]);
    $ist_date = convertUTCDateTimeToISTDate(getPktDateTime($pktData["date"], $pktData["time"]));
    if (is_null($device_id)) {
        logMsg("Got null device ID to update in VTS device Stats");
        return false;
    }
    $data = list_id_data_db($device_id, $ist_date);
    if ($data === false) {
        return false;
    }
    if (count($data) > 0) { // Already exist, so update
        $data = $data[0]; // Use first entry
        $updateData = [];

        $updateData["pkt_count"] = $data['pkt_count'] + 1;
        $updateData["last_pkt"] = $pktDateTime->format('Y-m-d H:i:s');

        if ($pktData['pkt_type'] == "SO" || $pktData['pkt_type'] == "LO") {
            if ($data["first_so_pkt"]) {
                $updateData["first_so_pkt"] = $data["first_so_pkt"];
            } else {
                $updateData["first_so_pkt"] = $pktDateTime->format('Y-m-d H:i:s');;
            }
            $updateData["so_pkt_cnt"] = $data['so_pkt_cnt'] + 1;
            $updateData["last_so_pkt"] = $pktDateTime->format('Y-m-d H:i:s');
            $updateData["online_time"] = $data['online_time'];
        } else {
            $updateData["first_so_pkt"] = $data["first_so_pkt"];
            $updateData["so_pkt_cnt"] = $data['so_pkt_cnt'];
            $updateData["last_so_pkt"] = $data['last_so_pkt'];

            $last_pkt_dt = new DateTime($data["last_pkt"], new DateTimeZone("UTC"));
            $time_diff = getDateTimeDiff($last_pkt_dt, $pktDateTime);
            if ($time_diff > $pktsTimeDiffThreshold) { // Consider this as SO pkt
                $updateData['online_time'] = $data['online_time'];
            } else {
                $updateData['online_time'] = $data['online_time'] + $time_diff;
            }
        }

        $res = update_data_db($data['id'], $updateData);
        logMsg("Device status Updation Status: " . json_encode($res));
    } else { // New pkt for the day, so create
        $insertData = [];

        $insertData["device_id"] = $device_id;
        $insertData["data_date"] = $ist_date;

        $insertData["pkt_count"] = 1;
        $insertData["first_pkt"] = $pktDateTime->format('Y-m-d H:i:s');;
        $insertData["last_pkt"] = $pktDateTime->format('Y-m-d H:i:s');
        $insertData["online_time"] = 0;

        if ($pktData['pkt_type'] == "SO" || $pktData['pkt_type'] == "LO") {
            $insertData["first_so_pkt"] = $pktDateTime->format('Y-m-d H:i:s');;
            $insertData["so_pkt_cnt"] = 1;
            $insertData["last_so_pkt"] = $pktDateTime->format('Y-m-d H:i:s');
        } else {
            $insertData["first_so_pkt"] = null;
            $insertData["so_pkt_cnt"] = 0;
            $insertData["last_so_pkt"] = null;
        }

        $res = insert_data_db($insertData);
        logMsg("Device status Insertion Status: " . json_encode($res));
    }

    // Log Msg to check if it is correct status
    // logMsg(list_id_data_db($device_id, $ist_date));
}

function listVTSData($date = null, $device_id = null)
{
    if ($date == null && $device_id == null) {
        $res = list_all_data();
    } else if ($date == null) {
        $res = list_id_only_data_db($device_id);
    } else if ($device_id == null) {
        $res = list_data_db($date);
    } else {
        $res = list_id_data_db($device_id, $date);
    }
    return $res;
}
