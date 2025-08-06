<?php
require_once __DIR__ . "/../log.php";

function isNullOrEmptyString($str)
{
    if (is_null($str)) {
        return true;
    }
    if ($str === "") {
        return true;
    }
    return false;
}

function transformKey($key)
{
    $transforms = [
        "config_id" => "id",
    ];
    return $transforms[$key] ?? $key;
}

function deleteXml($imei)
{
    $xmlFile = __DIR__ . '/../ip_port/' . $imei . '.xml';
    if (file_exists($xmlFile)) {
        if (unlink($xmlFile)) {
            return "File '$xmlFile' deleted successfully.";
        } else {
            return "Error deleting file '$xmlFile'.";
        }
    } else {
        return "File '$xmlFile' does not exist.";
    }
}

function addChilds(SimpleXMLElement $xml, $dataList, $fallbackName = null, $exluded_keys = [], $attribute_keys = [], $child_keys = [], $addIndex = false)
{
    $index = 0;
    foreach ($dataList as $data) {
        $index = $index + 1;
        logMsg($data);

        $name = $data['name'] ?? $data["NAME"] ?? $fallbackName ?? throw new Exception("Name is required", 1);
        $name = empty($name) ? throw new Exception("Name is required", 1) : $name;
        $config = $xml->addChild(strtolower($name));

        if ($addIndex) {
            $config->addAttribute("id", htmlspecialchars($index));
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $exluded_keys)) {
                continue;
            }
            if (in_array($key, $attribute_keys) && !empty($key) && !isNullOrEmptyString($value)) {
                $key = transformKey($key);
                $config->addAttribute(strtolower($key), htmlspecialchars($value));
            } else if (in_array($key, $child_keys) && !empty($key) && !isNullOrEmptyString($value)) {
                $key = transformKey($key);
                $config->addChild(strtolower($key), htmlspecialchars($value));
            } else if (!empty($key) && !isNullOrEmptyString($value)) {
                $key = transformKey($key);
                $config->addAttribute(strtolower($key), htmlspecialchars($value));
            }
        }

        // Handle custom fields stored as JSON
        if (!empty($data['custom_fields'])) {
            $customFields = json_decode($data['custom_fields'], true);
            if (is_array($customFields)) {
                foreach ($customFields as $field) {
                    if (!empty($field['key']) && !isNullOrEmptyString($field['value']) && in_array($field['key'], $attribute_keys)) {
                        $field['key'] = transformKey($field['key']);
                        $config->addAttribute(strtolower($field['key']), htmlspecialchars($field['value']));
                    } else if (!empty($field['key']) && !isNullOrEmptyString($field['value']) && in_array($field['key'], $child_keys)) {
                        $field['key'] = transformKey($field['key']);
                        $config->addChild(strtolower($field['key']), htmlspecialchars($field['value']));
                    } else if (!empty($field['key']) && !isNullOrEmptyString($field['value'])) {
                        $field['key'] = transformKey($field['key']);
                        $config->addAttribute(strtolower($field['key']), htmlspecialchars($field['value']));
                    }
                }
            }
        }
    }
    return $xml;
}

function saveXml($imei, $configs, $otaUpdates = [], $xmlUpdates = [])
{
    $xmlFile = __DIR__ . '/../ip_port/' . $imei . '.xml';

    logMsg($configs);

    // $xml = file_exists($xmlFile) ? simplexml_load_file($xmlFile) : new SimpleXMLElement('<Routes></Routes>');
    $xml = new SimpleXMLElement('<servercommunication><!-- protocol: [ 1 = TCP, 2 = MQTT, 3 = HTTP ] --></servercommunication>');
    $xml->addAttribute("status", "1");
    $xml->addAttribute("backendservernos", count($configs));

    if (!empty($configs) && $configs != []) {
        $exluded_keys = ["id", "group_id", "imei", "created_at", "updated_at", "name", "NAME", "custom_fields", "group_name"];
        $attribute_keys = ["STATUS", "status"];
        $child_keys = [];
        addChilds($xml, $configs, null, $exluded_keys, $attribute_keys, $child_keys, false);
    }

    if (!empty($otaUpdates) && $otaUpdates != []) {
        $otaContainer = $xml->addChild('otaupdates');
        $exluded_keys = ["id", "group_id", "imei", "created_at", "updated_at", "name", "NAME", "custom_fields", "sub_group_id"];
        $attribute_keys = ["STATUS", "status"];
        $child_keys = [];
        addChilds($otaContainer, $otaUpdates, 'otaupdate', $exluded_keys, $attribute_keys, $child_keys, true);
    }

    if (!empty($xmlUpdates) && $xmlUpdates != []) {
        $xmlContainer = $xml->addChild('xmlupdates');
        $exluded_keys = ["id", "group_id", "imei", "created_at", "updated_at", "name", "NAME", "custom_fields", "sub_group_id"];
        $attribute_keys = ["STATUS", "status"];
        $child_keys = [];
        addChilds($xmlContainer, $xmlUpdates, 'xmlupdate', $exluded_keys, $attribute_keys, $child_keys, true);
    }

    $xml->asXML($xmlFile);
}
