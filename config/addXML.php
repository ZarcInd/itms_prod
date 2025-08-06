<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/xml.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? null;
    $sub_group_id = $_POST['sub_group_id'] ?? null;
    $imei = $_POST['imei'] ?? null;

    if (empty($imei) && (empty($group_id) || empty($sub_group_id))) {
        throw new Exception("Either IMEI or Group & Sub Group is required", 1);
    }

    // Note: Relying on DB foreign keys for integrity of data

    $data = [
        "imei" => $imei ?? null,
        "group_id" => $group_id ?? null,
        "sub_group_id" => $sub_group_id ?? null,
        "version" => $_POST["version"] ?? throw new Exception("Version is required", 1),
        "path" => $_POST["path"] ?? '',
        "username" => $_POST["username"] ?? '',
        "password" => $_POST["password"] ?? '',
        "ip" => $_POST["ip"] ?? '',
        "port" => $_POST["port"] ?? '0',
        "status" => $_POST["status"] ?? '',
        "custom_fields" => $_POST["custom_fields"] ?? "",
    ];

    $cols = "";
    $qs = "";
    $params = [];
    foreach ($data as $key => $val) {
        if ($cols == "") {
            $cols = "`$key`";
            $qs = "?";
        } else {
            $cols = $cols . " ,`$key`";
            $qs = $qs . " ,?";
        }
        $params[] = $val;
    }
    $query = "INSERT INTO itms_xml ($cols) values ($qs)";

    $res = execute_insert_query($query, $params);

    if ($res && $res['status']) {
        $xmlId = $res['data']['insertId'] ?? -1;
        update_imei_xml(($imei ?? 0), ($sub_group_id ?? -1), $xmlId);

        $updateQuery = "UPDATE itms_groups set xml_id = ? where id = ?";
        $params = [$xmlId, $sub_group_id];
        execute_query($updateQuery, $params);

        $imeis = [['imei' => $imei]];
        if ($sub_group_id) {
            $imeis = get_imei_by_sub_group($sub_group_id);
            if ($imeis['status']) {
                $imeis = $imeis['data'];
            } else {
                $imeis = [];
            }
        }
        foreach ($imeis as $upd_imei) {
            deleteXml($upd_imei['imei']);
        }

        echo json_encode(['status' => 'success', 'message' => 'XML update saved successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'XML update save failed. ' . ($res['message'] ?? "")]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
