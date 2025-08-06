<?php
$pdo = new PDO("mysql:host=localhost;dbname=mtc_primeedg;charset=utf8mb4", "mtc_primeedg", "oq7aFmbxA2OEJpkt");
$stmt = $pdo->query("SELECT DISTINCT device_id FROM itms_data_update ORDER BY device_id");
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));