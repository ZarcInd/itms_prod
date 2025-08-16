<?php
$logFile = __DIR__ . "/script_debug.log";
function logMsg($msg) {
    global $logFile;
    $ts = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$ts] $msg\n", FILE_APPEND);
    echo $msg . "\n";
}

// = DB connection =
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=mtc_primeedg;charset=utf8",
        "mtc_primeedg",
        "oq7aFmbxA2OEJpkt"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    logMsg("✅ DB connected");
} catch (Exception $e) {
    logMsg("❌ DB Connection failed: " . $e->getMessage());
    exit(1);
}

// = Timestamps =
$runStartUtc = new DateTime("now", new DateTimeZone("UTC"));
$runStartIst = clone $runStartUtc;
$runStartIst->setTimezone(new DateTimeZone("Asia/Kolkata"));
$fixedUtcNow  = $runStartUtc;
$fixedUtcFrom = (clone $runStartUtc)->modify("-3 hours");
$fixedTimeStampIst = $runStartIst->format("Y-m-d H:i:s");
$utcFromStr = $fixedUtcFrom->format("Y-m-d H:i:s");
$utcNowStr  = $fixedUtcNow->format("Y-m-d H:i:s");
$expectedPacket = 1080;

logMsg("⏱️ Run window: $utcFromStr → $utcNowStr (UTC)");

// = Helpers =
function convertToDecimal($coord, $direction) {
    if (!$coord || $coord == 0) return 0.0;
    $degrees = (int)floor($coord / 100);
    $minutes = $coord - ($degrees * 100);
    $decimal = $degrees + ($minutes / 60);
    // Fix: comparison, not assignment
    if ($direction === 'S' || $direction === 'W') $decimal = -$decimal;
    return $decimal;
}
function haversine($lat1, $lon1, $lat2, $lon2) {
    // Earth radius in km
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $sinLat = sin($dLat * 0.5);
    $sinLon = sin($dLon * 0.5);
    $a = $sinLat * $sinLat + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * $sinLon * $sinLon;
    return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
}

