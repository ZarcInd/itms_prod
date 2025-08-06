<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use App\Jobs\ProcessRawDataExport;
use DateTimeZone;



class DeviceController extends Controller
{
    protected $deviceService;

    public $accesss;

    public $role;
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;

        $user = auth('admin')->user();
        $user_role = $user->role;
        $user_id = $user->id;
        $this->accesss = json_decode($user->user_access, true);
        $access_new = array();
        if ($this->accesss != null) {
            foreach ($this->accesss as $a) {
                $access_new[$a['m_id']] = $a;
            }
        } else {
            $access_new = $this->accesss;
        }


        if ($user_role === 'sub-admin') {
            View::share('accesss', $access_new[4]);
            $this->accesss = $access_new[4];

            View::share('role', $user_role);
            $this->role = $user_role;
        } else {
            $user_role = '';
            $access_new = '';
            View::share('role', $user_role);
            View::share('accesss', $access_new);
            $this->role = '';
            $this->accesss = '';
        } 
           $this->user_id = $user_id;
    }

    /**
     * Display a listing of the device
     */


    /**
     * Get device data for DataTables
     */
    public function index(Request $request)
    {
        if ($this->role === 'sub-admin'  && isset($this->accesss['view']) != '1') {
            return view('admin.noacesss');
        }
      
        $customValue = $request->get('customValue');
        $isStatusFilterResponse = $request->get('isStatusFilterResponse');
     
        if ($request->ajax()) {
             
            //\DB::enableQueryLog();
            $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
            $currentDate = Carbon::now('UTC')->format('Y-m-d');
            $query = DB::table('itms_data_update')
            ->leftJoin('devices', function ($join) {
                $join->on('devices.device_id', '=', 'itms_data_update.device_id')
                    ->where('itms_data_update.packet_type', 'LP');
            })
            ->select('devices.*', 'itms_data_update.date as updatedate', 'itms_data_update.time as updatetime', 
                'itms_data_update.firmware_version as updatefirmware_version', 'itms_data_update.network as updatenetwork', 
                'itms_data_update.gps as updategps','itms_data_update.device_id as newdeviceid', 'itms_data_update.lon as updatelon', 'itms_data_update.lat as updatelat', 
                DB::raw("
                    CASE 
                        WHEN STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 60 MINUTE < '$currentTime'
                        THEN 1
                        ELSE 0
                    END AS is_expired
                "),
                DB::raw("
                    CASE
                        WHEN STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') < '$currentDate'
                        THEN 2
                        ELSE 0
                    END AS is_previous_date
                ")
            );
          
          
        
        // Apply status filter
        // Apply status filter for multiple selections
          if ($request->has('status') && is_array($request->status) && !in_array('All', $request->status)) {
              $query->where(function($mainQuery) use ($request, $currentTime, $currentDate) {
                  foreach ($request->status as $status) {
                      $mainQuery->orWhere(function($q) use ($status, $currentTime, $currentDate) {
                          if ($status == 'communation_lost') {
                              // Filter for Communication Lost status
                              $q->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') < '$currentDate'");
                          } 
                          elseif ($status == 'daly_community') {
                              // Filter for Delay Community status (offline in current date OR null data)
                              $q->where(function($subq) use ($currentTime, $currentDate) {
                                  $subq->whereNull('itms_data_update.date')
                                    ->orWhereNull('itms_data_update.time')
                                    ->orWhere(function($innerq) use ($currentTime, $currentDate) {
                                        $innerq->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = '$currentDate'")
                                             ->whereRaw("STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 60 MINUTE < '$currentTime'");
                                    });
                              });
                          }
                          elseif ($status == 'community') {
                              // Filter for Community status (everything is working fine)
                              $q->whereNotNull('itms_data_update.date')
                                  ->whereNotNull('itms_data_update.time')
                                  ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = '$currentDate'")
                                  ->whereRaw("STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 60 MINUTE >= '$currentTime'");
                          }
                      });
                  }
              });
          }

         
        $device = $query->get();
         //  dd(\DB::getQueryLog());
          
            //dd(\DB::getQueryLog());
            return DataTables::of($device)
                ->addIndexColumn()
                ->addColumn('time_date', function ($device) {
                    if ((!empty($device->updatedate)) && (!empty($device->updatetime))) {
                        $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $device->updatedate . ' ' . $device->updatetime);

                        // Format as dd/mm/yyyy hh:mm A (e.g., 04/05/2025 02:30 PM)
                        $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                        return $datetime->format('d/m/Y h:i A');
                    } else {
                        return '';
                    }
                })

                ->addColumn('firmversion', function ($device) {

                    if (!empty($device->updatefirmware_version)) {
                        return $device->updatefirmware_version;
                    } else {
                        return '';
                    }
                })
               
                 ->addColumn('customValue', function ($device) use ($customValue) {
                      return $customValue; // or return boolean
                  })
              
                ->addColumn('network', function ($device) {

                    if (!empty($device->updatenetwork)) {

                        return  $device->updatenetwork;
                    }

                    return '';
                })
                ->addColumn('status', function ($device) {
                    // Fetch latest LP packet
                    
                   if ((empty($device->updatedate)) || (empty($device->updatetime))) {
                      $device_color = 'orange';
                      $status_name = 'Delay Communicating';
                     $status_code = 'daly_community';
                  } elseif ($device->is_previous_date == 2) {
                      $device_color = 'red'; // You can choose any color to represent this
                      $status_name = 'Communication Lost';
                  } elseif ($device->is_expired) {
                      $device_color = 'orange';
                      $status_name = 'Delay Communicating ';
                     $status_code = 'daly_community';
                  } else {
                      $device_color = 'green';
                      $status_name = 'Communicating';
                     $status_code = 'community';
                  }

                    $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
                    return  '            <div><b><p style="color:' . $device_color . '">'.$status_name.'</p></b></div>';
                })

                ->addColumn('action', function ($device) {

                    if ($this->role === 'sub-admin'  && isset($this->accesss['data_view']) != '1') {
                        $newitem_btn = '';
                    } else {
                        if ((!empty($device->updatedate)) && (!empty($device->updatetime))) {
                            $ldate = $device->updatedate;
                            $ltime = $device->updatetime;
                            $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $ldate . ' ' . $ltime);
                            $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                            $fdate = $datetime->format('Y-m-d H:i:s');  // Formatted datetime

                            //online ofline data
                            $packetTimestamp  = Carbon::createFromFormat('d/m/Y H:i:s', $ldate . ' ' . $ltime)->addSeconds(120)->format('d/m/Y H:i:s');
                            $packtim = Carbon::createFromFormat('d/m/Y H:i:s', $packetTimestamp, 'UTC')->timestamp;

                            // current time
                            $nowFormatted = Carbon::now('UTC')->format('d/m/Y H:i:s');
                            $timestamp = Carbon::createFromFormat('d/m/Y H:i:s', $nowFormatted, 'UTC')->timestamp;

                            $device_status = '';
                            if ($packtim <= $timestamp) {
                                $device_status = 'Offline';
                            } else {
                                $device_status = 'Online';
                            }
                        } else {
                            $fdate = '';
                            $device_status = 'Offline';
                        }

                        //end
                        if (!empty($device->updategps)) {
                            if ($device->updategps == 'A') {
                                $gpa = 'Lock';
                            } else {
                                $gpa = 'Unlock';
                            }
                        } else {
                            $gpa = '';
                        }
                        $refarsurl = url('/admin/device_status/' . $device->id);

                        $newitem_btn = '<button 
                            class="btn btn-sm btn-light"
                            id="btn-info' . $device->id . '"
                            style="background-color: #775DA6; color: white;"
                             data-device_id="' . $device->newdeviceid . '"
                             data-refars_url="' . $refarsurl . '"
                            data-vehicle_no="' . $device->vehicle_no . '"
                            data-protocol="' . $device->protocol . '"
                            data-lat="' . $device->updatelat . '"
                            data-lon="' . $device->updatelon . '"
                            data-time_in_packt="' . $fdate . '"
                            data-natwork="' . $device->updatenetwork . '"
                            data-packet_status="' . $device_status . '"
                            data-gps_signal="' . $gpa . '"
                            onclick="deviceinf(' . $device->newdeviceid . ',this)">
                            <i class="bi bi-eye"></i>
                         </button>';
                    }


                    return '
                    ' . (($this->role === 'sub-admin' && (!isset($this->accesss['edit']) || $this->accesss['edit'] != '1')) ? '' : '
                    <button type="button"
                            class="btn btn-sm btn-light"
                            id="btn-edit' . $device->newdeviceid . '"
                            style="background-color: #775DA6; color: white;"
                            data-id="' . $device->id . '"
                            data-device_id="' . $device->newdeviceid . '"
                            data-vehicle_no="' . $device->vehicle_no . '"
                            data-agency="' . $device->agency . '"
                            data-depot="' . $device->depot . '"
                            data-protocol="' . $device->protocol . '"
                            data-region_id="' . $device->region_id . '"
                            onclick="devicedit(' . $device->newdeviceid . ',this)">
                            <i class="fa fa-edit"></i>
                    </button>
                    ') . '
                      
                        ' . $newitem_btn . '
                    ';
                })
                ->with('input', ['customValue' => $customValue]) // << extra meta
                ->with('select', ['isStatusFilterResponse' => $isStatusFilterResponse]) // << extra meta
                ->rawColumns(['customValue','time_date', 'firmversion', 'network', 'status', 'action'])
                ->make(true);
        }
        return view('admin.device');
    }


    public function devicedata($id)
    {
        $device = Device::findOrFail($id);

        $live_data = DB::select("
                              SELECT `id`, `date`, `time`, `lat`, `lon`, `lat_dir`, `lon_dir`, `network`, `driver_id`, `gps`
                              FROM `itms_data_update`
                              WHERE `packet_type` = 'LP'
                                AND `device_id` = ?
                              ORDER BY `id` DESC
                              LIMIT 1
                          ", [$device->device_id]);

        if (count($live_data) > 0) {
            $ldata = $live_data[0];

            //$ldata = $live_data->first();
            $ldate = $ldata->date;
            $ltime = $ldata->time;
            $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $ldate . ' ' . $ltime);

            // Convert to IST (Asia/Kolkata)
            $datetime->setTimezone('Asia/Kolkata');
            $fdate = $datetime->format('Y-m-d H:i:s');

            $notwork = $ldata->network;

            // Determine online/offline status
            $packetTime = Carbon::createFromFormat('d/m/Y', $ldata->date)->startOfDay();
            $currentTime = Carbon::now('Asia/Kolkata')->startOfDay();

            $device_status = $packetTime->equalTo($currentTime) ? 'Online' : 'Offline';

            if ($ldata->gps == 'A') {
                $gpa = 'Lock';
            } else {
                $gpa = 'Unlock';
            }
            // Return **all data** as JSON
            return response()->json([
                'device_id' => $device->device_id,
                'vehicle_no' => $device->vehicle_no,
                'protocol' => $device->protocol,
                'lat' => $ldata->lat,
                'lon' => $ldata->lon,
                'time_in_packt' => $fdate,
                'natwork' => $ldata->network,
                'packet_status' => $device_status,
                'gps_signal' => $gpa,
            ]);
        } else {
            return response()->json(['error' => 'No live data found'], 404);
        }
    }


    /**
     * Store a newly created device
     */
  
     
    public function store(Request $request)
    {
        if ($this->role === 'sub-admin'  && isset($this->accesss['add']) != '1') {
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255|unique:devices,device_id',
            'vehicle_no' => 'required|string|max:255|unique:devices,vehicle_no',
            // Add validation for other fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // Convert checkbox values to boolean
        $data = array(
            "device_id" => $request->device_id,
            "vehicle_no" => $request->vehicle_no,
            "protocol" => $request->protocol,
            "agency" => $request->agency,
            "depot" => $request->depot,
            "region_id" => $request->region_id,
            "packet_status" => $request->packet_status,
        );

        if (Device::create($data)) {
            return response()->json([
                'status' => true,
                'message' => 'Device created successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }
    }



    /**
     * Update the specified device
     */
    public function update(Request $request)
    {
 
        if ($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1') {
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }
        $id = $request->id;
       if($id != null){
            $device = Device::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string|max:255|unique:devices,device_id,' . $id,
                'vehicle_no' => 'required|string|max:255|unique:devices,vehicle_no,' . $id,
                // Add validation for other fields as needed
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }


            // Convert checkbox values to boolean
            $data = array(
                "device_id" => $request->device_id,
                "vehicle_no" => $request->vehicle_no,
                "protocol" => $request->protocol,
                "agency" => $request->agency,
                "depot" => $request->depot,
                "region_id" => $request->region_id,
                "packet_status" => $request->packet_status,
            );
            if ($device->update($data)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Device updated successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed and try again..'
                ]);
            }
         }else{
                  $validator = Validator::make($request->all(), [
                  'device_id' => 'required|string|max:255|unique:devices,device_id',
                  'vehicle_no' => 'required|string|max:255|unique:devices,vehicle_no',
                  // Add validation for other fields as needed
              ]);

              if ($validator->fails()) {
                  return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
              }

              // Convert checkbox values to boolean
              $data = array(
                  "device_id" => $request->device_id,
                  "vehicle_no" => $request->vehicle_no,
                  "protocol" => $request->protocol,
                  "agency" => $request->agency,
                  "depot" => $request->depot,
                  "region_id" => $request->region_id,
                  "packet_status" => $request->packet_status,
              );

              if (Device::create($data)) {
                  return response()->json([
                      'status' => true,
                      'message' => 'Device created successfully'
                  ]);
              } else {
                  return response()->json([
                      'status' => false,
                      'message' => 'Failed and try again..'
                  ]);
              }
       
         }
    }




    /**
     * Remove the specified device
     */
    public function destroy($id)
    {
        if ($this->role === 'sub-admin'  && isset($this->accesss['delete']) != '1') {
            return response()->json(['status' => false, 'message' => 'You have Not Delete Access']);
        }
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['status' => false, 'message' => 'Device not found.']);
        }

        $device->delete();

        return response()->json(['status' => true, 'message' => 'Device deleted successfully.']);
    }


    public function toggleStatus(Request $request, $id)
    {
        if ($this->role === 'sub-admin'  && isset($this->accesss['status']) != '1') {
            return response()->json(['status' => false, 'message' => 'You have Not Status Access']);
        }

        $response = [];
        if ($request->id) {
            $get_user = Device::where('id', $id)->first();

            if ($get_user->status == '1') {
                $val = '0';
            } else {
                $val = '1';
            }

            if (Device::where('id', $id)->update(['status' => $val])) {
                $response = ['status' => true, 'message' => 'Updated Successfully'];
            } else {
                $response = ['status' => false, 'message' => 'Something went wrong'];
            }
        } else {
            $response = ['status' => false, 'message' => 'Something went wrong'];
        }
        return response()->json($response);
    }




    /**
     * Import device from CSV
     */
    public function import(Request $request)
    {
        if ($this->role === 'sub-admin'  && isset($this->accesss['import']) != '1') {
            return response()->json(['status' => false, 'message' => 'You have Not Import Access']);
        }
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->deviceService->processCsvImport($request->file('csv_file'));
        return response()->json($result);
    }

    /**
     * Export device to CSV
     */
    public function export(Request $request)
    {  
        if ($this->role === 'sub-admin'  && isset($this->accesss['export']) != '1') {
            return response()->json(['status' => false, 'message' => 'You have Not Import Access']);
        }
        $status = $request->get('status');
         $filename = $this->deviceService->generateCsvExport($status);
        $filePath = 'public/' . $filename;
        return response()->download(storage_path('app/' . $filePath), 'device.xlsx');
    }
  
  
    public function device_get_data(){
    
      $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
                $currentDate = Carbon::now('UTC')->format('Y-m-d');

                $devices = DB::table('itms_data_update')
                    ->leftJoin('devices', function ($join) {
                        $join->on('devices.device_id', '=', 'itms_data_update.device_id')
                            ->where('itms_data_update.packet_type', 'LP');
                    })
                    ->select(
                        'devices.*',
                        'itms_data_update.date as updatedate',
                        'itms_data_update.time as updatetime',
                        'itms_data_update.firmware_version as updatefirmware_version',
                        'itms_data_update.network as updatenetwork',
                        'itms_data_update.gps as updategps',
                        'itms_data_update.device_id as newdeviceid',
                        'itms_data_update.lon as updatelon',
                        'itms_data_update.lat as updatelat',
                        DB::raw("
                            CASE 
                                WHEN STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 60 MINUTE < '$currentTime'
                                THEN 1 ELSE 0
                            END AS is_expired
                        "),
                        DB::raw("
                            CASE
                                WHEN STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') < '$currentDate'
                                THEN 2 ELSE 0
                            END AS is_previous_date
                        ")
                    )
                    ->orderBy('itms_data_update.device_id', 'DESC')
                    ->get();

                // Status counters
                $statusCounts = [
                    'daly_community' => 0,
                    'communication_lost' => 0,
                    'communicating' => 0,
                    'total' => $devices->count()
                ];

                foreach ($devices as $device) {
                    if (empty($device->updatedate) || empty($device->updatetime)) {
                        $device->status_color = 'orange';
                        $device->status_name = 'Delay Communicating';
                        $device->status_code = 'daly_community';
                        $statusCounts['daly_community']++;
                    } elseif ($device->is_previous_date == 2) {
                        $device->status_color = 'red';
                        $device->status_name = 'Communication Lost';
                        $device->status_code = 'communication_lost';
                        $statusCounts['communication_lost']++;
                    } elseif ($device->is_expired) {
                        $device->status_color = 'orange';
                        $device->status_name = 'Delay Communicating';
                        $device->status_code = 'daly_community';
                        $statusCounts['daly_community']++;
                    } else {
                        $device->status_color = 'green';
                        $device->status_name = 'Communicating';
                        $device->status_code = 'communicating';
                        $statusCounts['communicating']++;
                    }
                }

                // Output
                return response()->json([
                    'devices' => $devices,
                    'status_summary' => $statusCounts
                ]);

    }
  
  
  
     public function download_communicating_device_data()
    {
        return view('admin.device_download_communicating_data');
    }

    /**
     * Handle the CSV download request
     */
   public function download_communicating_downloadCsv(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'token' => 'required'
    ]);

    $date = $request->input('date');
    $token = $request->input('token');

    // Set cookie to indicate download has started
    Cookie::queue('downloadToken', $token, 1); // 1 minute expiry

    // Get device stats from staging database
    $deviceStats = DB::table('itms_device_stats')
        ->where('data_date', $date)
        ->select('device_id', 'data_date')
        ->get();

    // Get all devices from update table
    $allDevices = DB::table('itms_data_update')
        ->select('device_id', 'firmware_version')
        ->get();

    if ($allDevices->isEmpty()) {
        return redirect()->route('download.communicating')
            ->with('error', 'No data found in update table.');
    }

    // Build list of online device IDs
    $onlineIds = $deviceStats->pluck('device_id')->toArray();

    // Prepare CSV data
    $csvData = [];
    $csvData[] = ['Device ID', 'Data Date', 'Depot Name', 'Firmware Version', 'Status'];

    foreach ($allDevices as $device) {
        // Get depot name from prime database
        $depot = DB::table('vehicles')
            ->where('device_id', $device->device_id)
            ->value('depot') ?? '';

        // Get firmware version from update table
        $firmware = $device->firmware_version ?? '';

        // Check if this device is online for selected date
        $status = in_array($device->device_id, $onlineIds) ? 'Online' : 'Offline';

        $csvData[] = [
            $device->device_id,
            $date,
            $depot,
            $firmware,
            $status
        ];
    }

    // Generate CSV content
    $csvContent = $this->arrayToCsv($csvData);

    // Return CSV download response
    return Response::make($csvContent, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="device_export_' . $date . '.csv"',
    ]);
}

