<?php

namespace App\Services;

use App\Models\Vehicles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTimeZone;

class VehicleService
{
    /**
     * Process CSV file and insert/update vehicle records
     *
     * @param UploadedFile $file
     * @return array
     */
    public function __construct()
    {
        //
    }

    public function remove_special_char($arr_val)
    {
        return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $arr_val);
    }

    
         public function processCsvImport(UploadedFile $file)
          {
              $path = $file->getRealPath();
              $handle = fopen($path, 'r');

              if (!$handle) {
                  return [
                      'status' => false,
                      'message' => 'Could not open file'
                  ];
              }

              $header = fgetcsv($handle);
              if (!$header) {
                  fclose($handle);
                  return [
                      'status' => false,
                      'message' => 'Empty or invalid CSV file'
                  ];
              }

              $success = 0;
              $error = 0;
              $rows = [];

              // Read all rows into an array
              while (($row = fgetcsv($handle)) !== false) {
                  $rows[] = $row;
              }

              fclose($handle);

              for ($i = 0; $i < count($rows); $i++) {
                  $row = $rows[$i];

                  if (empty($row) || count($row) < 22) {
                      continue;
                  }

                  $data = [
                      'vehicle_no' => $this->remove_special_char($row[0]),
                      'vehicle_code' => $this->remove_special_char($row[1]),
                      'device_id' => $this->remove_special_char($row[2]),
                      'city' => $this->remove_special_char($row[3]),
                      'agency' => $this->remove_special_char($row[4]),
                      'operator' => $this->remove_special_char($row[5]),
                      'depot' => $this->remove_special_char($row[6]),
                      'vehicle_type' => $this->remove_special_char($row[7]),
                      'seating_capacity' => $this->remove_special_char($row[8]),
                      'region' => $this->remove_special_char($row[9]),
                      'etim_frequency' => $this->remove_special_char($row[10]),
                      'gst_on_ticket' => $this->remove_special_char($row[11]),
                      'surcharge_on_ticket' => $this->remove_special_char($row[12]),
                      'collection_on_etim' => $this->remove_special_char($row[13]),
                      'gps_from_etim' => $this->remove_special_char($row[14]),
                      'forward_to_shuttl' => $this->remove_special_char($row[15]),
                      'service_category' => $this->remove_special_char($row[16]),
                      'fuel_type' => $this->remove_special_char($row[17]),
                      'dispatch_type' => $this->remove_special_char($row[18]),
                      'route_name' => $this->remove_special_char($row[19]),
                      'service_start_time' => $this->remove_special_char($row[20]),
                      'service_end_time' => $this->remove_special_char($row[21]),
                  ];



                  if (empty($data['device_id'])) {
                      $error++;
                      continue;
                  }

                  // Update or Insert logic here
                  $existing = Vehicles::where('device_id', $data['device_id'])->first();

                  if ($existing) {
                      // Update existing record
                      $existing->update($data);
                  } else {
                      // Insert new record
                      Vehicles::create($data);
                  }

                  $success++;
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
                        $statusFilters = $status;
                        
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

                        $vehicles = $query->get();
        //$vehicles = Vehicles::all();
        $filename = 'vehicles_export_' . date('YmdHis') . '.csv';
        $filepath = storage_path('app/public/' . $filename);
        
        $headers = [
            'Vehicle No', 'Vehicle Code', 'DeviceID', 'City', 'Agency', 'Operator', 'Depot',
            'VehicleType', 'Seating Capacity', 'Region', 'Etim Frequency', 'GST On Ticket',
            'Surcharge On Ticket', 'Collection On Etim','GPS', 'Speed', 'Last Seen Packet', 'Vehicle Status', 'Status', 'GPS From Etim',
            'Forward to Shuttl', 'Service Category', 'Fuel Type', 'Dispatch Type',
            'Route Name', 'Service Start Time', 'Service End Time'
        ];
        
        $file = fopen($filepath, 'w');
        fputcsv($file, $headers);
        
        foreach ($vehicles as $vehicle) {
            $gpsStatus = ($vehicle->updategps == 'A') ? 'Lock' : 'Unlock';
            if($vehicle->status == '1'){
               $status = 'Active';
            }else{
               $status = 'Inactive';
            }
          
           
          if ((!empty($vehicle->updatedate)) || (!empty($vehicle->updatetime))) {
                 if ($vehicle->is_expired) {
                    $status_name = 'Offline';
                  } else {
                    $status_name = 'Online';
                  }
             }else{
                $status_name = 'Offline';
             } 
      
             if ((!empty($vehicle->updatedate)) && (!empty($vehicle->updatetime))) {
                                  $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $vehicle->updatedate . ' ' . $vehicle->updatetime);

                                  // Format as dd/mm/yyyy hh:mm A (e.g., 04/05/2025 02:30 PM)
                                  $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                  $vdatetime = $datetime->format('d/m/Y h:i A');
                              } else {
                                  $vdatetime = '';
                              }
            $row = [
                $vehicle->vehicle_no,
                $vehicle->vehicle_code,
                $vehicle->device_id,
                $vehicle->city,
                $vehicle->agency,
                $vehicle->operator,
                $vehicle->depot,
                $vehicle->vehicle_type,
                $vehicle->seating_capacity,
                $vehicle->region,
                $vehicle->etim_frequency,
                $vehicle->gst_on_ticket,
                $vehicle->surcharge_on_ticket,
                $vehicle->collection_on_etim,
                $gpsStatus,
                $vehicle->updatespeed_kmh,
                $vdatetime,
                $status_name,
                $status,
                $vehicle->gps_from_etim,
                $vehicle->forward_to_shuttl,
                $vehicle->service_category,
                $vehicle->fuel_type,
                $vehicle->dispatch_type,
                $vehicle->route_name,
                $vehicle->service_start_time,
                $vehicle->service_end_time,
            ];

            
            fputcsv($file, $row);
        }
        
        fclose($file);
        return $filename;
    }

}
