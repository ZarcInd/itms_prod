<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicles;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Services\VehicleService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VehiclesController extends Controller
{
    protected $vehicleService;
    public $accesss;

    public $role;
    
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;

          $user = auth('admin')->user();
          $user_role = $user->role;
           $this->accesss = json_decode($user->user_access, true);
            $access_new = array();
            if ($this->accesss != null) {
                foreach ($this->accesss as $a) {
                    $access_new[$a['m_id']] = $a;
                }
            } else {
                $access_new = $this->accesss;
            }

            if($user_role === 'sub-admin'){
                View::share('accesss', $access_new[3]);
                $this->accesss = $access_new[3]; 

                View::share('role', $user_role);
                $this->role = $user_role; 
            }else{
                $user_role ='';
                $access_new ='';
                View::share('role', $user_role);
                 View::share('accesss', $access_new);
                $this->role = ''; 
                $this->accesss = ''; 
            }

    }
    
    /**
     * Display a listing of the vehicles
     */
    
    
    /**
     * Get vehicles data for DataTables
     */
    public function index(Request $request)
              {
                  if ($this->role === 'sub-admin' && (!isset($this->accesss['view']) || $this->accesss['view'] != '1')) {
                      return view('admin.noaccess');
                  }

                  
                  if ($request->ajax()) {
                      //dd(\DB::getQueryLog());
                      $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
                        $currentDate = Carbon::now('UTC')->format('Y-m-d');
                        // Base query with left join
                        $query = Vehicles::leftJoin('itms_data_update', function ($join) {
                            $join->on('vehicles.device_id', '=', 'itms_data_update.device_id')
                                 ->where('itms_data_update.packet_type', 'LP');
                        })
                        ->select(
                            'vehicles.*', 
                            'itms_data_update.id as itm_id',
                            'itms_data_update.date as updatedate', 
                            'itms_data_update.time as updatetime', 
                            'itms_data_update.lat as updatelat', 
                            'itms_data_update.lon as updatelon',
                            'itms_data_update.lat_dir as updatelat_dir', 
                            'itms_data_update.lon_dir as updatelon_dir', 
                            'itms_data_update.ignition as updateignition', 
                            'itms_data_update.driver_id as updatedriver_id',
                            'itms_data_update.gps as updategps', 
                            'itms_data_update.speed_knots as updatespeed_knots', 
                            'itms_data_update.speed_kmh as updatespeed_kmh',
                            'itms_data_update.odo_meter as updateodo_meter',
                            	'itms_data_update.route_no as updateroute_no',
                            DB::raw("
                                CASE 
                                        WHEN STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') != '$currentDate'

                                    THEN 1
                                    ELSE 0
                                END AS is_expired
                            ")
                        );

                        // Apply status filters with corrected logic
                        $statusFilters = $request->get('status', []);
                        if (!empty($statusFilters) && !in_array('All', $statusFilters)) {
                            $query->where(function($q) use ($statusFilters) {
                               // Check if exactly 2 filters are selected and combine them
                                  if (count($statusFilters) === 2) {
                                      if (in_array('active', $statusFilters) && in_array('online', $statusFilters)) {
                                          $statusFilters = ['active_online'];
                                      } elseif (in_array('active', $statusFilters) && in_array('offline', $statusFilters)) {
                                          $statusFilters = ['active_offline'];
                                      } elseif (in_array('inactive', $statusFilters) && in_array('online', $statusFilters)) {
                                          $statusFilters = ['inactive_online'];
                                      } elseif (in_array('inactive', $statusFilters) && in_array('offline', $statusFilters)) {
                                          $statusFilters = ['inactive_offline'];
                                      }
                                  }

                                foreach ($statusFilters as $status) {
                                    switch ($status) {
                                        case 'active':
                                            // Active vehicles (regardless of online/offline status)
                                            $q->orWhere('vehicles.status', '1');
                                            break;

                                        case 'inactive':
                                            // Inactive vehicles (regardless of online/offline status)
                                            $q->orWhere('vehicles.status', '0');
                                            break;

                                       case 'online':
                                      $q->orWhere(function($subQ) {
                                          $subQ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = CURDATE()");
                                      });
                                      break;

                                case 'offline':
                                    $q->orWhere(function($subQ) {
                                        $subQ->where(function($subSubQ) {
                                            $subSubQ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') != CURDATE()")
                                                    ->orWhereNull('itms_data_update.date');
                                        });
                                    });
                                    break;

                                case 'active_online':
                                    $q->orWhere(function($subQ) {
                                        $subQ->where('vehicles.status', '1')
                                             ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = CURDATE()");
                                    });
                                    break;

                                case 'active_offline':
                                    $q->orWhere(function($subQ) {
                                        $subQ->where('vehicles.status', '1')
                                             ->where(function($subSubQ) {
                                                 $subSubQ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') != CURDATE()")
                                                         ->orWhereNull('itms_data_update.date');
                                             });
                                    });
                                    break;

                                case 'inactive_online':
                                    $q->orWhere(function($subQ) {
                                        $subQ->where('vehicles.status', '0')
                                             ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = CURDATE()");
                                    });
                                    break;

                                case 'inactive_offline':
                                    $q->orWhere(function($subQ) {
                                        $subQ->where('vehicles.status', '0')
                                             ->where(function($subSubQ) {
                                                 $subSubQ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') != CURDATE()")
                                                         ->orWhereNull('itms_data_update.date');
                                             });
                                    });
                                    break;

                                    }
                                }
                            });
                        }

                        $vehicle = $query->get();
                        //\DB::enableQueryLog();
                      return DataTables::of($vehicle)
                          ->addIndexColumn()
                          ->addColumn('gps', function ($vehicle) {
                              $gpsStatus = ($vehicle->updategps == 'A') ? 'Lock' : 'Unlock';
                              return $gpsStatus;
                          })
                        
                           ->addColumn('time_date', function ($vehicle) {
                              if ((!empty($vehicle->updatedate)) && (!empty($vehicle->updatetime))) {
                                  $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $vehicle->updatedate . ' ' . $vehicle->updatetime);

                                  // Format as dd/mm/yyyy hh:mm A (e.g., 04/05/2025 02:30 PM)
                                  $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                  return $datetime->format('d/m/Y h:i A');
                              } else {
                                  return '';
                              }
                          })
                          ->addColumn('vehicle_status', function ($vehicle) {
                            if ((!empty($vehicle->updatedate)) || (!empty($vehicle->updatetime))) {
                                   if ($vehicle->is_expired) {
                                    $vehicles_color = 'red';
                                    $status_name = 'Offline';
                                    } else {
                                        $vehicles_color = 'green';
                                        $status_name = 'Online';
                                    }
                                 }else{
                                    $vehicles_color = 'red';
                                    $status_name = 'Offline';
                                 }
                            
                                
                                  $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
                                  return  '<div><b><p style="color:' . $vehicles_color . '">'.$status_name.'</p></b></div>';

                            })
                        
                          
                          ->addColumn('status', function ($vehicle) {
                              if ($this->role === 'sub-admin' && (!isset($this->accesss['status']) || $this->accesss['status'] != '1')) {
                                  return '';
                              }

                              $statusText = $vehicle->status ? 'Active' : 'Inactive';
                              $btnClass = $vehicle->status ? 'btn-success' : 'btn-secondary';
                              $icon = $vehicle->status ? 'toggle-on' : 'toggle-off';

                              return '
                                  <button type="button"
                                      data-id="' . $vehicle->id . '"
                                      data-status="' . $vehicle->status . '"
                                      data-url="' . url('/admin/toggle-vehicle-status/' . $vehicle->id) . '"
                                      class="btn btn-sm ' . $btnClass . ' btn-toggle-status">
                                      ' . $statusText . ' 
                                      <i class="fa fa-' . $icon . '"></i>
                                  </button>
                              ';
                          })
                        
                          
                          ->addColumn('action', function ($vehicle) {
                              $buttons = '';

                              // View Info
                              if (!($this->role === 'sub-admin' && (!isset($this->accesss['data_view']) || $this->accesss['data_view'] != '1'))) {
                                  
                                 // Edit
                              if (!($this->role === 'sub-admin' && (!isset($this->accesss['edit']) || $this->accesss['edit'] != '1'))) {
                                  $buttons .= '
                                      <a  class="btn btn-sm btn-light"  style="background-color: #775DA6; color: white;" id="btn-edit' . $vehicle->id . '" onclick="vicaledit(' . $vehicle->id . ',this)"
                                          data-id="' . $vehicle->id . '"
                                          data-vehicle_no="' . $vehicle->vehicle_no . '"
                                          data-vehicle_code="' . $vehicle->vehicle_code . '"
                                          data-device_id="' . $vehicle->device_id . '"
                                          data-city="' . $vehicle->city . '"
                                          data-agency="' . $vehicle->agency . '"
                                          data-operator="' . $vehicle->operator . '"
                                          data-depot="' . $vehicle->depot . '"
                                          data-vehicle_type="' . $vehicle->vehicle_type . '"
                                          data-seating_capacity="' . $vehicle->seating_capacity . '"
                                          data-region="' . $vehicle->region . '"
                                          data-etim_frequency="' . $vehicle->etim_frequency . '"
                                          data-service_category="' . $vehicle->service_category . '"
                                          data-fuel_type="' . $vehicle->fuel_type . '"
                                          data-dispatch_type="' . $vehicle->dispatch_type . '"
                                          data-route_name="' . $vehicle->route_name . '"
                                          data-service_start_time="' . $vehicle->service_start_time . '"
                                          data-service_end_time="' . $vehicle->service_end_time . '"
                                          data-gst_on_ticket="' . $vehicle->gst_on_ticket . '"
                                          data-surcharge_on_ticket="' . $vehicle->surcharge_on_ticket . '"
                                          data-collection_on_etim="' . $vehicle->collection_on_etim . '"
                                          data-gps_from_etim="' . $vehicle->gps_from_etim . '"
                                          data-forward_to_shuttl="' . $vehicle->forward_to_shuttl . '">
                                          <i class="fa fa-edit"></i>
                                      </a>';
                              }

                              // Delete
                              if (!($this->role === 'sub-admin' && (!isset($this->accesss['delete']) || $this->accesss['delete'] != '1'))) {
                                  $buttons .= '
                                      <a data-id="' . $vehicle->id . '" data-url="' . url('/admin/delete-vehicle/' . $vehicle->id) . '"  class="btn btn-sm btn-danger btn-delete">
                                       <i class="fa fa-trash"></i>
                                      </a>';
                              }
                                
                                // show on map
                                try {
                                      $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $vehicle->updatedate . ' ' . $vehicle->updatetime);
                                      $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                      $fdate = $datetime->format('Y-m-d H:i:s');
                                  } catch (\Exception $e) {
                                      $fdate = '';
                                  }

                                  $latLong = $this->change_latlong($vehicle->updatelat, $vehicle->updatelon, $vehicle->updatelat_dir, $vehicle->updatelon_dir);
                                  $gpsStatus = ($vehicle->updategps == 'A') ? 'Lock' : 'Unlock';
                                  $locationImage = url('public/location.png');
                                  $detailsUrl = url('/admin/vehicle/' . $vehicle->id);
                                  
                                  $getaddresss ='Loading ....';
                                   $buttons .= '
                                      <a id="btn-info' . $vehicle->id . '" style="color: #775DA6;" onclick="vicalinf(' . $vehicle->id . ',this)"
                                          data-id="' . $vehicle->id . '"
                                          data-vehicle_no="' . $vehicle->vehicle_no . '"
                                          data-ignition="' . $vehicle->updateignition . '"
                                          data-driver_id="' . $vehicle->updatedriver_id . '"
                                          data-date_time="' . $fdate . '"
                                          data-gps="' . $gpsStatus . '"
                                          data-refars_url="' . $detailsUrl . '"
                                          data-lat="' . $latLong['latitude'] . '"
                                          data-lat_dir="' . $vehicle->updatelat_dir . '"
                                          data-lon="' . $latLong['longitude'] . '"
                                          data-lon_dir="' . $vehicle->updatelon_dir . '"
                                          data-route_no="'.$vehicle->updateroute_no.'"
                                          data-speed_kmh="'.$vehicle->updatespeed_kmh.'"
                                          data-odo_meter="'.$vehicle->updateodo_meter.'"
                                          data-live_address="'.$getaddresss.'"
                                          data-loca_img="' . $locationImage . '">
                                         <svg class="folded-map-icon" viewBox="0 0 512 512" fill="none">
                                            <path d="M0 256L128 192L256 256L384 192L512 256V448L384 384L256 448L128 384L0 448V256Z" fill="#4ECDC4"/>
                                           <path d="M128 192L256 256V448L128 384V192Z" fill="#45B7D1"/>
                                           <path d="M256 256L384 192V384L256 448V256Z" fill="#96CEB4"/>
                                           <path d="M384 192L512 256V448L384 384V192Z" fill="#FFEAA7"/>
                                           <path d="M0 256L128 192V384L0 448V256Z" fill="#74B9FF"/>

                                           <path d="M256 64C220.7 64 192 92.7 192 128C192 179.2 256 288 256 288S320 179.2 320 128C320 92.7 291.3 64 256 64Z" fill="#FF6B6B"/>
                                           <circle cx="256" cy="128" r="24" fill="white"/>
                                       </svg>

                                      </a>';
                              }

                             

                              return $buttons;
                          })
                          ->rawColumns(['address','time_date','gps','vehicle_status','status', 'action'])
                          ->make(true);
                  }

                  return view('admin.vehicles');
              }


           public function vicaldata($id)
                  {
                       
                      $vehicle = Vehicles::findOrFail($id);
                      //$live_data1 = DB::table('itms_data_update')
                        //->select('id', 'date', 'time', 'lat', 'lon', 'lat_dir', 'lon_dir', 'ignition', 'driver_id', 'gps', 'speed_knots', 'speed_kmh', 'odo_meter', 'route_no')
                         // ->where('packet_type', 'LP')
                          //->where('device_id', $vehicle->device_id)
                          //->orderBy('id', 'DESC');
                        $live_data1 = DB::select("
                              SELECT `id`, `date`, `time`, `lat`, `lon`, `lat_dir`, `lon_dir`, `ignition`, `driver_id`, `gps`, `speed_knots`, `speed_kmh`, `odo_meter`, `route_no`
                              FROM `itms_data_update`
                              WHERE `packet_type` = 'LP'
                                AND `device_id` = ?
                              ORDER BY `id` DESC
                              LIMIT 1
                          ", [$vehicle->device_id]);

                      if (count($live_data1) > 0) {
                         // $ldata = $live_data1->first();
                             $ldata = $live_data1[0];
                          // Combine date and time to datetime
                          $ldate = $ldata->date;
                          $ltime = $ldata->time;
                          $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $ldate . ' ' . $ltime);
                           $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                           $fdate = $datetime->format('Y-m-d H:i:s');  // Formatted datetime

                          // Convert lat/lon
                          $late_long_convert = $this->change_latlong($ldata->lat, $ldata->lon, $ldata->lat_dir, $ldata->lon_dir);
                          
                          // Location icon image URL
                          $locatore = url('public/location.png');
                           if($ldata->gps == 'A'){
                           $gpa = 'Lock';
                          }else{
                            $gpa = 'Unlock'; 
                          }
                          
                          $adressdata = $this->getAddress($late_long_convert['latitude'],$late_long_convert['longitude']);
                          if($adressdata['success'] == true){
                                       $adressdata['address_components'];
                                       $addressParts = [
                                                $adressdata['address_components']['house_number'] ?? null,
                                                $adressdata['address_components']['road'] ?? null,
                                                $adressdata['address_components']['neighbourhood'] ?? null,
                                                $adressdata['address_components']['suburb'] ?? null,
                                                $adressdata['address_components']['city'] ?? null,
                                                $adressdata['address_components']['municipality'] ?? null,
                                                $adressdata['address_components']['state_district'] ?? null,
                                                $adressdata['address_components']['state'] ?? null,
                                                $adressdata['address_components']['postcode'] ?? null,
                                                $adressdata['address_components']['country'] ?? null,
                                            ];

                                            $fullAddress = implode(', ', array_filter($addressParts));
                                         $getaddresss = $fullAddress;
                                    }else{
                                         $getaddresss ='Address not found. Please check again after some time.';
                                    }
                          // Prepare the data to send back
                          $result = [
                              'vehicle_id' => $vehicle->id,
                              'device_id' => $vehicle->device_id,
                              'vehicle_no' => $vehicle->vehicle_no,
                              'ignition' => $ldata->ignition,
                              'driver_id' => $ldata->driver_id,
                              'date_time' => $fdate,
                              'gps' => $gpa,
                              'lat' => $late_long_convert['latitude'],
                              'lat_dir' => $ldata->lat_dir,
                              'lon' => $late_long_convert['longitude'],
                              'lon_dir' => $ldata->lon_dir,
                              'route_no' => $ldata->route_no,
                              'speed_kmh' => $ldata->speed_kmh,
                              'odo_meter' => $ldata->odo_meter,
                              'live_address' => $getaddresss,
                              'loca_img' => $locatore,
                          ];
                           return response()->json($result);
                      }
                            
                      // No live data found  return empty or error
                      return response()->json([
                          'message' => 'No live data found for this vehicle',
                      ], 404);
                  }


  
               // Convert DDDMM.MMMMM to Decimal Degrees
              private function dmsToDecimal($dms, $dir)
              {
                  $degrees = floor($dms / 100);
                  $minutes = $dms - ($degrees * 100);
                  $decimal = $degrees + ($minutes / 60);

                  // Make negative if direction is South or West
                  return ($dir === 'S' || $dir === 'W') ? -$decimal : $decimal;
              }

              // Public method to convert lat/lon
              public function change_latlong($lat, $lon, $lat_dir, $lon_dir)
              {
                  $latitude = $this->dmsToDecimal($lat, $lat_dir);
                  $longitude = $this->dmsToDecimal($lon, $lon_dir);

                  // You can return or use the values here
                  return [
                      'latitude' => $latitude,
                      'longitude' => $longitude,
                  ];
              }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {  
         if($this->role === 'sub-admin'  && isset($this->accesss['add']) != '1'){
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed and try again..'
                    ]);
        }
                       

          if($this->role === 'sub-admin'  && isset($this->accesss['add_a']) != '1'){
                                return view('admin.noacesss');
             }
                       

        $validator = Validator::make($request->all(), [
            'vehicle_no' => 'required|string|max:255|unique:vehicles,vehicle_no',
            'device_id' => 'required|string|max:255|unique:vehicles,device_id',
            // Add validation for other fields as needed
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        
        // Convert checkbox values to boolean
        $data = array(
            "vehicle_no" => $request->vehicle_no,
            "vehicle_code" => $request->vehicle_code,
            "device_id" => $request->device_id,
            "city" => $request->city,
            "agency" => $request->agency,
            "operator" => $request->operator,
            "depot" => $request->depot,
            "vehicle_type" => $request->vehicle_type,
            "seating_capacity" => $request->seating_capacity,
            "region" => $request->region,
            "etim_frequency" => $request->etim_frequency,
            "service_category" => $request->service_category,
            "fuel_type" => $request->fuel_type,
            "dispatch_type" =>$request->dispatch_type,
            "route_name" =>$request->route_name,
            "service_start_time" => $request->service_start_time,
            "service_end_time" =>$request->service_end_time,
            "gst_on_ticket" =>$request->gst_on_ticket,
            "surcharge_on_ticket" =>$request->surcharge_on_ticket,
            "collection_on_etim" => $request->collection_on_etim,
            "gps_from_etim" => $request->gps_from_etim,
            "forward_to_shuttl" => $request->forward_to_shuttl,
        );
        
        if(Vehicles::create($data)){
            return response()->json([
                'status' => true,
                'message' => 'Vehicle created successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }
        
    }
    
    
    
    /**
     * Update the specified vehicle
     */
    public function update(Request $request)
    {  
         if($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1'){
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed and try again..'
                    ]);
        }

        $id = $request->vehicle_id;
        $vehicle = Vehicles::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'vehicle_no' => 'required|string|max:255|unique:vehicles,vehicle_no,'.$id,
            // Add validation for other fields as needed
           'device_id' => 'required|string|max:255|unique:vehicles,device_id,'.$id,
        ]);
        
        if ($validator->fails()) {
             return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        
        // Convert checkbox values to boolean
        $data = array(
            "vehicle_no" => $request->vehicle_no,
            "vehicle_code" => $request->vehicle_code,
            "device_id" => $request->device_id,
            "city" => $request->city,
            "agency" => $request->agency,
            "operator" => $request->operator,
            "depot" => $request->depot,
            "vehicle_type" => $request->vehicle_type,
            "seating_capacity" => $request->seating_capacity,
            "region" => $request->region,
            "etim_frequency" => $request->etim_frequency,
            "service_category" => $request->service_category,
            "fuel_type" => $request->fuel_type,
            "dispatch_type" =>$request->dispatch_type,
            "route_name" =>$request->route_name,
            "service_start_time" => $request->service_start_time,
            "service_end_time" =>$request->service_end_time,
            "gst_on_ticket" =>$request->gst_on_ticket,
            "surcharge_on_ticket" =>$request->surcharge_on_ticket,
            "collection_on_etim" => $request->collection_on_etim,
            "gps_from_etim" => $request->gps_from_etim,
            "forward_to_shuttl" => $request->forward_to_shuttl,
        );
        
        if($vehicle->update($data)){
            return response()->json([
                'status' => true,
                'message' => 'Vehicle updated successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }
        
    }
    


    
    /**
     * Remove the specified vehicle
     */
    public function destroy($id)
        {   
              if($this->role === 'sub-admin'  && isset($this->accesss['delete']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Delete Access']);
              }
              
            $vehicle = Vehicles::find($id);

            if (!$vehicle) {
                return response()->json(['status' => false, 'message' => 'Vehicle not found.']);
            }

            $vehicle->delete();

            return response()->json(['status' => true, 'message' => 'Vehicle deleted successfully.']);
        }

    
        public function toggleStatus(Request $request, $id)
        {
              if($this->role === 'sub-admin'  && isset($this->accesss['status']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Status Access']);
              }
            $response=[];
                if($request->id){
                    $get_user = Vehicles::where('id',$id)->first();

                    if ($get_user->status == '1') {
                        $val = '0';
                    }else{
                        $val='1';
                    }

                    if(Vehicles::where('id', $id)->update(['status' => $val])){
                        $response=['status'=>true,'message'=>'Updated Successfully'];
                    }else{
                        $response=['status'=>false,'message'=>'Something went wrong'];
                    }
                }else{
                    $response=['status'=>false,'message'=>'Something went wrong'];
                }
                return response()->json($response);
           
        }

        


    /**
     * Import vehicles from CSV
     */
    public function import(Request $request)
    {   
          if($this->role === 'sub-admin'  && isset($this->accesss['import']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Import Access']);
              }

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $result = $this->vehicleService->processCsvImport($request->file('csv_file'));
        return response()->json($result);
    }
    
    /**
     * Export vehicles to CSV
     */
    public function export(Request $request)
    {
        if($this->role === 'sub-admin'  && isset($this->accesss['export']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Import Access']);
        }
        $status = $request->get('status');
        $filename = $this->vehicleService->generateCsvExport($status);
        $filePath = 'public/' . $filename;
        return response()->download(storage_path('app/' . $filePath), 'vehicles.xlsx');

    }


    public function vichail_dowllound(){
        if($this->role === 'sub-admin'  && isset($this->accesss['download']) != '1'){
                     abort(404, 'File not found.');
        }
        $filePath = public_path('exports/vehicles.csv');
        
            if (file_exists($filePath)) {
                return response()->download($filePath, 'vehicles.csv', [
                    'Content-Type' => 'text/csv',
                ]);
            } else {
                abort(404, 'File not found.');
            }
    }
  
     public function vehicle_get_data(){
    
      $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
        $currentDate = Carbon::now('UTC')->format('Y-m-d');
              // Simple query to get vehicle counts by status without any filters
              $vehicleCounts = Vehicles::leftJoin('itms_data_update', function ($join) {
                  $join->on('vehicles.device_id', '=', 'itms_data_update.device_id')
                       ->where('itms_data_update.packet_type', 'LP');
              })
              ->selectRaw("
                  COUNT(*) as total_vehicles,
                  SUM(CASE WHEN vehicles.status = '1' THEN 1 ELSE 0 END) as active_count,
                  SUM(CASE WHEN vehicles.status = '0' THEN 1 ELSE 0 END) as inactive_count,
                  SUM(CASE 
                        WHEN STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = '$currentDate'
                      THEN 1 
                      ELSE 0 
                  END) as online_count,
                  SUM(CASE 
                        WHEN STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') != '$currentDate'
                      OR itms_data_update.date IS NULL
                      THEN 1 
                      ELSE 0 
                  END) as offline_count
              ")
              ->first();

              // Return data as JSON response
              $response = [
                  'status' => 'success',
                  'data' => [
                      'total_vehicles' => (int) $vehicleCounts->total_vehicles,
                      'active_count' => (int) $vehicleCounts->active_count,
                      'inactive_count' => (int) $vehicleCounts->inactive_count,
                      'online_count' => (int) $vehicleCounts->online_count,
                      'offline_count' => (int) $vehicleCounts->offline_count
                  ],
                  'timestamp' => $currentTime
              ];

              // For Laravel Controller - return JSON response
              return response()->json($response);

    }
      
     
           public function getAddress($lat,$long)
                {
                             $latitude = $lat;
                             $longitude = $long;

                    // Validate required parameters
                    if (empty($latitude) || empty($longitude)) {
                        return [
                            'success' => false,
                            'message' => 'Latitude and longitude are required'
                        ];
                    }

                    // Validate coordinate ranges
                    if ($latitude < -90 || $latitude > 90) {
                        return [
                            'success' => false,
                            'message' => 'Latitude must be between -90 and 90'
                        ];
                    }

                    if ($longitude < -180 || $longitude > 180) {
                        return [
                            'success' => false,
                            'message' => 'Longitude must be between -180 and 180'
                        ];
                    }

                    // Try multiple geocoding services
                    $services = [
                        [
                            'name' => 'nominatim',
                            'url' => 'https://nominatim.openstreetmap.org/reverse',
                            'params' => [
                                'format' => 'json',
                                'lat' => $latitude,
                                'lon' => $longitude,
                                'addressdetails' => 1,
                                'zoom' => 18,
                                'accept-language' => 'en'
                            ]
                        ],
                        [
                            'name' => 'photon',
                            'url' => 'https://photon.komoot.io/reverse',
                            'params' => [
                                'lat' => $latitude,
                                'lon' => $longitude,
                                'lang' => 'en'
                            ]
                        ]
                    ];

                    foreach ($services as $service) {
                        try {
                            // Make request with improved settings
                            $response = Http::timeout(30) // Increased timeout
                                ->connectTimeout(15) // Connection timeout
                                ->retry(3, 2000) // Retry 3 times with 2 second delay
                                ->withHeaders([
                                    'User-Agent' => 'Laravel App - Reverse Geocoding v2.0',
                                    'Accept' => 'application/json',
                                    'Accept-Language' => 'en'
                                ])
                                ->withOptions([
                                    'verify' => false, // Disable SSL verification if needed
                                    'http_errors' => false
                                ])
                                ->get($service['url'], $service['params']);

                            if ($response->successful()) {
                                $data = $response->json();

                                // Handle Nominatim response
                                if ($service['name'] === 'nominatim' && isset($data['display_name']) && !empty($data['display_name'])) {
                                    $address = $data['address'] ?? [];

                                    return [
                                        'success' => true,
                                        'service_used' => 'nominatim',
                                        'full_address' => $data['display_name'],
                                        'latitude' => (float) $latitude,
                                        'longitude' => (float) $longitude,
                                        'address_components' => ['house_number' => $address['house_number'] ?? null, 'road' => $address['road'] ?? null, 'neighbourhood' => $address['neighbourhood'] ?? null, 'suburb' => $address['suburb'] ?? null, 'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null, 'municipality' => $address['municipality'] ?? null, 'state_district' => $address['state_district'] ?? null, 'state' => $address['state'] ?? null, 'postcode' => $address['postcode'] ?? null, 'country' => $address['country'] ?? null, 'country_code' => strtoupper($address['country_code'] ?? '')],
                                        'place_type' => $data['type'] ?? null,
                                        'place_id' => $data['place_id'] ?? null,
                                        'osm_type' => $data['osm_type'] ?? null,
                                        'osm_id' => $data['osm_id'] ?? null
                                    ];

                                }

                                // Handle Photon response
                                if ($service['name'] === 'photon' && isset($data['features']) && count($data['features']) > 0) {
                                    $feature = $data['features'][0];
                                    $properties = $feature['properties'] ?? [];

                                    return [
                                        'success' => true,
                                        'service_used' => 'photon',
                                        'full_address' => $properties['name'] ?? 'Unknown location',
                                        'latitude' => (float) $latitude,
                                        'longitude' => (float) $longitude,
                                        'address_components' => ['house_number' => $properties['housenumber'] ?? null, 'road' => $properties['street'] ?? null, 'neighbourhood' => null, 'suburb' => $properties['district'] ?? null, 'city' => $properties['city'] ?? null, 'municipality' => null, 'state_district' => null, 'state' => $properties['state'] ?? null, 'postcode' => $properties['postcode'] ?? null, 'country' => $properties['country'] ?? null, 'country_code' => strtoupper($properties['countrycode'] ?? '')],
                                        'place_type' => $properties['type'] ?? null
                                    ];

                                }
                            }

                        } catch (\Exception $e) {
                            // Continue to next service if this one fails
                            continue;
                        }
                    }

                    // If all services fail
                    return [
                        'success' => false,
                        'message' => 'All geocoding services are currently unavailable. Please try again later.',
                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude
                    ];
                }

            
  
              



}
