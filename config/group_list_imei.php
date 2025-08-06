<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$groupId = $_GET['group_id'] ?? -1;

$res = get_imei_by_group($groupId);

if (!$res['status']) {
    echo json_encode(['status' => 'false', 'message' => $res['message']]);
} else {
    echo json_encode(['status' => 'success', 'data' => $res['data']]);
}
