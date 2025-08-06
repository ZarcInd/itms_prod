<?php

require_once __DIR__ . '/stats.php';

function convertUTCStringToIST($utc_string)
{
    $utc_datetime = new DateTime("$utc_string", new DateTimeZone("UTC"));
    // Convert to IST
    $utc_datetime->setTimezone(new DateTimeZone("Asia/Kolkata"));
    // Extract the IST datetime
    $ist_datetime = $utc_datetime->format("Y-m-d H:i:s");
    return $ist_datetime;
}

header("Content-Type: application/json");

// Get Parameters
$date = isset($_GET['date']) ? $_GET['date'] : '';

$data = listVTSData($date) ?? [];

$responseData = [];

foreach ($data as $deviceStats) {
    $resultData = [];
    $oth_pkts = $deviceStats['pkt_count'] - $deviceStats['so_pkt_cnt'];
    $active_hours = round($deviceStats['online_time'] / 3600, 2);
    $packets_frequency_in_secs = 10;
    $expected_packets = round($active_hours * (60 * 60 / $packets_frequency_in_secs));
    $total_packets_received = $deviceStats['pkt_count'];
    $efficiency = ($expected_packets > 0) ? round(($total_packets_received / $expected_packets) * 100, 2) : 0;

    $resultData['device_id'] = $deviceStats['device_id'];
    $resultData['active_hours'] = $active_hours;
    $resultData['total_pkts'] = $deviceStats['pkt_count'];
    $resultData['oth_pkts'] = $oth_pkts;
    $resultData['so_pkts'] = $deviceStats['so_pkt_cnt'];
    $resultData['expected_pkts'] = $expected_packets;
    $resultData['efficiency'] = $efficiency;
    $resultData['first_pkt'] = convertUTCStringToIST($deviceStats['first_pkt']);
    $resultData['last_pkt'] = convertUTCStringToIST($deviceStats['last_pkt']);

    array_push($responseData, $resultData);
}

// Return JSON
echo json_encode($responseData);
