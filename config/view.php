<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json');

$imei = $_GET["imei"] ?? null;
$group_id = $_GET["group_id"] ?? null;
$id = $_GET["id"] ?? null;
if ($id) {
    $configurations = list_id_data_db($id);
} else if ($group_id) {
    $configurations = list_data_db(0, $group_id);
} else if ($imei) {
    $configurations = list_data_db($imei, -1);
} else {
    $configurations = list_all_data();
}
logMsg($configurations);
if (!$configurations['status']) {
    echo json_encode(['status' => 'false', 'message' => $configurations['message']]);
} else if ($id == null) {
    echo json_encode(['status' => 'success', 'data' => $configurations['data']]);
} else {
    foreach ($configurations['data'] as $config) {
        if ($config["id"] == $id) {
            echo json_encode(['status' => 'success', 'data' => [$config]]);
            break;
        }
    }
}
