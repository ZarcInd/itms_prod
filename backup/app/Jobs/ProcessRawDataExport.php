<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessRawDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rawDataId;
    public $timeout = 300; // 5 minutes timeout
    public $tries = 3; // Retry 3 times on failure

    public function __construct($rawDataId)
    {
        $this->rawDataId = $rawDataId;
    }

    public function handle()
    {
        try {
            $rawDataRecord = DB::table('rawdatalist')->where('id', $this->rawDataId)->first();
            
            if (!$rawDataRecord || $rawDataRecord->status !== 'incompleted') {
                return;
            }

            // Update status to processing
            DB::table('rawdatalist')
                ->where('id', $this->rawDataId)
                ->update(['status' => 'processing', 'updated_at' => now()]);

            // Process the export using your existing logic
            $result = $this->processExport($rawDataRecord);

            if ($result) {
                DB::table('rawdatalist')
                    ->where('id', $this->rawDataId)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => now()
                    ]);
            } else {
                throw new \Exception('Export processing failed');
            }

        } catch (\Exception $e) {
            Log::error('Raw data export job failed: ' . $e->getMessage(), [
                'rawDataId' => $this->rawDataId,
                'error' => $e->getTraceAsString()
            ]);

            DB::table('rawdatalist')
                ->where('id', $this->rawDataId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now()
                ]);
        }
    }

    private function processExport($record)
    {
        // Your existing export logic here (from the original function)
        $packet_type = strtolower($record->packet_type);
        $fleet_number = $record->fleet_number;
        $device_number = $record->device_number;
        $date_filter = $record->date_filter;

        // Validate packet_type
        if (!in_array($packet_type, ['raw_data', 'can_data'])) {
            return false;
        }

        if ($packet_type == 'raw_data') {
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

            if ($fleet_number && !empty($fleet_number)) {
                $query->where('itms_data.device_id', $fleet_number);
            }

            if ($device_number && !empty($device_number)) {
                $query->where('itms_data.device_id', $device_number);
            }

            if ($date_filter && !empty($date_filter)) {
                $formattedDate = Carbon::parse($date_filter)->format('d/m/Y');
                $query->where('itms_data.date', $formattedDate);
            }
        } else {
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

            if ($fleet_number && !empty($fleet_number)) {
                $query->where('itms_can_data.device_id', $fleet_number);
            }

            if ($device_number && !empty($device_number)) {
                $query->where('itms_can_data.device_id', $device_number);
            }

            if ($date_filter && !empty($date_filter)) {
                $formattedDate = Carbon::parse($date_filter)->format('d/m/Y');
                $query->where('itms_can_data.date', $formattedDate);
            }
        }

        $rawdevice = $query->get();

        $filename = 'rawdata_export_' . date('YmdHis') . '_' . $record->id . '.csv';
        $filepath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $headers = [
            'Region', 'Depot', 'Fleet Number', 'Device Unique ID', 'Packet Header',
            'Mode', 'Device Type', 'Packet Type', 'Firmware Version', 'Time',
            'Date', 'Speed/Kmh', 'Oil Pressure', 'Server Time','Ignition','Driver Id','Gps','Lat','Lat dir','Lon','Lon dir',
            'Speed Knots','Network','Route No','Odo meter','Led Health 1','Led Health 2','Led Health 3','Led Health 4','Partition Key'
        ];

        $file = fopen($filepath, 'w');
        if (!$file) {
            throw new \Exception('Could not create CSV file');
        }

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

        // Update the record with file path
        $filePath = 'public/' . $filename;
        DB::table('rawdatalist')
            ->where('id', $record->id)
            ->update(['filePath' => $filePath]);

        return true;
    }
}


