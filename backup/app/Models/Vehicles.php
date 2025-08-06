<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vehicle_no',
        'vehicle_code',
        'device_id',
        'city',
        'agency',
        'operator',
        'depot',
        'vehicle_type',
        'seating_capacity',
        'region',
        'etim_frequency',
        'gst_on_ticket',
        'surcharge_on_ticket',
        'collection_on_etim',
        'status',
        'gps_from_etim',
        'forward_to_shuttl',
        'service_category',
        'fuel_type',
        'dispatch_type',
        'route_name',
        'service_start_time',
        'service_end_time',
    ];

      
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    

}
