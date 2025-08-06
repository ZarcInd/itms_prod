<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$imei = $_GET["imei"] ?? null;
$group_id = $_GET["group_id"] ?? null;
$sub_group_id = $_GET["sub_group_id"] ?? null;
$id = $_GET["id"] ?? null;
$dbRes = fetch_all_from_table("itms_ota");
logMsg($dbRes);
if (!$dbRes['status']) {
    echo json_encode(['status' => 'false', 'message' => $dbRes['message']]);
}

$ota = [];

if ($id) {
    foreach ($dbRes['data'] as $otaRow) {
        if ($otaRow['id'] == $id) $ota[] = $otaRow;
    }
} else if ($sub_group_id) {
    foreach ($dbRes['data'] as $otaRow) {
        if ($otaRow['sub_group_id'] == $sub_group_id) $ota[] = $otaRow;
    }
} else if ($group_id) {
    foreach ($dbRes['data'] as $otaRow) {
        if ($otaRow['group_id'] == $group_id) $ota[] = $otaRow;
    }
} else if ($imei) {
    foreach ($dbRes['data'] as $otaRow) {
        if ($otaRow['imei'] == $imei) $ota[] = $otaRow;
    }
} else {
    // Do nothing
}

echo json_encode(['status' => 'success', 'data' => $ota]);
