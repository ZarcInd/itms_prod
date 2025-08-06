<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Export Raw Data</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container {
            width: 100% !important;
        }
        /* Center spinner vertically */
        #loadingSpinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1051; /* Above modal backdrop */
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4 text-center">Export Raw Data</h2>

    <div id="loadingSpinner" class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">Loading device list...</div>
    </div>

    <form id="exportForm" action="export.php" method="get" class="mx-auto" style="max-width: 480px;">
        <div class="mb-3">
            <label for="date" class="form-label">Select Date:</label>
            <input
                type="date"
                id="date"
                name="date"
                class="form-control"
                required
            />
        </div>

        <div class="mb-4">
            <label for="deviceID" class="form-label">Select Device ID:</label>
            <select
                name="device_id"
                id="deviceID"
                class="form-select"
                required
            >
                <option value="">Select Device</option>
            </select>
        </div>

        <button id="submitBtn" type="submit" class="btn btn-primary w-100">Download CSV</button>
       <div id="submitLoader" style="display: none; margin-top: 10px;">
    <div class="spinner-border text-primary" role="status"></div>
    <div>Preparing your download...</div>
</div>
    </form>
    

 <div id="submitLoader" style="display: none; margin-top: 10px; margin-left: 100px;">
    <div class="spinner-border text-primary" role="status"></div>
    <div>Preparing your download...</div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        const spinner = $("#loadingSpinner");
        const deviceDropdown = $("#deviceID");

        spinner.show();

        $.ajax({
            url: "get_devices.php",
            type: "GET",
            dataType: "json",
            success: function (data) {
                deviceDropdown.empty();
                deviceDropdown.append('<option value="">Select Device</option>');
                data.forEach(function (device) {
                    deviceDropdown.append(
                        `<option value="${device.device_id}">${device.device_id}</option>`
                    );
                });

                deviceDropdown.select2({
                    placeholder: "Select Device",
                    allowClear: true,
                    width: "100%",
                });
            },
            error: function () {
                alert("Failed to load device IDs.");
            },
            complete: function () {
                spinner.hide();
            },
        });
    });
</script>
<script>
document.getElementById('exportForm').addEventListener('submit', function () {
    // Show loader and disable submit
    document.getElementById('submitLoader').style.display = 'block';
    document.getElementById('submitBtn').disabled = true;

    // Start polling for the download cookie
    const poll = setInterval(() => {
        if (document.cookie.includes('fileDownload=true')) {
            clearInterval(poll);
            document.getElementById('submitLoader').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;

            // Clear cookie
            document.cookie = 'fileDownload=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }
    }, 1000);
});
</script>
</body>
</html>
