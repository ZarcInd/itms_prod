<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;

class LogController extends Controller
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
                View::share('accesss', $access_new[7]);
                $this->accesss = $access_new[7]; 

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
        return view('admin.logs');
    }

    public function getData(Request $request)
    {
        // Static data that matches the screenshot
        $data = [
            [
                'id' => 1,
                'vehicle' => 'Vehicle 1',
                'tracking_raw_data' => '120, 450, 760',
                'health_raw_data' => '120, 450, 760',
                'alert_raw_data' => '120, 450, 760',
                'login_raw_data' => '120, 450, 760',
            ],
            [
                'id' => 2,
                'vehicle' => 'Vehicle 2',
                'tracking_raw_data' => '220, 350, 660',
                'health_raw_data' => '220, 350, 660',
                'alert_raw_data' => '230, 350, 870',
                'login_raw_data' => '220, 350, 660',
            ],
            [
                'id' => 3,
                'vehicle' => 'Vehicle 3',
                'tracking_raw_data' => '320, 250, 560',
                'health_raw_data' => '320, 250, 560',
                'alert_raw_data' => '340, 650, 980',
                'login_raw_data' => '320, 250, 560',
            ],
            [
                'id' => 4,
                'vehicle' => 'Vehicle 4',
                'tracking_raw_data' => '420, 150, 460',
                'health_raw_data' => '420, 150, 460',
                'alert_raw_data' => '450, 750, 1090',
                'login_raw_data' => '420, 150, 460',
            ],
            [
                'id' => 5,
                'vehicle' => 'Vehicle 5',
                'tracking_raw_data' => '520, 50, 360',
                'health_raw_data' => '520, 50, 360',
                'alert_raw_data' => '560, 850, 1200',
                'login_raw_data' => '520, 50, 360',
            ],
            [
                'id' => 6,
                'vehicle' => 'Vehicle 6',
                'tracking_raw_data' => '620, -50, 260',
                'health_raw_data' => '620, -50, 260',
                'alert_raw_data' => '670, 950, 1310',
                'login_raw_data' => '620, -50, 260',
            ],
            [
                'id' => 7,
                'vehicle' => 'Vehicle 7',
                'tracking_raw_data' => '720, -150, 160',
                'health_raw_data' => '720, -150, 160',
                'alert_raw_data' => '780, 1050, 1420',
                'login_raw_data' => '720, -150, 160',
            ],
            [
                'id' => 8,
                'vehicle' => 'Vehicle 8',
                'tracking_raw_data' => '820, -250, 60',
                'health_raw_data' => '820, -250, 60',
                'alert_raw_data' => '890, 1150, 1530',
                'login_raw_data' => '820, -250, 60',
            ],
            [
                'id' => 9,
                'vehicle' => 'Vehicle 9',
                'tracking_raw_data' => '920, -350, -40',
                'health_raw_data' => '920, -350, -40',
                'alert_raw_data' => '1000, 1250, 1640',
                'login_raw_data' => '920, -350, -40',
            ],
        ];

        // Apply filters if needed
        if ($request->has('vehicle') && $request->vehicle) {
            $vehicle = $request->vehicle;
            $data = array_filter($data, function ($item) use ($vehicle) {
                return strpos($item['vehicle'], $vehicle) !== false;
            });
        }

        if ($request->has('group') && $request->group) {
            // Implement group filtering logic if needed
        }

        if ($request->has('date') && $request->date) {
            // Implement date filtering logic if needed
        }

        return DataTables::of($data)->toJson();
    }
}
