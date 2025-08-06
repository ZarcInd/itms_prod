<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/xml.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST["id"] ?? null;
    $imei = $_POST["imei"] ?? null;
    $group_id = $_POST['group_id'] ?? null;

    if (empty($imei) && empty($group_id)) {
        throw new Exception("IMEI or group_id is required", 1);
    }

    $imei = $imei ?? 0;
    $group_id = $group_id ?? (-1);

    // Note: Relying on DB foreign keys for integrity of data

    $data = [
        "config_id" => $_POST["CONFIG_ID"] ?? $_POST["config_id"] ?? throw new Exception("Config ID is required", 1),
        "name" => $_POST["NAME"] ?? $_POST["name"] ?? throw new Exception("Name is required", 1),
        "port" => $_POST["PORT"] ?? $_POST["port"] ?? "",
        "url" => $_POST["URL"] ?? $_POST["url"] ?? "",
        "status" => $_POST["STATUS"] ?? $_POST["status"] ?? "",
        "interval" => $_POST["INTERVAL"] ?? $_POST["interval"] ?? "",
        "protocol" => $_POST["PROTOCOL"] ?? $_POST["protocol"] ?? "",
        "depo_name" => $_POST["depo_name"] ?? $_POST["depo_name"] ?? "",
        "fleet_no" => $_POST["FLEET_NO"] ?? $_POST["fleet_no"] ?? "",
        "duration" => $_POST["DURATION"] ?? $_POST["duration"] ?? "",
        "page" => $_POST["PAGE"] ?? $_POST["page"] ?? "",
        "timeformat" => $_POST["TIMEFORMAT"] ?? $_POST["timeformat"] ?? "",
        "datestring" => $_POST["DATESTRING"] ?? $_POST["datestring"] ?? "",
        "apiKey" => $_POST["APIKEY"] ?? $_POST["apiKey"] ?? "",
        "otastatus" => $_POST["OTASTATUS"] ?? $_POST["otastatus"] ?? "",
        "ip" => $_POST["IP"] ?? $_POST["ip"] ?? "",
        "harshalerts" => $_POST["HARSHALERTS"] ?? $_POST["harshalerts"] ?? "",
        "storepacket" => $_POST["STOREPACKET"] ?? $_POST["storepacket"] ?? "",
        "topic" => $_POST["TOPIC"] ?? $_POST["topic"] ?? "",
        "custom_fields" => $_POST["custom_fields"] ?? "",
    ];
    $data['name'] = str_replace(" ", "", $data['name']);
    $res = update_data_db($id, $imei, $group_id, $data);

    if ($res && $res['status']) {
        $imeis = [$imei];
        if ($group_id) {
            $imeis = get_imei_by_group($group_id);
            if ($imeis['status']) {
                $imeis = $imeis['data'];
            } else {
                $imeis = [];
            }
        }
        foreach ($imeis as $upd_imei) {
            deleteXml($upd_imei['imei']);
        }
        echo json_encode(['status' => 'success', 'message' => 'Configuration saved successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Save failed. ' . ($res['message'] ?? "")]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
