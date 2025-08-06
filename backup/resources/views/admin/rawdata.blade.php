@extends('admin/layouts/master')
@section('container')
<style>
          :root {
            --theme-color: #775DA6;
            --theme-light: #8A73B5;
            --theme-dark: #6A4D95;
        }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .filter-header {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .filter-item {
            display: flex;
            flex-direction: column;
            min-width: 200px;
        }
        
        .filter-item label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        /* Theme Color Buttons */
        .btn-primary {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
        }
        
        .btn-primary:hover {
            background-color: var(--theme-dark);
            border-color: var(--theme-dark);
        }
        
        .btn-primary:focus, .btn-primary.focus {
            box-shadow: 0 0 0 0.2rem rgba(119, 93, 166, 0.5);
        }
        
        /* Custom Select2 styling with theme color */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            display: flex;
            min-height: 38px;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        /* Theme color for Select2 focus and selected states */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--theme-color);
            display: flex;
            box-shadow: 0 0 0 0.2rem rgba(119, 93, 166, 0.25);
        }
        
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--theme-color);
        }
        
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--theme-light);
            color: white;
        }
        
        /* Disabled state styling */
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection {
            background-color: #e9ecef;
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection__rendered {
            color: #6c757d;
        }
        
        #rawdatalist_filter{
            text-align: end;
        }
        
        /* Fix Select2 dropdown height and scrolling issues */
.select2-container--bootstrap-5 .select2-dropdown {
    max-height: 200px !important;
    overflow-y: auto !important;
    z-index: 9999 !important;
    border: 1px solid #ced4da;
    border-top: none;
}

</style> 
<main class="main-content">
    <div class="container-fluid px-4">
    <h2 class="mb-4">Raw Data</h2>
        <div class="card">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Raw Data Report</h4>
           </div>
                <div class="container-fluid">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
               <div class="filter-item">
                    <label for="packet_type">Packet Type*</label>
                    <select name="packet_type" id="packet_type" class="form-control">
                        <option value="">Select Packet Type</option>
                        <option value="raw_data">Raw Data</option>
                        <option value="can_data">Can Data</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="fleet_number">Fleet Number*</label>
                    <select name="fleet_number" id="fleet_number" class="form-control">
                        <option value="">Select Fleet Number</option>
                        @foreach($device_data as $device_id)
                         <option value="{{$device_id->device_id}}" >{{$device_id->vehicle_no}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-item">
    <label for="device_number">Device Number*</label>
    <select name="device_number" id="device_number" class="form-control">
        <option value="">Select Device Number</option>
        @foreach($device_numbers as $device)
         <option value="{{$device->device_id}}" >{{$device->device_id}}</option>
        @endforeach
    </select>
</div>
                
                <div class="filter-item">
                    <label for="date_filter">Date*</label>
                    <input type="date" name="date_filter" id="date_filter" class="form-control" value="2025-05-26">
                </div>
                
                <div class="filter-item d-none">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary" id="search-btn">Search</button>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="btn-group col-4">
                <input type="file" id="csv-file-input" accept=".csv" style="display: none;">
                     <button type="button" href="{{ route('rowdata.export') }}" class="btn btn-success" id="btn-export">
                         <i class="fas fa-file-excel"></i>Export to Excel
                    </button>
               <!-- <button type="button" class="btn btn-secondary">
                    <i class="fas fa-refresh"></i> Refresh
                </button> -->
            </div>
        </div>

            </div>    
            <div class="card-body d-none">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="row_data-table">
                        <thead class="table-header">
                            <tr>
                                <th>Sr.No</th>
                                <th>Region</th>
                                <th>Depot</th>
                                <th>Fleet Number</th>
                                <th>Device Unique ID</th>
                                <th>Packet Header</th>
                                <th>Mode</th>
                                <th>Device Type</th>
                                <th>Packet Type</th>
                                <th>Framware Version</th>
                                <th>Time</th>
                                <th>date</th>
                                <th>Speed / Kmh</th>
                                <th>Oil Pressure</th>
                                <th>Server Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
      
      <!--  User dowllound list -->
      
       <div class="card mt-4">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Raw Data List</h4>
           </div>
                
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="rawdatalist">
                        <thead class="table-header">
                            <tr>
                                <th>Sr.No</th>
                                <th>Packet Type</th>
                                <th>Fleet Number</th>
                                <th>Device Unique ID</th>
                                <th>date</th>
                                <th>Status</th>
                                <th>Action</th>
                              </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

</main>

<script>
  
   let table;
$(document).ready(function () {
   
    // Initialize DataTable with loader
    table = $('#rawdatalist').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("userrowlist") }}',
            
            beforeSend: function (xhr, settings) {
                    toggleLoader(true); // Show loader for initial load or status filter change
               },
            complete: function() {
                toggleLoader(false); // Hide loader after request completes
               
            }
        },
        columns: [
            {data: 'DT_RowIndex'},
            {data: 'packettype'},
            {data: 'fleet_number'}, 
            {data: 'device_number'}, 
            {data: 'date_filter'},
            {data: 'status'},
            {data: 'action'},
        ]
    });
    
   
});
  
  
  
 let rowdatatable;

// Initialize Select2 for all dropdowns
$('#packet_type').select2({
   theme: 'bootstrap-5',
    placeholder: 'Select packet type...',
    allowClear: false,
    width: '100%'
});

$('#fleet_number').select2({
    theme: 'bootstrap-5',
    placeholder: 'Select fleet number...',
    allowClear: true,
    width: '100%'
});

