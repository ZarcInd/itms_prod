<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_id',
        'vehicle_no',
        'protocol',
        'agency',
        'depot',
        'status',
        'region_id',
        'packet_status',
    ];

      public function vehicle()
        {
            return $this->hasOne(Vehicles::class, 'device_id', 'device_id');
        }
}
