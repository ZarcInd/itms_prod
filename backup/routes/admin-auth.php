<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\VehiclesController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\MessagesController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\UserController;

use Illuminate\Support\Facades\Route;


Route::prefix('admin')->middleware('guest:admin')->group(function () {

     //Route::get('register', [RegisterController::class, 'create'])->name('admin.register');
     //Route::post('register', [RegisterController::class, 'store']);
    Route::get('/', function () {return redirect()->route('admin_login');});
    Route::get('login', [LoginController::class, 'create'])->name('admin_login');
    Route::post('login', [LoginController::class, 'store'])->name('admin_post_login');
    Route::get('forgot-password/{email?}', [LoginController::class, 'forget_view']);
    Route::post('forgot-password/{email?}', [LoginController::class, 'forget_send_otp_email']);
    Route::post('send-otp', [LoginController::class, 'send_otp'])->name('admin.send_otp');
    Route::post('create_new_password', [LoginController::class, 'create_new_password'])->name('create_new_password');


});

Route::prefix('admin')->middleware(['admin.2fa.complete'])->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'verify'])->name('admin.2fa.verify');
    Route::post('/2fa/validate', [TwoFactorController::class, 'validateCode'])->name('admin.2fa.validate');
});


Route::prefix('admin')->middleware(['admin.cookie.auth'])->group(function () {
    

     Route::get('/profile/2fa/setup', [TwoFactorController::class, 'setup'])->name('admin.2fa.setup');
    Route::post('/profile/2fa/enable', [TwoFactorController::class, 'enable'])->name('admin.2fa.enable');
    Route::post('/profile/2fa/disable', [TwoFactorController::class, 'disable'])->name('admin.2fa.disable');

    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
    Route::post('/profile/update-image', [ProfileController::class, 'updateProfileImage'])->name('admin.profile.update-image');
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/data', [LogController::class, 'getData'])->name('logs.data');
    
    Route::get( '/live-tracking', [AdminController::class, 'live_tracking'])->name('admin.live-tracking');

            // Vehicle routes
        Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/data', [VehiclesController::class, 'getVehicles'])->name('vehicles.data');
        Route::post('/vehicles', [VehiclesController::class, 'store'])->name('vehicles.store');
        Route::post('/vehicles-edit', [VehiclesController::class, 'update'])->name('vehicles.update');
        Route::delete('/delete-vehicle/{id}', [VehiclesController::class, 'destroy'])->name('vehicle.destroy');
        Route::post('/toggle-vehicle-status/{id}', [VehiclesController::class, 'toggleStatus']);


        // CSV Import/Export
        Route::post('/vehicles/import', [VehiclesController::class, 'import'])->name('vehicles.import');
        Route::get('/vehicles/export/csv', [VehiclesController::class, 'export'])->name('vehiclescsv.export');
        Route::get('/vehicle/{id}', [VehiclesController::class, 'vicaldata']);
        Route::get('/vehicles/export', [VehiclesController::class, 'vichail_dowllound'])->name('vehicles.export');
       
         // DEvices routes
         Route::get('/device', [DeviceController::class, 'index'])->name('device.index');
         Route::get('/device/data', [DeviceController::class, 'getDevice'])->name('device.data');
         Route::post('/device', [DeviceController::class, 'store'])->name('device.store');
         Route::post('/device-edit', [DeviceController::class, 'update'])->name('device.update');
         Route::delete('/delete-device/{id}', [DeviceController::class, 'destroy'])->name('device.destroy');
         Route::post('/toggle-device-status/{id}', [DeviceController::class, 'toggleStatus']);
         Route::get('/device_status/{id}', [DeviceController::class, 'devicedata']);
        
        
         // CSV Import/Export
         Route::post('/device/import', [DeviceController::class, 'import'])->name('device.import');
         Route::get('/device/export/csv', [DeviceController::class, 'export'])->name('devicecsv.export');
         Route::get('/device/export', function () {
             $devicefilePath = public_path('exports/device.csv');
              if (file_exists($devicefilePath)) {
                  return response()->download($devicefilePath, 'device.csv', [
                     'Content-Type' => 'text/csv',
                  ]);
              } else {
                  abort(404, 'File not found.');
              }
              })->name('device.export');
        
        Route::get('/route-map',[RouteController::class, 'index'])->name('route.map');
        Route::get('/user-massage',[MessagesController::class, 'index'])->name('admin.messages');
    
        // add sub-admin
        Route::get('/user-list',[UserController::class,'index'])->name('user.list');
        Route::get('/user-add',[UserController::class,'user_add'])->name('user.add');
        Route::post('/user',[UserController::class,'store'])->name('store.user');
        Route::get('/user-edit/{id}',[UserController::class,'user_edit'])->name('user.edit');
        Route::post('/user-update',[UserController::class,'update'])->name('user.update');
        Route::post('/toggle-user-status/{id}', [UserController::class, 'toggleStatus']);
        Route::delete('/delete-user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/device-data-count',[DeviceController::class,'device_get_data'])->name('device.count');
        Route::get('/vehicle-data-count',[VehiclesController::class,'vehicle_get_data'])->name('vehicle.count');
        Route::get('/raw-data',[DeviceController::class,'row_data'])->name('rawdata');
        Route::get('/raw-data-get',[DeviceController::class,'raw_data_get'])->name('rawdata.index');
        Route::get('/download-communicating', [DeviceController::class, 'download_communicating_device_data'])->name('download.communicating');
        Route::post('/download-communicating-download', [DeviceController::class, 'download_communicating_downloadCsv'])->name('download.communicating.csv');
        Route::post('/check-status', [DeviceController::class, 'checkDownloadStatus'])->name('check-status');
        Route::post('/rowdata-export', [DeviceController::class, 'rowdata_export'])->name('rowdata.export');
        Route::get('/user-row-data-list', [DeviceController::class, 'user_rowdata_list'])->name('userrowlist');
        Route::post('/rowdata/export', [DeviceController::class, 'roedataCsvExport'])->name('rowdata.export');
        // Add new route for checking status
        Route::get('/rawdata/export/status/{id}', [DeviceController::class, 'checkExportStatus'])->name('rawdata.export.status');
            

});

