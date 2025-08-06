<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$dataType = $_GET["type"] ?? throw new Exception("dataType is required", 1);

$tableMapping = [
    "imei" => "itms_imei",
    "group" => "itms_groups",
];

$tableName = $tableMapping[$dataType] ?? null;

if (empty($tableName)) {
    throw new Exception("Invalid data type", 1);
}

$res = fetch_all_from_table($tableName);

logMsg($res);
if (!$res['status']) {
    echo json_encode(['status' => 'false', 'message' => $res['message']]);
} else {
    echo json_encode(['status' => 'success', 'data' => $res['data']]);
}
