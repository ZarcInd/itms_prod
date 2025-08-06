<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Device Data</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchDeviceData() {
            $.ajax({
                url: "get_latest_devices_withcan.php",
                method: "GET",
                success: function(data) {
                    $("#deviceTable").html(data);
                }
            });
        }

        $(document).ready(function() {
            fetchDeviceData(); // Load data initially
            setInterval(fetchDeviceData, 10000); // Refresh every 10 seconds
        });
    </script>
</head>
<body>

    <h2>Live Device Data</h2>
    <div id="deviceTable">Loading...</div>

</body>
</html>
