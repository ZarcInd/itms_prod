<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RouteController extends Controller
{   
     public $accesss;

    public $role;

    public function __construct()
    {

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
                View::share('accesss', $access_new[5]);
                $this->accesss = $access_new[5]; 

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
     
    public function index()
    {
         if ($this->role === 'sub-admin'  && isset($this->accesss['view']) != '1') {
            return view('admin.noacesss');
        }
        return view('admin.maproute');
    }

        public function getRouteData(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $vehicleId = $request->vehicle_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get vehicle tracking data
        $trackingData = DB::table('vehicle_tracking')
            ->where('vehicle_id', $vehicleId)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'recorded_at', 'speed', 'address']);

        if ($trackingData->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No tracking data found for the specified period'
            ]);
        }

        // Get vehicle details
        $vehicle = DB::table('vehicles')->find($vehicleId);

        // Format data for Google Maps
        $routePoints = $trackingData->map(function ($point) {
            return [
                'lat' => (float) $point->latitude,
                'lng' => (float) $point->longitude,
                'timestamp' => $point->recorded_at,
                'speed' => $point->speed ?? 0,
                'address' => $point->address ?? 'Unknown location'
            ];
        });

        return response()->json([
            'success' => true,
            'vehicle' => $vehicle,
            'route_points' => $routePoints,
            'start_point' => $routePoints->first(),
            'end_point' => $routePoints->last(),
            'total_points' => $routePoints->count()
        ]);
    }

}
