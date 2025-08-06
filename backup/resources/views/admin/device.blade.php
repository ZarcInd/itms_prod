@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
        <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h4 class="display-6 fw-bold" id="totle_device"></h4>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                       <i class="bi bi-device-ssd text-dark" style="font-size: 25px;"></i>
                                        Total Device
                                     </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold" id="communication_lost"></h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot red-dot"></div>
                                        <i class="bi bi-device-ssd text-dark" style="font-size: 25px;"></i>
                                             Communication Lost
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold" id="delay_communicating"></h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot orange-dot"></div>
                                        <i class="bi bi-device-ssd text-dark" style="font-size: 25px;"></i>
                                             Delay Communicating
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold" id="communicating"></h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot green-dot"></div>
                                        <i class="bi bi-device-ssd text-dark" style="font-size: 25px;"></i>
                                             Communicating
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                  
               </div>
              
            </div>
        <div class="card">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Device Management</h4>
                <div>          
                             <div style="display:flex">
                                <span><h6 class="m-2" style="color: black;"> <i class="fa fa-filter"></i>Status Filter</h6></span>
                                <select name="status[]" id="status-filter" class="form-control mb-3" multiple>
                                    <option value="All" @if(in_array('All', Request::get('status', []))) selected @endif>All</option>
                                    <option value="communation_lost" @if(in_array('communation_lost', Request::get('status', []))) selected @endif>Communication Lost</option>
                                    <option value="daly_community" @if(in_array('daly_community', Request::get('status', []))) selected @endif>Delay Communicating</option>
                                    <option value="community" @if(in_array('community', Request::get('status', []))) selected @endif>Communicating</option>
                                </select>
                            </div>
                 @if($role === 'sub-admin' && (!isset($accesss['download']) || $accesss['download'] != '1'))
                 
                @else    
                <a href="{{ route('device.export') }}" class="btn btn-sm btn-light me-2" id="btn-exportcsv">
                    <i class="fas fa-file-export"></i> CSV Format
                </a>
                @endif

                @if($role === 'sub-admin' && (!isset($accesss['import']) || $accesss['import'] != '1'))
                 
                @else
                <button type="button" class="btn btn-sm btn-light me-2" id="btn-import" data-url="{{ route('device.import') }}">
                    <i class="fas fa-file-import"></i> Import CSV
                </button>
                @endif

                 @if($role === 'sub-admin' && (!isset($accesss['export']) || $accesss['export'] != '1'))
                 
                 @else
                <input type="file" id="csv-file-input" accept=".csv" style="display: none;">
                  <input type="text" id="csv-file-input-filter" style="display: none;">
                    <a href="{{ route('devicecsv.export') }}" class="btn btn-sm btn-light me-2" id="btn-export">
                        <i class="fas fa-file-export"></i> Export CSV
                    </a>
                 @endif   

                  @if($role === 'sub-admin' && (!isset($accesss['add']) || $accesss['add'] != '1'))
                 
                  @else
                    <button type="button" class="btn btn-sm btn-light" id="btn-add">
                        <i class="fas fa-plus"></i> Add Device
                    </button>

                  @endif   
                    
                </div>
            </div>
<div id="iframeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
  <div style="background:white; width:90%; height:90%; position:relative; box-shadow:0 0 10px black;">
    <button onclick="closeIframe()" style="position:absolute; top:10px; right:15px; z-index:1001; background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
    <iframe id="iframeContent" src="" style="width:100%; height:100%; border:none;"></iframe>
  </div>
</div>            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="device-table">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Device ID</th>
                                <th>Vehicles No</th>
                                <th>Agency</th>
                                <th>Depot</th>
                                <th>Firmware Version</th>
                                <th>Network</th>
                                <th>Last Seen Packet</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

</main>

<!-- Add modal -->
<div class="modal fade" id="device-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white" style="background-color: #775DA6;">
                <h5 class="modal-title" id="modal-title">Add Device</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_form" class="form_submit" enctype="multipart/form-data" action="{{ route('device.store') }}">
                @csrf
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device_id">Device ID</label>
                            <input type="text"  name="device_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_no">Vehicle No</label>
                            <input type="text" name="vehicle_no" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                            <label for="longitude">Agency</label>
                            <input type="text"  name="agency" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Latitude">Depot</label>
                            <input type="text"  name="depot" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                            <label for="Protocol">Protocol</label>
                            <input type="text"  name="protocol" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Last Posted at">Region</label>
                            <input type="text"   name="region_id" class="form-control" required>
                        </div>
                    </div>
                    
                    
                   
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn submit_button" style="background-color: #7e57c2; color: white;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>  


<!-- edit modal -->