try {
    // = Fetch devices =
    $devices = $pdo->query("SELECT DISTINCT device_id FROM itms_data_update")->fetchAll(PDO::FETCH_COLUMN);
    if (!$devices) {
        logMsg("⚠️ No devices found");
        exit(0);
    }
    $deviceCount = count($devices);
    logMsg("✅ Found $deviceCount devices");

    // Build IN list (numeric ids assumed)
    $deviceIds = array_map('intval', $devices);
    $deviceIdsList = implode(',', $deviceIds);

    // Vehicle info preload
    $vehicleLookup = [];
    if ($deviceIdsList !== '') {
        $vehicleData = $pdo->query("
            SELECT device_id, vehicle_no, depot 
            FROM vehicles 
            WHERE device_id IN ($deviceIdsList)
        ")->fetchAll();
        foreach ($vehicleData as $v) {
            $vehicleLookup[(int)$v['device_id']] = $v;
        }
        unset($vehicleData);
    }

    // Stats info preload: latest per device via window function
    $statsLookup = [];
    if ($deviceIdsList !== '') {
        $statsData = $pdo->query("
            SELECT device_id, pkt_count, first_pkt, last_pkt
            FROM (
                SELECT device_id, pkt_count, first_pkt, last_pkt,
                       ROW_NUMBER() OVER (PARTITION BY device_id ORDER BY updated_at DESC) AS rn
                FROM itms_device_stats 
                WHERE device_id IN ($deviceIdsList)
            ) ranked
            WHERE rn = 1
        ")->fetchAll();
        foreach ($statsData as $s) {
            $statsLookup[(int)$s['device_id']] = $s;
        }
        unset($statsData);
    }

    // = Bulk fetch GPS data for all devices in one go =
    logMsg("📥 Fetching GPS data for all devices in one query...");
    $gpsSql = "
        SELECT device_id, lat, lat_dir, lon, lon_dir, created_at, firmware_version
        FROM itms_data
        WHERE device_id IN ($deviceIdsList)
          AND created_at BETWEEN :from AND :to
        ORDER BY device_id, created_at ASC
    ";
    $gpsStmt = $pdo->prepare($gpsSql);
    $gpsStmt->execute([':from' => $utcFromStr, ':to' => $utcNowStr]);
    $gpsData = $gpsStmt->fetchAll();
    logMsg("📦 Fetched " . count($gpsData) . " GPS rows");

    // Group GPS by device_id
    $gpsGrouped = [];
    foreach ($gpsData as $row) {
        $did = (int)$row['device_id'];
        $gpsGrouped[$did][] = $row;
    }
    unset($gpsData);

    // Prepare results for batch insert
    $insertRows = [];
    $processed = 0;

    foreach ($deviceIds as $deviceId) {
        if (($processed % 500) === 0) {
            logMsg("➡️ Processing device $deviceId (" . ($processed+1) . "/$deviceCount)");
        }
        $processed++;

        // Vehicle lookup
        $fleetNo = 'Unknown';
        $depo    = 'Unknown';
        if (isset($vehicleLookup[$deviceId])) {
            $vi = $vehicleLookup[$deviceId];
            if (isset($vi['vehicle_no'])) $fleetNo = $vi['vehicle_no'];
            if (isset($vi['depot']))      $depo    = $vi['depot'];
        }

        $points = $gpsGrouped[$deviceId] ?? [];
        $totalPackets3h = ($points ? count($points) : 0);

        $odoMeter = 0.0;
        $firstPacketUTC = null;
        $lastPacketUTC  = null;
        $firmware = '';

        if ($totalPackets3h > 0) {
            $prevLat = null;
            $prevLon = null;
            $lastIdx = $totalPackets3h - 1;

            foreach ($points as $idx => $p) {
                $lat = convertToDecimal((float)$p['lat'], $p['lat_dir']);
                $lon = convertToDecimal((float)$p['lon'], $p['lon_dir']);

                // Track first/last timestamps regardless of zero coords,
                // but skip distance when invalid
                if ($idx === 0) $firstPacketUTC = $p['created_at'];
                $lastPacketUTC = $p['created_at'];

                if ($lat == 0.0 || $lon == 0.0) {
                    if ($idx === $lastIdx && isset($p['firmware_version'])) {
                        $firmware = (string)$p['firmware_version'];
                    }
                    continue;
                }

                if ($prevLat !== null) {
                    $odoMeter += haversine($prevLat, $prevLon, $lat, $lon);
                }

                $prevLat = $lat;
                $prevLon = $lon;

                if ($idx === $lastIdx && isset($p['firmware_version'])) {
                    $firmware = (string)$p['firmware_version'];
                }
            }
        }

        // Last packet IST or '0'
        if ($lastPacketUTC) {
            $tmp = new DateTime($lastPacketUTC, new DateTimeZone("UTC"));
            $tmp->setTimezone(new DateTimeZone("Asia/Kolkata"));
            $lastPktIST = $tmp->format("Y-m-d H:i:s");
        } else {
            $lastPktIST = '0';
        }

        // First packet IST from stats lookup if available
        $firstPktIST = '0';
        if (isset($statsLookup[$deviceId])) {
            $stats = $statsLookup[$deviceId];
            if (!empty($stats['first_pkt']) && $stats['first_pkt'] !== '0') {
                $dt = new DateTime($stats['first_pkt'], new DateTimeZone("UTC"));
                $dt->setTimezone(new DateTimeZone("Asia/Kolkata"));
                $firstPktIST = $dt->format("Y-m-d H:i:s");
            }
        }

        $deviceStatus = ($totalPackets3h > 0 ? "Online" : "Offline");

        $insertRows[] = [
            $deviceId,
            $fleetNo,
            $depo,
            $deviceStatus,
            $firmware,
            $totalPackets3h,
            $expectedPacket,
            round($odoMeter, 3),
            $firstPktIST,
            $lastPktIST,
            $fixedTimeStampIst
        ];
    }

    // Batch insert
    if ($insertRows) {
        logMsg("📝 Inserting " . count($insertRows) . " rows into device_data...");
        $placeholders = [];
        $params = [];
        foreach ($insertRows as $row) {
            $placeholders[] = "(?,?,?,?,?,?,?,?,?,?,?)";
            // Avoid array_merge in a loop for performance
            foreach ($row as $val) $params[] = $val;
        }
        $sql = "INSERT INTO device_data 
                (device_id, fleet_no, depo, device_status, firmware_version, total_packet, expected_packet, odo_meter, start_packet, last_packet, time_stamp)
                VALUES " . implode(',', $placeholders);

        try {
            // Transaction for durability and speed
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $pdo->commit();
            logMsg("✅ Batch insert completed");
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            logMsg("❌ Batch insert failed: " . $e->getMessage());
        }
    } else {
        logMsg("ℹ️ No rows to insert");
    }

    logMsg("🎉 Done processing $deviceCount devices");
} catch (Exception $e) {
    logMsg("💥 Fatal error: " . $e->getMessage());
}
exit(0);
