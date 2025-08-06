<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;

class UserController extends Controller
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
                View::share('accesss', $access_new[11]);
                $this->accesss = $access_new[11]; 

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

 public function index(Request $request)
        {

             if ($this->role === 'sub-admin'  && isset($this->accesss['view']) != '1') {
              return view('admin.noacesss');
             }
            $admin_id = auth('admin')->user()->id;
            if ($request->ajax()) {
                $user = Admin::where('id','!=',$admin_id)->where('role','!=','super-admin')->orderBy('id','DESC')->get();
                return DataTables::of($user)
                ->addIndexColumn()
                    
                ->addColumn('status', function ($row) {
               
                return '
                ' . (($this->role === 'sub-admin' && (!isset($this->accesss['status']) || $this->accesss['status'] != '1')) ? '' : '
                    <button type="button"
                        data-id="' . $row->id . '"
                        data-status="' . $row->status . '"
                        data-url="' . url('/admin/toggle-user-status/' . $row->id) . '"
                        class="btn btn-sm ' . ($row->status ? 'btn-success' : 'btn-secondary') . ' btn-toggle-status">
                        ' . ($row->status ? 'Active' : 'Inactive') . ' 
                        <i class="fa fa-' . ($row->status ? 'toggle-on' : 'toggle-off') . '"></i>
                    </button>
                ') . '';
            })
                
                    ->addColumn('action', function ($row) {
                      return'
                      ' . (($this->role === 'sub-admin' && (!isset($this->accesss['edit']) || $this->accesss['edit'] != '1')) ? '' : '
                        <a class="btn btn-sm btn-light" data-id="'.$row->id.'" href="' . url('/admin/user-edit/' . $row->id) . '" style="color: #775DA6;">
                        <i class="fa fa-edit"></i>
                        </a>
                      ') . '

                    ' . (($this->role === 'sub-admin' && (!isset($this->accesss['delete']) || $this->accesss['delete'] != '1')) ? '' : '
                            <button type="button" data-id="'.$row->id.'" data-url="' . url('/admin/delete-user/' . $row->id) . '"class="btn btn-sm btn-danger btn-delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
 
                        ') . ' 
                      
                      ';
                    })
                    ->rawColumns(['status','action'])
                    ->make(true);
            }
           
            return view('admin.user.list');
        }


      public function user_add(){
        if ($this->role === 'sub-admin'  && isset($this->accesss['add']) != '1') {
            return view('admin.noacesss');
        }
        $module = DB::table('module')->get();
        return view('admin.user.add',compact('module'));
      }  




     public function store(Request $request){
        if($this->role === 'sub-admin'  && isset($this->accesss['add']) != '1'){
                    return response()->json([
                        'status' => false,
                        'message' => 'User add but do not have access.'
                    ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Admin::class],
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:15', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
            
        $user_acess = json_encode($request->access, true);
            
        
        $data = array(
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_access' => $user_acess,
            'password' => Hash::make($request->password),
        );
         if(Admin::create($data)){
            return response()->json([
                'status' => true,
                'message' => 'User created successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }

     } 


     public function user_edit($id){
        if ($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1') {
            return view('admin.noacesss');
        }
         $user = Admin::findOrFail($id);
         $module = DB::table('module')->get();
        return view('admin.user.edit',compact('module','user'));
      }  
    

    public function update(Request $request){
         if($this->role === 'sub-admin'  && isset($this->accesss['edit']) != '1'){
                    return response()->json([
                        'status' => false,
                        'message' => 'User Update but do not have access..'
                    ]);
        }
         $id = $request->id;
         $user = Admin::findOrFail($id);
        $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => 'required|string|email|max:255|unique:admins,email,'.$id,
                'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:10|max:15|unique:admins,phone,'.$id,
              ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       if($request->password && $request->password_confirmation ){
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }

       } 
            
        $user_acess = json_encode($request->access, true);
            
        
        $data = array(
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_access' => $user_acess,
            'password' => Hash::make($request->password),
        );
         
        
        if($user->update($data)){
            return response()->json([
                'status' => true,
                'message' => 'Vehicle updated successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed and try again..'
            ]);
        }
    }


      public function destroy($id)
        {   
              if($this->role === 'sub-admin'  && isset($this->accesss['delete']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Delete Access']);
              }
              
            $user = Admin::find($id);

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.']);
            }

            $user->delete();

            return response()->json(['status' => true, 'message' => 'User deleted successfully.']);
        }

    
        public function toggleStatus(Request $request, $id)
        {
              if($this->role === 'sub-admin'  && isset($this->accesss['status']) != '1'){
                    return response()->json(['status' => false, 'message' => 'You have Not Status Access']);
              }
            $response=[];
                if($request->id){
                    $get_user = Admin::where('id',$id)->first();

                    if ($get_user->status == '1') {
                        $val = '0';
                    }else{
                        $val='1';
                    }

                    if(Admin::where('id', $id)->update(['status' => $val])){
                        $response=['status'=>true,'message'=>'Updated Successfully'];
                    }else{
                        $response=['status'=>false,'message'=>'Something went wrong'];
                    }
                }else{
                    $response=['status'=>false,'message'=>'Something went wrong'];
                }
                return response()->json($response);
           
        }



}