/**
 * Convert array to CSV string
 */
private function arrayToCsv(array $data)
{
    $output = fopen('php://temp', 'w');

    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    rewind($output);
    $csvContent = stream_get_contents($output);
    fclose($output);

    return $csvContent;
}

/**
 * AJAX endpoint to check download status
 */
public function checkDownloadStatus(Request $request)
{
    $token = $request->input('token');
    $cookieValue = $request->cookie('downloadToken');

    return response()->json([
        'ready' => $cookieValue === $token
    ]);
}
  
  
  
    public function row_data(Request $request){
                  $device_data = Device::get();
                     $device_numbers = DB::table('itms_data_update')
                        ->select('device_id')
                        ->distinct()
                        ->whereNotNull('device_id')
                        ->orderBy('device_id')
                        ->get();
    
    // Change this line - add 'device_numbers' to compact
    return view('admin.rawdata', compact('device_data', 'device_numbers'));

               }
                
 public function raw_data_get(Request $request){
                  if ($request->ajax()) {
                 // Start building the query
                      if($request->packet_type == 'raw_data'){
                         $query = DB::table('itms_data')
                                      ->leftJoin('devices', function ($join) {
                                          $join->on('itms_data.device_id', '=', 'devices.device_id')
                                           ->where('itms_data.device_type', 'VTS');
                                      })
                                      ->select(
                                          'itms_data.*',
                                          'devices.device_id as device_device_id',
                                          'devices.vehicle_no as fleet_number',
                                          'devices.depot as depot',
                                          'devices.region_id as region',
                                      );

                                  // Apply fleet number filter
                                  if ($request->has('fleet_number') && !empty($request->fleet_number)) {
                                      $query->where('itms_data.device_id', $request->fleet_number);
                                  }

                                  // Apply device number filter  
                                  if ($request->has('device_number') && !empty($request->device_number)) {
                                      $query->where('itms_data.device_id', $request->device_number);
                                  }

                                  // Apply date filter
                                  if ($request->has('date_filter') && !empty($request->date_filter)) {
                                      $formattedDate = Carbon::parse($request->date_filter)->format('d/m/Y');
                                      $query->where('itms_data.date', $formattedDate);
                                  }
                               }else{
                                $query = DB::table('itms_can_data')
                                      ->leftJoin('devices', function ($join) {
                                          $join->on('itms_can_data.device_id', '=', 'devices.device_id')
                                           ->where('itms_can_data.device_type', 'CAN');
                                      })
                                      ->select(
                                          'itms_can_data.*',
                                          'devices.device_id as device_device_id',
                                          'devices.vehicle_no as fleet_number',
                                          'devices.depot as depot',
                                          'devices.region_id as region',
                                      );

                                  // Apply fleet number filter
                                  if ($request->has('fleet_number') && !empty($request->fleet_number)) {
                                      $query->where('itms_can_data.device_id', $request->fleet_number);
                                  }

                                  // Apply device number filter  
                                  if ($request->has('device_number') && !empty($request->device_number)) {
                                      $query->where('itms_can_data.device_id', $request->device_number);
                                  }

                                  // Apply date filter
                                  if ($request->has('date_filter') && !empty($request->date_filter)) {
                                       $formattedDate = Carbon::parse($request->date_filter)->format('d/m/Y');
                                       $query->where('itms_can_data.date', $formattedDate);
                                  }
                                   
                                } 
                                  $rawdevice = $query->get();
                                  return DataTables::of($rawdevice)
                                    ->addIndexColumn()
                                     ->editColumn('oil_pressure', function($row) {
                                        return 'N/A';
                                    })
                                   ->editColumn('servertime', function($row) {
                                        return $row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : 'N/A';
                                    })
                                    ->rawColumns(['oil_pressure','servertime'])
                                    ->make(true);
                            }

                      return response()->json(['error' => 'Invalid request'], 400);

               }


               public function rowdata_export(Request $request){
                  $packet_type = $request->packet_type;
                  $fleet_number = $request->fleet_number;
                  $device_number = $request->device_number;
                  $date_filter = $request->date_filter;
                  $dataresponce = $this->deviceService->roedataCsvExport($packet_type,$fleet_number,$device_number,$date_filter);
                   if($dataresponce == true){
                        $response = ['status' => true, 'message' => 'File generated successfully.'];
                   }else{
                        $response = ['status' => false, 'message' => 'Failed and try again..']; 
                   }
                    return response()->json($response);
               }

              public function user_rowdata_list(Request $request){
                 if ($request->ajax()) {
                 // Start building the query
                              
                             $query = DB::table('rawdatalist')->where('user_id',$this->user_id);
                             $useradatalist = $query->get();
                                  return DataTables::of($useradatalist)
                                    ->addIndexColumn()
                                     ->editColumn('packettype', function($row) {
                                        if($row->packet_type == 'raw_data'){
                                          $packit_data = 'Raw Data';
                                        }else{
                                          $packit_data = 'Can Data';
                                        }
                                        return $packit_data;
                                    })

                                    ->editColumn('status', function($row) {
                                            if($row->status == 'incompleted'){
                                                $status = '<span class="text-warning">'.$row->status.'</span>';
                                            }elseif($row->status == 'completed'){
                                                $status = '<span class="text-success">'.$row->status.'</span>';
                                            }else{
                                                $status = '<span class="text-danger">'.$row->status.'</span>';
                                            }
                                            return $status;

                                    })
                                   ->editColumn('action', function($row) {
                                       
                                    if($row->status == 'incompleted'){
                                          $dwn = '<span class="text-warning">Processing</span>';
                                    }elseif($row->status == 'completed'){
                                    
                                       $fileUrl = asset('storage/app/' . $row->filePath);
                                          $dwn = '
                                              <a href="' . $fileUrl . '" 
                                                 class="btn btn-sm btn-success" 
                                                 download>
                                                 <i class="fas fa-download"></i> Download
                                              </a>
                                          ';
                                    }else{
                                            $dwn = '<span class="text-danger">Failed</span>';
                                    }
                                          return $dwn;

                                    })
                                    ->rawColumns(['packettype','status','action'])
                                    ->make(true);
                            }

                      return response()->json(['error' => 'Invalid request'], 400);
              }
  
              public function deleteOldRecords()
              {  
                  $filename = $this->deviceService->deletrecode15days();
                  
                   return true;
                  
              }


             public function roedataCsvExport(Request $request)
    {
        $user = auth('admin')->user();
        $date_filter = $request->date_filter;
        $dats = Carbon::parse($date_filter)->format('d-m-Y');
        
        // Validate required fields
        if (!$request->packet_type || !$request->date_filter) {
            return response()->json([
                'success' => false, 
                'message' => 'Packet type and date are required'
            ], 400);
        }

        if (!$request->fleet_number && !$request->device_number) {
            return response()->json([
                'success' => false, 
                'message' => 'Either fleet number or device number is required'
            ], 400);
        }
        
        $data = [
            "user_id" => $user->id,
            "packet_type" => $request->packet_type,
            "fleet_number" => $request->fleet_number ?: null,
            "device_number" => $request->device_number ?: null,
            "date_filter" => $dats,
            "filePath" => null,
            "status" => 'incompleted'
        ];

        $rowdata_id = DB::table('rawdatalist')->insertGetId($data);
        
        if (!$rowdata_id) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to create export request'
            ], 500);
        }

        // Dispatch the job to queue
        ProcessRawDataExport::dispatch($rowdata_id);

        return response()->json([
            'success' => true, 
            'message' => 'Export request queued successfully! Processing will begin shortly.',
            'export_id' => $rowdata_id
        ]);
    }

    // Method to check export status
    public function checkExportStatus($id)
    {
        $export = DB::table('rawdatalist')->where('id', $id)->first();
        
        if (!$export) {
            return response()->json(['success' => false, 'message' => 'Export not found'], 404);
        }

        $response = [
            'success' => true,
            'status' => $export->status,
            'created_at' => $export->created_at,
            'updated_at' => $export->updated_at
        ];

        if ($export->status === 'completed' && $export->filePath) {
            $response['download_url'] = asset('storage/' . str_replace('public/', '', $export->filePath));
        }

        if ($export->status === 'failed' && $export->error_message) {
            $response['error_message'] = $export->error_message;
        }

        return response()->json($response);
    }



       

}
