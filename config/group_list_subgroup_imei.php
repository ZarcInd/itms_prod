<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$subGroupId = $_GET['sub_group_id'] ?? -1;

$res = get_imei_by_sub_group($subGroupId);

if (!$res['status']) {
    echo json_encode(['status' => 'false', 'message' => $res['message']]);
} else {
    echo json_encode(['status' => 'success', 'data' => $res['data']]);
}
