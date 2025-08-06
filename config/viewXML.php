<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$imei = $_GET["imei"] ?? null;
$group_id = $_GET["group_id"] ?? null;
$sub_group_id = $_GET["sub_group_id"] ?? null;
$id = $_GET["id"] ?? null;
$dbRes = fetch_all_from_table("itms_xml");
logMsg($dbRes);
if (!$dbRes['status']) {
    echo json_encode(['status' => 'false', 'message' => $dbRes['message']]);
}

$xml = [];

if ($id) {
    foreach ($dbRes['data'] as $xmlRow) {
        if ($xmlRow['id'] == $id) $xml[] = $xmlRow;
    }
} else if ($sub_group_id) {
    foreach ($dbRes['data'] as $xmlRow) {
        if ($xmlRow['sub_group_id'] == $sub_group_id) $xml[] = $xmlRow;
    }
} else if ($group_id) {
    foreach ($dbRes['data'] as $xmlRow) {
        if ($xmlRow['group_id'] == $group_id) $xml[] = $xmlRow;
    }
} else if ($imei) {
    foreach ($dbRes['data'] as $xmlRow) {
        if ($xmlRow['imei'] == $imei) $xml[] = $xmlRow;
    }
} else {
    // Do nothing
}

echo json_encode(['status' => 'success', 'data' => $xml]);
