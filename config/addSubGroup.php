<?php

require_once __DIR__ . "/db.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $groupId = trim($_POST["group_id"]);
    $subGroupName = trim($_POST["sub_group_name"]);
    $imeiNumbers = isset($_POST["imei_values"]) ? explode(",", $_POST["imei_values"]) : [];
    $csvFile = $_FILES["csv_file"];

    if (empty($groupId)) {
        die("Group is required.");
    }

    if (empty($subGroupName)) {
        die("Sub Group Name is required.");
    }

    $groupCreateQuery = "INSERT INTO itms_groups (group_name, parent_group) values (?, ?)";

    $res = execute_query($groupCreateQuery, [$subGroupName, $groupId]);

    if (!$res['status']) {
        die("Unable to create sub group.");
    }

    $groupFindQuery = "SELECT * FROM itms_groups where group_name = ?";
    $res = execute_query($groupFindQuery, [$subGroupName]);

    if (!$res['status']) {
        die("Unable to find sub group.");
    }

    $res['data'] = $res['data']->fetchAll(PDO::FETCH_ASSOC);
    $subGroupId = $res['data'][0]['id'];

    $updateCount = 0;

    // Process manually entered IMEIs
    foreach ($imeiNumbers as $imei) {
        if (!empty($imei)) {
            $res = update_imei_sub_group($imei, $subGroupId, $groupId);
            if ($res['status']) {
                $updateCount++;
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
                    $res = update_imei_sub_group($imei, $subGroupId, $groupId);
                    if ($res['status']) {
                        $updateCount++;
                    }
                }
            }
            fclose($handle);
        }
    }

    echo "Successfully created sub group & updated $updateCount IMEIs.";
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