$('#device_number').select2({
    theme: 'bootstrap-5',
    placeholder: 'Select device number...',
    allowClear: true,
    width: '100%'
});

// Demo selects
$('#searchable_select').select2({
    theme: 'bootstrap-5',
    placeholder: 'Search and select...',
    allowClear: true,
    width: '100%'
});

$('#multi_select').select2({
    theme: 'bootstrap-5',
    placeholder: 'Select multiple options...',
    allowClear: true,
    width: '100%'
});

$('#tags_select').select2({
    theme: 'bootstrap-5',
    placeholder: 'Add tags...',
    allowClear: true,
    tags: true,
    width: '100%'
});

// Mutual exclusion: Fleet Number disables Device Number
$('#fleet_number').on('select2:select', function (e) {
    const selectedValue = e.params.data.id;
    if (selectedValue) {
        $('#device_number').prop('disabled', true).val(null).trigger('change');
    }
});

$('#fleet_number').on('select2:clear', function () {
    $('#device_number').prop('disabled', false);
});

// Mutual exclusion: Device Number disables Fleet Number
$('#device_number').on('select2:select', function (e) {
    const selectedValue = e.params.data.id;
    if (selectedValue) {
        $('#fleet_number').prop('disabled', true).val(null).trigger('change');
    }
});

$('#device_number').on('select2:clear', function () {
    $('#fleet_number').prop('disabled', false);
});

// Optional packet type select handler
$('#packet_type').on('select2:select', function (e) {
    // Add logic here if needed
});
  
  $('#search-btn').on('click', function () {
    const formData = {
        packet_type: $('#packet_type').val(),
        fleet_number: $('#fleet_number').val(),
        device_number: $('#device_number').val(),
        date: $('#date_filter').val()
    };

    // Validation
   if (!formData.packet_type) {
       alert('Please select a packet type');
        return;
    }

    if (!formData.fleet_number && !formData.device_number) {
        alert('Please select either Fleet Number or Device Number');
        return;
    }

    if (!formData.date) {
        alert('Please select a date');
        return;
    }

    console.log('Search clicked with data:', formData);

    // Reload existing DataTable with new filters
    if (rowdatatable) {
        rowdatatable.ajax.reload();
    } else {
        initializeDataTable();
    }
});


// Refresh button click handler
$('#refresh-btn').on('click', function () {
    // Reset form values
  //  $('#packet_type').val('raw_data').trigger('change');
    $('#fleet_number').val(null).trigger('change');
    $('#device_number').val(null).trigger('change');
    $('#date_filter').val();

    // Re-enable selects
    $('#fleet_number').prop('disabled', false);
    $('#device_number').prop('disabled', false);

    // Reload DataTable
    if (rowdatatable) {
        rowdatatable.ajax.reload();
    }

    console.log('Form reset and table reloaded');
});

function initializeDataTable() {
    rowdatatable = $('#row_data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{route('rawdata.index')}}",
            data: function (d) {
                d.packet_type = $('#packet_type').val();
                d.fleet_number = $('#fleet_number').val();
                d.device_number = $('#device_number').val();
                d.date_filter = $('#date_filter').val();
                console.log('DataTable request data:', d);
            },
            beforeSend: function () {
                toggleLoader(true);
            },
            complete: function () {
                toggleLoader(false);
            },
            error: function (xhr, error) {
                console.log('DataTable error:', error);
                toggleLoader(false);
                alert('Error loading data. Please try again.');
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'region', name: 'region' },
            { data: 'depot', name: 'depot' },
            { data: 'fleet_number', name: 'fleet_number' },
            { data: 'device_device_id', name: 'device_device_id' },
            { data: 'packet_header', name: 'packet_header' },
            { data: 'mode', name: 'mode' },
            { data: 'device_type', name: 'device_type' },
            { data: 'packet_type', name: 'packet_type' },
            { data: 'firmware_version', name: 'firmware_version' },
            { data: 'time', name: 'time' },
            { data: 'date', name: 'date' },
            { data: 'speed_kmh', name: 'speed_kmh' },
            { data: 'oil_pressure', name: 'oil_pressure' },
            { data: 'servertime', name: 'servertime' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        language: {
            processing: "Processing...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            loadingRecords: "Loading...",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table"
        }
    });
}


$(document).ready(function() {
    $('#btn-export').on('click', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...');
        $btn.prop('disabled', true);

        // Use FormData to send form-style POST data
        const formData = new FormData();
        formData.append('packet_type', $('#packet_type').val());
        formData.append('fleet_number', $('#fleet_number').val());
        formData.append('device_number', $('#device_number').val());
        formData.append('date_filter', $('#date_filter').val());
         const exportUrl = $btn.attr('href');
        $.ajax({
            url: exportUrl,
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showNotification('Export processed successfully!', 'success');
                table.ajax.reload(null, true);
                // You can also display data like:
                // console.log(response.data);
            },
            error: function (xhr) {
                showNotification('Failed to process export. Please try again.', 'error');
            },
            complete: function () {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            }
        });
    });

   
    // Helper function to extract filename from headers
    function getFilenameFromHeader(xhr) {
        const disposition = xhr.getResponseHeader('Content-Disposition');
        if (disposition && disposition.includes('filename=')) {
            return disposition.split('filename=')[1].replace(/['"]/g, '');
        }
        return null;
    }
  
      // Helper function for notifications (implement as needed)
    function showNotification(message, type) {
        // You can replace this with your preferred notification system
        if (typeof toastr !== 'undefined') {
            // If you're using toastr
            toastr[type](message);
        } else {
            // Simple alert fallback
            alert(message);
        }
    }
   }); 

</script>  
@endsection