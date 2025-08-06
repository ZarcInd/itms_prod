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
                url: "stag_device_data.php",
                method: "GET",
                success: function(data) {
                    $("#deviceTable").html(data);
                }
            });
        }

        $(document).ready(function() {
            fetchDeviceData(); // Load data initially
            setInterval(fetchDeviceData, 2000); // Refresh every 2 seconds
        });
    </script>
</head>
<body>

    <h2>Live Device Data</h2>
    <div id="deviceTable">Loading...</div>

</body>
</html>
