<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
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
                View::share('accesss', $access_new[12]);
                $this->accesss = $access_new[12]; 

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
      /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {   
        if ($this->role === 'sub-admin'  && isset($this->accesss['view']) != '1') {
            return view('admin.noacesss');
        }
        // Get current user
        $user = Auth::guard('admin')->user();
        return view('admin.auth.profile')->with('user', $user);
    }

    /**
     * Update the user's profile image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfileImage(Request $request)
    {
            if($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1'){
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Failed and try again..'
                                ]);
                    }

        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => $validator->errors()->first()
            ]);
        }

        $data = array();

        if ($request->file('profile_image')) {
            $file = $request->file('profile_image');
            $destinationpath = public_path('/attach/image/');
            $fnn = rand() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationpath, $fnn);
            $pic = 'attach/image/' . $fnn;
            $data['profile_image'] = $pic;
        }

        // Get current user
        $user = Auth::guard('admin')->user();

        if ($user) {
            // Update existing user
            $user->profile_image = $data['profile_image'];
            
            if ($user->save()) {
                return response()->json([
                    'status' => true, 
                    'message' => 'Profile image updated successfully.',
                    'file' => $data['profile_image']
                ]);
            }
        } else {
            // Insert new user (based on your code, but this is unusual in a profile update)
            if (Admin::insert($data)) {
                return response()->json([
                    'status' => true, 
                    'message' => 'Added successfully.',
                    'file' => $data['profile_image']
                ]);
            }
        }

        return response()->json([
            'status' => false, 
            'message' => 'Failed to update profile image. Please try again.'
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {

        if($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1'){
                return redirect()->back()->with('error', 'Profile updated but do not have access.');
                    }
        $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|max:255',
            // 'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::guard('admin')->user();
        
        $user->name = $request->name;
        // $user->email = $request->email;
        // $user->phone = $request->phone;
        
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        if($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1'){
                return redirect()->back()->with('error', 'Profile updated but do not have access.');
                    }
        $request->validate([
            'current_password' => 'required|current_password:admin',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }



}
