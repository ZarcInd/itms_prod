<?php

require_once __DIR__ . "/db.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $groupName = trim($_POST["group_name"]);
    $imeiNumbers = isset($_POST["imei_values"]) ? explode(",", $_POST["imei_values"]) : [];
    $csvFile = $_FILES["csv_file"];

    if (empty($groupName)) {
        die("Group Name is required.");
    }

    $groupCreateQuery = "INSERT INTO itms_groups (group_name) values (?)";

    $res = execute_query($groupCreateQuery, [$groupName]);

    if (!$res['status']) {
        die("Unable to create group.");
    }

    $groupFindQuery = "SELECT * FROM itms_groups where group_name = ?";
    $res = execute_query($groupFindQuery, [$groupName]);

    if (!$res['status']) {
        die("Unable to create group.");
    }

    $res['data'] = $res['data']->fetchAll(PDO::FETCH_ASSOC);
    $groupId = $res['data'][0]['id'];

    $imeiData = [
        "imei" => "",
        "fleet_no" => null,
        "depo_name" => null,
        "group_id" => $groupId,
    ];

    $insertCount = 0;

    // Process manually entered IMEIs
    foreach ($imeiNumbers as $imei) {
        if (!empty($imei)) {
            $imeiData["imei"] = $imei;
            $res = upsert_imei($imeiData);
            if ($res['status']) {
                $insertCount++;
            }
        }
    }

    // Process CSV file if uploaded
    if ($csvFile["size"] > 0) {
        $fileTmpPath = $csvFile["tmp_name"];
        $handle = fopen($fileTmpPath, "r");

        if ($handle !== false) {
            fgetcsv($handle); // Skip header row
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if (count($data) === 3) {
                    list($imei, $fleet_no, $depot_name) = $data;
                    $imeiData["imei"] = $imei;
                    $imeiData["fleet_no"] = $fleet_no;
                    $imeiData["depo_name"] = $depot_name;
                    $res = upsert_imei($imeiData);
                    if ($res['status']) {
                        $insertCount++;
                    }
                }
            }
            fclose($handle);
        }
    }

    echo "Successfully created group & added/updated $insertCount IMEIs.";
    echo "Redirecting to home in <span id='duration'></span> seconds.";
    echo "<script>
        let duration = 10;
        setTimeout(() => {
            window.location.href = '/config';
        }, duration * 1000);
        document.getElementById('duration').innerHTML = duration;
        setInterval(() => {
            duration -= 1;
            document.getElementById('duration').innerHTML = duration;
        }, 1000);
    </script>";
}
