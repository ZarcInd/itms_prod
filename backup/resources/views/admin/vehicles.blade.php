@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
    <h2 class="mb-4">Vehicle</h2>
    <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h4 class="display-6 fw-bold" id="total_vehicles"></h4>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                        Total vehicles
                                     </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold" id="online_count"></h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot green-dot"></div>
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                             Online vehicles
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold" id="offline_count"></h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot red-dot"></div>
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                             Offline vehicles
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                  
               </div>
              
            </div>
        <div class="card">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Vehicle Management</h4>
                <div>
                      <div style="display:flex">
                                <span><h6 class="m-2" style="color: black;"> <i class="fa fa-filter"></i>Status Filter</h6></span>
                                  <select name="status[]" id="status-filter" class="form-control mb-3" multiple>
                                      <option value="All" @if(in_array('All', Request::get('status', []))) selected @endif>All</option>
                                      <option value="active" @if(in_array('active', Request::get('status', []))) selected @endif>Active</option>
                                      <option value="inactive" @if(in_array('inactive', Request::get('status', []))) selected @endif>Inactive</option>
                                      <option value="online" @if(in_array('online', Request::get('status', []))) selected @endif>Online Vehicle</option>
                                      <option value="offline" @if(in_array('offline', Request::get('status', []))) selected @endif>Offline Vehicle</option>
                                  </select>
                            </div>
               @if($role === 'sub-admin' && (!isset($accesss['download']) || $accesss['download'] != '1'))
                 
                @else
                <a href="{{ route('vehicles.export') }}" class="btn btn-sm btn-light me-2" id="btn-exportcsv">
                    <i class="fas fa-file-export"></i> CSV Format
                </a>
                @endif
               @if($role === 'sub-admin' && (!isset($accesss['import']) || $accesss['import'] != '1'))
                 
               @else
                    <button type="button" class="btn btn-sm btn-light me-2" id="btn-import" data-url="{{ route('vehicles.import') }}">
                        <i class="fas fa-file-import"></i> Import CSV
                    </button>
               @endif 

               @if($role === 'sub-admin' && (!isset($accesss['export']) || $accesss['export'] != '1'))
                 
                @else
                <input type="file" id="csv-file-input" accept=".csv" style="display: none;">
                    <input type="text" id="csv-file-input-filter" style="display: none;">
                    <a href="{{ route('vehiclescsv.export') }}" class="btn btn-sm btn-light me-2" id="btn-export">
                        <i class="fas fa-file-export"></i> Export CSV
                    </a>

                 @endif  
                 
                  @if($role === 'sub-admin' && (!isset($accesss['add']) || $accesss['add'] != '1'))
                 
                  @else
                    <button type="button" class="btn btn-sm btn-light" id="btn-add">
                        <i class="fas fa-plus"></i> Add Vehicle
                    </button>
                  @endif  
                    
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="vehicles-table">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Vehicle No</th>
                                <th>Vehicle Code</th>
                                <th>Device ID</th>
                                <th>City</th>
                                <th>Agency</th>
                                <th>Depot</th>
                                <th>Vehicle Type</th>
                                <th>Route Name</th>
                                <th>Gps</th>
                                <th>Speed</th>
                                <th>Last Seen Packet</th>
                                <th>Vehicle Status</th>
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
<div class="modal fade" id="vehicle-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white" style="background-color: #775DA6;">
                <h5 class="modal-title" id="modal-title">Add Vehicle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_form" class="form_submit" enctype="multipart/form-data" action="{{ route('vehicles.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="vehicle_id" name="vehicle_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_no">Vehicle No</label>
                            <input type="text"  name="vehicle_no" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_code">Vehicle Code</label>
                            <input type="text"  name="vehicle_code" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device_id">Device ID</label>
                            <input type="text"  name="device_id" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city">City</label>
                            <input type="text"  name="city" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="agency">Agency</label>
                            <input type="text"  name="agency" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="operator">Operator</label>
                            <input type="text"  name="operator" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="depot">Depot</label>
                            <input type="text" id="depot" name="depot" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_type">Vehicle Type</label>
                            <input type="text"  name="vehicle_type" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="seating_capacity">Seating Capacity</label>
                            <input type="text"  name="seating_capacity" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region">Region</label>
                            <input type="text"  name="region" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="etim_frequency">ETIM Frequency</label>
                            <input type="text"  name="etim_frequency" class="form-control">
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_category">Service Category</label>
                            <input type="text"  name="service_category" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fuel_type">Fuel Type</label>
                            <input type="text"  name="fuel_type" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dispatch_type">Dispatch Type</label>
                            <input type="text"  name="dispatch_type" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="route_name">Route Name</label>
                            <input type="text"  name="route_name" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_start_time">Service Start Time</label>
                            <input type="text"  name="service_start_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="service_end_time">Service End Time</label>
                            <input type="text"  name="service_end_time" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="gst_on_ticket" name="gst_on_ticket" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">GST On Ticket</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="surcharge_on_ticket" name="surcharge_on_ticket" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Surcharge On Ticket</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="collection_on_etim" name="collection_on_etim" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Collection On ETIM</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="gps_from_etim" name="gps_from_etim" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">GPS From ETIM</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="forward_to_shuttl" name="forward_to_shuttl" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Forward to Shuttl</span>
                                <span class="toggle-value off">No</span>
                            </div>
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

<div class="modal fade" id="edit_vehicle-modal"  tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white" style="background-color: #775DA6;">
                <h5 class="edit_modal-title" >Edit Vehicle</h5>
                <button type="button" class="btn-close btn-close-white" id="addmidal"  data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form_submit" id="edit_vehicle-form" enctype="multipart/form-data" action="{{ route('vehicles.update') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="vehicle_id" name="vehicle_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_no">Vehicle No</label>
                            <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_code">Vehicle Code</label>
                            <input type="text" id="vehicle_code" name="vehicle_code" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device_id">Device ID</label>
                            <input type="text" id="device_id" name="device_id" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="agency">Agency</label>
                            <input type="text" id="agency" name="agency" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="operator">Operator</label>
                            <input type="text" id="operator" name="operator" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="depot">Depot</label>
                            <input type="text" id="depot" name="depot" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_type">Vehicle Type</label>
                            <input type="text" id="vehicle_type" name="vehicle_type" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="seating_capacity">Seating Capacity</label>
                            <input type="text" id="seating_capacity" name="seating_capacity" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region">Region</label>
                            <input type="text" id="region" name="region" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="etim_frequency">ETIM Frequency</label>
                            <input type="text" id="etim_frequency" name="etim_frequency" class="form-control">
                        </div>
                      </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_category">Service Category</label>
                            <input type="text" id="service_category" name="service_category" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fuel_type">Fuel Type</label>
                            <input type="text" id="fuel_type" name="fuel_type" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dispatch_type">Dispatch Type</label>
                            <input type="text" id="dispatch_type" name="dispatch_type" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="route_name">Route Name</label>
                            <input type="text" id="route_name" name="route_name" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_start_time">Service Start Time</label>
                            <input type="text" id="service_start_time" name="service_start_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="service_end_time">Service End Time</label>
                            <input type="text" id="service_end_time" name="service_end_time" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="gst_on_ticket" name="gst_on_ticket" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">GST On Ticket</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="surcharge_on_ticket" name="surcharge_on_ticket" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Surcharge On Ticket</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="collection_on_etim" name="collection_on_etim" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Collection On ETIM</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="gps_from_etim" name="gps_from_etim" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">GPS From ETIM</span>
                                <span class="toggle-value off">No</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="toggle-container">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="forward_to_shuttl" name="forward_to_shuttl" value="No">
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Forward to Shuttl</span>
                                <span class="toggle-value off">No</span>
                            </div>
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
<div class="modal fade" id="show_vehicle-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white" style="background-color: #775DA6;">
                <div class="d-flex align-items-center">
                    <h5 class="edit_modal-title mb-0">Vehicle Tracking Information</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" id="addmidal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 0; border-radius: 8px; background-color: white;">
        <div class="container-fluid" style="padding: 20px;">
            <div class="row mb-4">
                <div class="col-12">
                    <div id="vehicle-location-map" style="height: 300px; width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 20px; background-color: #e9ecef; position: relative;">
                        <div style="position: absolute; top: 10px; right: 10px; background-color: rgba(255, 255, 255, 0.8); padding: 5px 10px; border-radius: 4px; font-weight: bold; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Live Tracking</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6" style="margin-bottom: 20px;">
                    <h4 style="font-size: 1.1rem; margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; display: inline-block;">Vehicle Status</h4>
                    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                        
                      <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                               <i class="fa-solid fa-car"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Vehicle NO:</span>
                            <span id="modal-vehicle_no" style="color: #2ecc71; font-weight: bold;"></span>
                        </div>
                      <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-key"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Ignition:</span>
                            <span id="modal-ignition" style="color: #2ecc71; font-weight: bold;"></span>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Driver ID:</span>
                            <span id="modal-driver_id" style="color: #555;"></span>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="far fa-clock"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Date/Time:</span>
                            <span id="modal-date_time" style="color: #555;"></span>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-satellite"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">GPS:</span>
                            <span id="modal-gps" style="color: #555;"></span>
                        </div>
                        
                        <div style="display: flex; align-items: center;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-route"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Route No:</span>
                            <span id="modal-route_no" style="color: #555;"></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h4 style="font-size: 1.1rem; margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; display: inline-block;">Location Data</h4>
                    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-location-arrow"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Latitude:</span>
                            <span style="color: #555;">
                                <span id="modal-lat"></span>° 
                                <span id="modal-lat_dir"></span>
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Longitude:</span>
                            <span style="color: #555;">
                                <span id="modal-lon"></span>
                                <span id="modal-lon_dir"></span>
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Speed:</span>
                            <span style="color: #555;">
                                <span id="modal-speed_kmh"></span>
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <i class="fas fa-road"></i>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Odometer:</span>
                            <span style="color: #555;">
                                <span id="modal-odo_meter"></span>
                            </span>
                        </div>
                      
                     
                    </div>
                </div>
                  <div class="col-md-12">
                    <h4 style="font-size: 1.1rem; margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; display: inline-block;">Live Address</h4>
                    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                        
                      
                      <div style="display: flex; align-items: center;">
                            <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background-color: #f0f8ff; color: #3498db; border-radius: 50%; margin-right: 10px; flex-shrink: 0;">
                                <svg class="folded-map-icon" viewBox="0 0 512 512" fill="none">
                                            <path d="M0 256L128 192L256 256L384 192L512 256V448L384 384L256 448L128 384L0 448V256Z" fill="#4ECDC4"/>
                                           <path d="M128 192L256 256V448L128 384V192Z" fill="#45B7D1"/>
                                           <path d="M256 256L384 192V384L256 448V256Z" fill="#96CEB4"/>
                                           <path d="M384 192L512 256V448L384 384V192Z" fill="#FFEAA7"/>
                                           <path d="M0 256L128 192V384L0 448V256Z" fill="#74B9FF"/>

                                           <path d="M256 64C220.7 64 192 92.7 192 128C192 179.2 256 288 256 288S320 179.2 320 128C320 92.7 291.3 64 256 64Z" fill="#FF6B6B"/>
                                           <circle cx="256" cy="128" r="24" fill="white"/>
                                  </svg>
                            </div>
                            <span style="font-weight: 600; color: #2c3e50; margin-right: 5px;">Live Address:</span>
                            <span style="color: #555;">
                                <span id="modal-live_address"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
<script src="{{ asset('public/backend/js/vehicle.js')}}"></script>

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
    table = $('#vehicles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("vehicles.index") }}',
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
            {data: 'vehicle_no'},
            {data: 'vehicle_code'},
            {data: 'device_id'},
            {data: 'city'},
            {data: 'agency'},
            {data: 'depot'},
            {data: 'vehicle_type'},
            {data: 'route_name'},
            {data: 'gps'},
            {data: 'updatespeed_kmh'},
            {data: 'time_date'},
            {data: 'vehicle_status'},
            {data: 'status'},
            {data: 'action'},
        ]
    });

    setInterval(function() {
      customValue += 1;
        table.ajax.reload(null, false); // Reload table every 30s without resetting page
    }, 60000);    
});
  
  $('#status-filter').select2({
        placeholder: "Select Status",
        allowClear: true
    });
  
  
  
    function fetchDeviceStatus() {
      $.ajax({
          url: '{{ route("vehicle.count") }}',
          method: 'GET',
          dataType: 'json',
          success: function(data) {
              $('#total_vehicles').html(data.data.total_vehicles);
              $('#online_count').html(data.data.online_count);
              $('#offline_count').html(data.data.offline_count); // corrected
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

</script>  
@endsection