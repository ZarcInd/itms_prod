<?php
require_once __DIR__ . "/db.php"; // Include database connection

header("Content-Type: application/json");

try {
    $stmt = $pdo->query("SELECT DISTINCT device_id FROM itms_device_stats ORDER BY device_id ASC");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($devices);
} catch (Exception $e) {
    echo json_encode([]);
}
?>