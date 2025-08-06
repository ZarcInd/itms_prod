<?php

require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/xml.php";

$ip_port = '{"ip":"15.207.42.99","port":"1047"}';

$imei = $_GET['imei'] ?? null;

if ($imei === null) {
    http_response_code(400);
    echo "IMEI is missing.";
    exit;
}

$file_ext = "xml"; // "txt";
$download = false; // true;

// $filename = "./ip_port/$imei.txt";
$filename = "./ip_port/$imei.$file_ext";
$downloadname = "$imei.$file_ext";

// If file doesn't exist, create it first
if (!file_exists($filename)) {
    // file_put_contents($filename, $ip_port);
    // http_response_code(404);
    // echo "No config found for IMEI.";
    $imeiRes = get_imei($imei);
    if (!$imeiRes['status'] || !$imeiRes['data'] || count($imeiRes['data']) == 0) {
        http_response_code(400);
        logMsg("Cannot fetch IMEI info.");
        echo "Cannot fetch IMEI info.\n";
        return;
    }
    $group_id = $imeiRes['data'][0]['active_group_id'] ?? -1;
    $ota_id = $imeiRes['data'][0]['ota_id'] ?? null;
    $xml_id = $imeiRes['data'][0]['xml_id'] ?? null;
    $data = list_data_db($imei, $group_id);
    if ($data['status']) {
        logMsg($data['data']);
        $otaUpdates = get_ota($ota_id);
        $xmlUpdates = get_xml_update($xml_id);
        saveXml($imei, $data['data'], $otaUpdates['data'] ?? [], $xmlUpdates['data'] ?? []);
    }
}

// Force file download
if (file_exists($filename)) {
    if ($download) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($downloadname) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
    } else {
        header("Content-Type: application/xml; charset=UTF-8");
        $xml = file_exists($filename) ? simplexml_load_file($filename) : new SimpleXMLElement('<servercommunication></servercommunication>');
        echo $xml->asXML();
    }
    exit;
} else {
    http_response_code(404);
    echo "Config not found.";
}
