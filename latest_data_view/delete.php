

<?php

// Database connection
$host = 'localhost';
$dbname = 'itms_staging_db';
$username = 'itms_staging_app';
$password = 'Staysafe@01';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Control file path
$lockFile = 'delete_lock.txt';

// Handle start/stop requests
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'start') {
        file_put_contents($lockFile, 'running');
    } elseif ($_POST['action'] == 'stop') {
        file_put_contents($lockFile, 'stopped');
    }
}

// HTML interface
echo '<form method="post">';
if (file_exists($lockFile) && trim(file_get_contents($lockFile)) == 'running') {
    echo '<button type="submit" name="action" value="stop">Stop Deletion</button>';
} else {
    echo '<button type="submit" name="action" value="start">Start Deletion</button>';
}
echo '</form>';

// Batch delete process
if (file_exists($lockFile) && trim(file_get_contents($lockFile)) == 'running') {
    while (trim(file_get_contents($lockFile)) == 'running') {
        $stmt = $pdo->prepare("DELETE FROM itms_data limit 500000");
        $stmt->execute();

        echo "Deleted 500000 records.<br>";
        ob_flush();
        flush();

        // Wait for 1 second
        sleep(1);
    }
    echo "Deletion stopped.<br>";
}
