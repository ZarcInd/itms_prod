<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MessagesController extends Controller
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
                View::share('accesss', $access_new[8]);
                $this->accesss = $access_new[8]; 

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

    public function index(){
        if ($this->role === 'sub-admin'  && isset($this->accesss['view']) != '1') {
            return view('admin.noacesss');
        }
        return view('admin.message');
    }
}
