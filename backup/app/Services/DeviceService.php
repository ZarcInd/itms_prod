<?php

namespace App\Services;
use App\Models\Device;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTimeZone;



class DeviceService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function remove_special_char($arr_val)
    {
        return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $arr_val);
    }

    
    public function processCsvimport(UploadedFile $file){
        $path = $file->getRealPath();
        $file = file($path);
        $file_1 = array_map('str_getcsv', file($path));
        $data = array();
        $error = 0;
        $success = 0;
        for ($i = 1; $i < count($file); $i++){
            $arr = explode(',', $file[$i]);
                     $data = array(
                        'device_id' => $this->remove_special_char(str_replace('#@"', ',', $arr[0])),
                        'vehicle_no' => $this->remove_special_char(str_replace('#@', ',', $arr[1])),
                        'agency' => $this->remove_special_char(str_replace('#@', ',', $arr[2])),
                        'depot' => $this->remove_special_char(str_replace('#@', ',', $arr[3])),
                        'protocol' => $this->remove_special_char(str_replace('#@', ',', $arr[4])),
                        'region_id' => $this->remove_special_char(str_replace('#@', ',', $arr[5])),
                    );
            
                    $device = null;
                    if (!empty($arr[0])) {
                        $device = Device::where('device_id', $arr[0])->get();
                    }

                   

                    try {
                        if($device->count() > 0){
                            $device->update($data);
                            $success++;
                        }else{
                            Device::create($data);
                            $success++;
                        }
                      
                    } catch (\Exception $e) {
                                    Log::error('Failed to save device data: ' . $e->getMessage(), ['data' => $data]);
                                    $error++;
                        }
         
        }

        return [
            'status' => true,
            'success' => $success,
            'failed' => $error,
            'message' => "Processed successfully: {$success} inserted/updated, {$error} failed"
        ];

    }
    
    /**
     * Generate CSV file from vehicles data
     *
     * @return string Path to generated CSV file
     */
    public function generateCsvExport($status): string
    {    
            
         $status = explode(',',$status);
         $currentTime = Carbon::now('UTC')->format('Y-m-d H:i:s');
            $currentDate = Carbon::now('UTC')->format('Y-m-d');
            $query = DB::table('itms_data_update')
            ->leftJoin('devices', function ($join) {
                $join->on('devices.device_id', '=', 'itms_data_update.device_id')
                    ->where('itms_data_update.packet_type', 'LP');
            })
            ->select('devices.*', 'itms_data_update.date as updatedate', 'itms_data_update.time as updatetime', 
                'itms_data_update.firmware_version as updatefirmware_version', 'itms_data_update.network as updatenetwork', 
                'itms_data_update.gps as updategps','itms_data_update.speed_kmh as updatespeed_kmh','itms_data_update.device_id as newdeviceid', 'itms_data_update.lon as updatelon', 'itms_data_update.lat as updatelat', 
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
          if ($status && is_array($status) && !in_array('All', $status)) {
              $query->where(function($mainQuery) use ($status, $currentTime, $currentDate) {
                  foreach ($status as $statuss) {
                      $mainQuery->orWhere(function($q) use ($statuss, $currentTime, $currentDate) {
                          if ($statuss == 'communation_lost') {
                              // Filter for Communication Lost status
                              $q->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') < '$currentDate'");
                          } 
                          elseif ($statuss == 'daly_community') {
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
                          elseif ($statuss == 'community') {
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

         
        $device = $query->orderBy('itms_data_update.device_id', 'DESC')->get();
        // Apply status filter
        if ($status != 'All' && $status != null) {
            if ($status == 'communation_lost') {
                // Filter for Communication Lost status
                $query->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') < '$currentDate'");
            } 
            elseif ($status == 'daly_community') {
                // Filter for Daly Community status (offline in current date OR null data)
                $query->where(function($q) use ($currentTime, $currentDate) {
                    $q->whereNull('itms_data_update.date')
                      ->orWhereNull('itms_data_update.time')
                      ->orWhere(function($subq) use ($currentTime, $currentDate) {
                          $subq->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = '$currentDate'")
                               ->whereRaw("STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 120 SECOND < '$currentTime'");
                      });
                });
            }
            elseif ($status == 'community') {
                // Filter for Community status (everything is working fine)
                $query->whereNotNull('itms_data_update.date')
                    ->whereNotNull('itms_data_update.time')
                    ->whereRaw("STR_TO_DATE(itms_data_update.date, '%d/%m/%Y') = '$currentDate'")
                    ->whereRaw("STR_TO_DATE(CONCAT(itms_data_update.date, ' ', itms_data_update.time), '%d/%m/%Y %H:%i:%s') + INTERVAL 60 MINUTE >= '$currentTime'");
            }
        }
        
        $Devices = $query->orderBy('devices.id', 'DESC')->get();
        //$Devices = Device::all();
        $filename = 'vehicles_export_' . date('YmdHis') . '.csv';
        $filepath = storage_path('app/public/' . $filename);
        
        $headers = [
              'DeviceID','Vehicle No','Agency','Depot','Firmware Version','Network','Lest Seen Packet','GPS','Speed','Protocol','Region Id','Status'
        ];
        
        $file = fopen($filepath, 'w');
        fputcsv($file, $headers);
        foreach ($Devices as $device) {
           $gpsStatus = ($device->updategps == 'A') ? 'Lock' : 'Unlock';
         if ((!empty($device->updatedate)) && (!empty($device->updatetime))) {
                $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $device->updatedate . ' ' . $device->updatetime);
                                  // Format as dd/mm/yyyy hh:mm A (e.g., 04/05/2025 02:30 PM)
                $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                 $devicedatetime = $datetime->format('d/m/Y h:i A');
                } else {
                 $devicedatetime = '';
                }
                
           if ((empty($device->updatedate)) || (empty($device->updatetime))) {
                      $status_name = 'Delay Communicating';
                     $status_code = 'daly_community';
                  } elseif ($device->is_previous_date == 2) {
                      $device_color = 'orange'; // You can choose any color to represent this
                      $status_name = 'Communication Lost';
                  } elseif ($device->is_expired) {
                      $device_color = 'red';
                      $status_name = 'Delay Communicating';
                     $status_code = 'daly_community';
                  } else {
                      $device_color = 'green';
                      $status_name = 'Communicating';
                     $status_code = 'community';
                  }
                  
            $row = [
                $device->newdeviceid,
                $device->vehicle_no,
                $device->agency,
                $device->depot,
                $device->updatefirmware_version,
                $device->updatenetwork,
                $devicedatetime,
                $gpsStatus,
                $device->updatespeed_kmh,
                $device->protocol,
                $device->region_id,
                $status_name,
              ];
             
            fputcsv($file, $row);
        }
        
        fclose($file);
        return $filename;
    }
  
     public function roedataCsvExport($packet_type,$fleet_number,$device_number,$date_filter){
         $user = auth('admin')->user();
         $dats = Carbon::parse($date_filter)->format('d-m-Y');
        $data =array(
          "user_id" => $user->id,
          "packet_type" => $packet_type,
          "fleet_number" => $fleet_number,
          "device_number" => $device_number,
          "date_filter" => $dats,
          "filePath"=>  null,
         );

        $rowdata_id = DB::table('rawdatalist')->insertGetId($data);
        if(!$rowdata_id){
            return false;
        }
        
         // Check if packet_type is 'raw_data' or 'can_data'
         $packet_type = strtolower($packet_type);
         $fleet_number = strtolower($fleet_number);
         $device_number = strtolower($device_number);
         
         // Validate packet_type
         if (!in_array($packet_type, ['raw_data', 'can_data'])) {
             return false; // Invalid packet type
         }
        if($packet_type == 'raw_data'){
            
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
                                  if ($fleet_number && !empty($fleet_number)) {
                                      $query->where('itms_data.device_id', $fleet_number);
                                  }

                                  // Apply device number filter  
                                  if ($device_number && !empty($device_number)) {
                                      $query->where('itms_data.device_id', $device_number);
                                  }

                                  // Apply date filter
                                  if ($date_filter && !empty($date_filter)) {
                                      $formattedDate = Carbon::parse($date_filter)->format('d/m/Y');
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
                                  if ($fleet_number && !empty($date_filter)) {
                                      $query->where('itms_can_data.device_id', $request->fleet_number);
                                  }

                                  // Apply device number filter  
                                  if ($device_number && !empty($device_number)) {
                                      $query->where('itms_can_data.device_id', $device_number);
                                  }

                                  // Apply date filter
                                  if ($date_filter && !empty($date_filter)) {
                                       $formattedDate = Carbon::parse($date_filter)->format('d/m/Y');
                                       $query->where('itms_can_data.date', $formattedDate);
                                  }
                                   
                                } 
                                  $rawdevice = $query->get();
       
                      $filename = 'rawdata_export_' . date('YmdHis') . '.csv';
                      $filepath = storage_path('app/public/' . $filename);

                      $headers = [
                            'Region','Depot','Fleet Number','Device Unique ID','Packet Header','Mode','Device Type','Packet Type','Framware Version','Time','date','Speed/Kmh','Oil Pressure','Server Time',
                            'Ignition','Driver Id','Gps','Lat','Lat dir','Lon','Lon dir','Speed Knots','Network','Route No','Odo meter','Led Health 1','Led Health 2','Led Health 3','Led Health 4','Partition Key'
                      ];

                      $file = fopen($filepath, 'w');
                      fputcsv($file, $headers);
                      foreach ($rawdevice as $rowdata) {
                     $oil_pressure = 'N/A';
                     $servertime = $rowdata->created_at ? date('Y-m-d H:i:s', strtotime($rowdata->created_at)) : 'N/A';
                    
                  
            $row = [
                $rowdata->region ?? 'N/A',
                $rowdata->depot ?? 'N/A',
                $rowdata->fleet_number ?? 'N/A',
                $rowdata->device_device_id ?? 'N/A',
                $rowdata->packet_header ?? 'N/A',
                $rowdata->mode ?? 'N/A',
                $rowdata->device_type ?? 'N/A',
                $rowdata->packet_type ?? 'N/A',
                $rowdata->firmware_version ?? 'N/A',
                $rowdata->time ?? 'N/A',
                $rowdata->date ?? 'N/A',
                $rowdata->speed_kmh ?? 'N/A',
                $oil_pressure,
                $servertime,
                $rowdata->ignition ?? 'N/A',
                $rowdata->driver_id ?? 'N/A',
                $rowdata->gps ?? 'N/A', 
                $rowdata->lat ?? 'N/A',
                $rowdata->lat_dir ?? 'N/A',
                $rowdata->lon ?? 'N/A',
                $rowdata->lon_dir ?? 'N/A',
                $rowdata->speed_knots ?? 'N/A',
                $rowdata->network ?? 'N/A',
                $rowdata->route_no ?? 'N/A',
                $rowdata->odo_meter ?? 'N/A',
                $rowdata->led_health_1 ?? 'N/A',
                $rowdata->led_health_2 ?? 'N/A',
                $rowdata->led_health_3 ?? 'N/A',
                $rowdata->led_health_4 ?? 'N/A',
                $rowdata->partition_key ?? 'N/A'
               ];

            fputcsv($file, $row);
        }
        fclose($file);
     
       
        $filePath = 'public/' . $filename;
        $dats = Carbon::parse($date_filter)->format('d-m-Y');
        $data =array(
          "user_id" => $user->id,
          "packet_type" => $packet_type,
          "fleet_number" => $fleet_number,
          "device_number" => $device_number,
          "date_filter" => $dats,
          "filePath"=> $filePath,
          "status" => 'completed',
         );
          if(DB::table('rawdatalist')->where('id',$rowdata_id)->update($data)){
           return true;  
          }else{
           return false;
          }
        
     }
  
           public function deletrecode15days(){
               
                    // Get the date 15 days ago in UTC
                  $before15Days = Carbon::now('UTC')->subDays(15)->format('Y-m-d');
                  // Delete from itms_data where created_at < $before15Days
                  DB::table('itms_data')
                      ->whereDate('created_at', '<', $before15Days)
                      ->delete();

                  // Delete from itms_can_data where created_at < $before15Days
                  DB::table('itms_can_data')
                      ->whereDate('created_at', '<', $before15Days)
                      ->delete();

                  return true;
               
           }
  
        
}