<div class="modal fade" id="edit_device-modal"  tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white" style="background-color: #775DA6;">
                <h5 class="edit_modal-title" >Edit Device</h5>
                <button type="button" class="btn-close btn-close-white" id="addmidal"  data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form_submit" id="edit_device-form" enctype="multipart/form-data" action="{{ route('device.update') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device_id">Device ID</label>
                            <input type="text" id="device_id" name="device_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_no">Vehicle No</label>
                            <input type="text" id="vehicle_no" name="vehicle_no" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6 mb-3">
                            <label for="longitude">Agency</label>
                            <input type="text" id="agency" name="agency" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Latitude">Depot</label>
                            <input type="text" id="depot" name="depot" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="Protocol">Protocol</label>
                            <input type="text"  id="protocol" name="protocol" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Last Posted at">Region</label>
                            <input type="text" id="region_id"  name="region_id" class="form-control" required>
                        </div>
                    </div>
                   
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn submit_button" style="background-color: #7e57c2; color: white;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>  


<!-- show inf vehicle -->
    <div class="modal fade" id="show_device-modal" tabindex="-1" aria-hidden="true" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1053;">
        <div class="modal-dialog modal-lg" style="margin: 50px auto; max-width: 800px; position: relative;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                <!-- Modal Header -->
                <div class="modal-header" style="background-color: #775DA6; color: white; padding: 15px 20px; border-bottom: 1px solid #6a529a;">
                    <div class="d-flex align-items-center" style="display: flex; align-items: center;">
                        <h5 class="edit_modal-title mb-0" style="margin: 0; font-size: 18px; font-weight: 600;">Device Tracking Information</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" id="addmidal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; outline: none;">✕</button>
                </div>
                
                <!-- Modal Body -->
                <div class="modal-body" style="padding: 0; background-color: white;">
                    <!-- Device Info Cards -->
                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <!-- Left Column -->
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <!-- Device ID Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #775DA6;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">DEVICE ID</div>
                                    <div id="modal-device_id_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                <!-- Vehicle Number Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #28a745;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">VEHICLE NUMBER</div>
                                    <div id="modal-vehicle_no_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                <!-- Protocol Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #17a2b8;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">PROTOCOL</div>
                                    <div id="modal-protocol_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                <!-- GPS Signal Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #ffc107;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">GPS SIGNAL</div>
                                    <div id="modal-gps_signal_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                <!-- Packet Status Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #dc3545;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">PACKET STATUS</div>
                                    <div id="modal-packet_status_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                 <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #dc3545;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">Network</div>
                                    <div id="modal-natwork_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <!-- Location Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #775DA6;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">LOCATION (LAT/LON)</div>
                                    <div style="font-size: 16px; font-weight: 600;">
                                        <span id="modal-lat_m">-</span> / <span id="modal-lon_m">-</span>
                                    </div>
                                </div>
                                
                                <!-- Time Info Card -->
                                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 4px solid #6c757d;">
                                    <div style="margin-bottom: 5px; font-size: 12px; color: #6c757d;">TIME IN PACKET</div>
                                    <div id="modal-time_in_packet_m" style="font-size: 16px; font-weight: 600;">-</div>
                                </div>
                                
                                <!-- Delay Card -->
                               
                            </div>
                        </div>
                        
                       
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 20px; display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #6c757d; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-right: 10px;">Close</button>
                    
                </div>
            </div>
        </div>
    </div>


<!-- Optional: Import Progress/Status Modal -->
<div class="modal fade" id="import-status-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white">
                <h5 class="modal-title">Importing Vehicles</h5>
                <button type="button" class="btn-close btn-close-white" id="editmidal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="progress mb-3">
                    <div id="import-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <div id="import-status">Please wait while your CSV file is being processed...</div>
                <div id="import-results" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('public/backend/js/device.js')}}"></script>

<script>
  var table;
    let isStatusFilterResponse = false;
    
    $('#status-filter').on('change', function() {
        $('#filter-form').submit(); // Assuming your filters are in a form with this ID
       $('#csv-file-input-filter').val($(this).val()); // Set value to hidden input
        table.ajax.reload(null, false); // Redraw the table with new filter
        isStatusFilterResponse = true; 
       });
    
    
 $(document).ready(function () {
   
    
    let customValue = 0;
  
  
    
    // Initialize DataTable with loader
     table = $('#device-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("device.index") }}',
            data: function (d) {
                d.status = $('#status-filter').val(); // Add status filter parameter
                d.customValue = customValue;
                d.isStatusFilterResponse = isStatusFilterResponse;
            },
            beforeSend: function (xhr, settings) {
               if (customValue > 0 && !isStatusFilterResponse) {
                    toggleLoader(false); // Hide loader for automatic refreshes
                } else {
                    toggleLoader(true); // Show loader for initial load or status filter change
                }
            },
            complete: function() {
                toggleLoader(false); // Hide loader after request completes
                if (isStatusFilterResponse) {
                    isStatusFilterResponse = false; // Reset flag after status filter request completes
                }
            }
        },
        columns: [
            {data: 'DT_RowIndex'},
            {data: 'newdeviceid'},
            {data: 'vehicle_no'}, 
            {data: 'agency'}, 
            {data: 'depot'},
            {data: 'firmversion'},
            {data: 'network'},
            {data: 'time_date'},
            {data: 'status'},
            {data: 'action'},
        ]
    });
    
   
    // Show loader on status filter change
     // Handle the form submission when selection changes
   
    // Handle automatic table refresh without loader
    setInterval(function() {
        customValue += 1;
        table.ajax.reload(null, false); // Reload table every 10s without resetting page
    }, 60000);
});
 
$('#status-filter').select2({
        placeholder: "Select Status",
        allowClear: true
    });
  
  
    function fetchDeviceStatus() {
      $.ajax({
          url: '{{ route("device.count") }}',
          method: 'GET',
          dataType: 'json',
          success: function(data) {
              $('#totle_device').html(data.status_summary.total);
              $('#communication_lost').html(data.status_summary.communication_lost);
              $('#delay_communicating').html(data.status_summary.daly_community); // corrected
              $('#communicating').html(data.status_summary.communicating);
          },
          error: function(xhr, status, error) {
              console.error('Error fetching device status:', error);
          }
      });
  }

  $(document).ready(function() {
      fetchDeviceStatus(); // Fetch immediately
      setInterval(fetchDeviceStatus, 60000); // Refresh every 10 seconds
  });

function openIframe(url) {
    document.getElementById("iframeContent").src = url;
    document.getElementById("iframeModal").style.display = "flex";
  }

  function closeIframe() {
    document.getElementById("iframeModal").style.display = "none";
    document.getElementById("iframeContent").src = "";
  }
</script>
@endsection