<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$groupId = $_GET['group_id'];

if (empty($groupId)) {
    die("Group is required.");
}

$query = "SELECT itms_groups.id, itms_groups.group_name, (SELECT COUNT(*) FROM itms_imei WHERE FIND_IN_SET(itms_groups.id, itms_imei.sub_group_id) > 0) AS imei_count FROM itms_groups where parent_group = ?";

$res = execute_query($query, [$groupId]);

if (!$res['status']) {
    logMsg($res);
    echo json_encode(['status' => 'false', 'message' => $res['message']]);
} else {
    $res["data"] = $res["data"]->fetchAll(PDO::FETCH_ASSOC);
    logMsg($res);
    echo json_encode(['status' => 'success', 'data' => $res['data']]);
}
