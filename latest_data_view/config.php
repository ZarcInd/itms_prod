



<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_HOST', 'localhost');       // Or your actual DB host
define('DB_NAME', 'mtc_primeedg');    // Make sure this matches your DB
define('DB_USER', 'mtc_primeedg');       // Replace with actual username
define('DB_PASS', 'oq7aFmbxA2OEJpkt');   // Replace with actual password
define('CHUNK_SIZE', 200000);         // 2 lakh rows per chunk

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

