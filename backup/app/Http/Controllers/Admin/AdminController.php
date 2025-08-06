<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{   
        public $accesss;

        public $role;
    public function __construct()
    {   
        $google_map_kry = config('services.GOOGLE_MAP_KEY');
        View::share('aarvy_link',$google_map_kry);

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
                View::share('accesss', $access_new);
                $this->accesss = $access_new; 

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
        if($this->role === 'sub-admin'  && isset($this->accesss[1]['view']) != '1'){
             return view('admin.noacesss');
        }
        return view('admin.dashboard');
    }

    public function live_tracking(){
        if($this->role === 'sub-admin'  && isset($this->accesss[2]['view']) != '1'){
             return view('admin.noacesss');
        }
        return view('admin.live_tracking');
    }

    public function update_profile(){
        if($this->role === 'sub-admin'  && isset($this->accesss[12]['view']) != '1'){
             return view('admin.noacesss');
        }
        $google_clint_key = config('services.GOOGLE_RECAPTCHA_CLIENT_KEY');
        return view('admin.auth.profile');
    } 

   
 public function updateProfileImage(Request $request){

    if($this->role === 'sub-admin'  && isset($this->accesss[12]['edit']) != '1'){
             return view('admin.noacesss');
        }
    $validator = Validator::make($request->all(), [
    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    
  ]);
    if ($validator->fails()) {
   
        return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
    }
    //  $tab = $request->get('tab');
    $data = array();

    if ($request->file('file')) {
        $file = $request->file('file');
        $destinationpath = public_path('/attach/image/');
        $fnn = rand() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationpath, $fnn);
        $pic = 'attach/image/' . $fnn;
        $data['file'] = $pic;
    }

    if (Admin::insert($data)) {

        return response()->json(['status' => true, 'message' => 'Add Successfully..']);
        
    } else {
        return response()->json(['status' => false, 'message' => 'Failed and try again..']);
    }
  }
    
   
    

  
  
 
}
