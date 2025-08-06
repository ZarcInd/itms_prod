<div class="sidebar">
    <div class="sidebar-header">
        <div class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">Prime Edge</div>
      </div>      
       
        <ul class="nav flex-column" style="overflow-y: scroll; height: 90vh; display: block;">
            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[1]['view']) != '1')
            
            @else
             <li class="nav-item d-none">
                <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-columns"></i> Overview
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[2]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link {{ request()->is('admin/live-tracking*') ? 'active' : '' }}" href="{{ route('admin.live-tracking') }}">
                <i class="fa-solid fa-location-dot"></i>Live tracking
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[3]['view']) != '1')
            @else
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/vehicles*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                <i class="bi bi-bus-front-fill"></i>Vechicles
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[4]['view']) != '1')
            @else
           <li class="nav-item">
                <a class="nav-link settings-toggle" href="javascript:void(0)">
                    <i class="bi bi-device-ssd"></i>Device Monitoring
                    <i class="fa-solid fa-chevron-down arrow-icon @if(Request::is(['admin/device*', 'admin/download-communicating*', 'admin/raw-data*'])) rotated @endif"></i>
                </a>
                <ul class="submenu @if(Request::is(['admin/device*', 'admin/download-communicating*', 'admin/raw-data*'])) show @endif settings-submenu" id="">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/device*') ? 'active' : '' }}" href="{{ route('device.index') }}">
                            <i class="bi bi-device-ssd"></i>Device
                        </a>
                    </li>
                  
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/download-communicating*') ? 'active' : '' }} " href="{{ route('download.communicating') }}" >
                            <i class="fa-solid fa-download"></i>Download Communicating device Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/raw-data*') ? 'active' : '' }}" href="{{ route('rawdata') }}">
                            <i class="fa-solid fa-download"></i>Download Raw Data
                        </a>
                    </li>
                </ul>
                
            </li>
            @endif
            
            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[5]['view']) != '1')
            @else
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/route-map*') ? 'active' : '' }}" href="{{ route('route.map') }}">
                    <i class="bi bi-arrow-repeat"></i> Route replay
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[6]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link" href="#">
                    <i class="bi bi-heart-pulse"></i> Health
                </a>
            </li>
            @endif
           
            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[7]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link {{ request()->is('admin/logs*') ? 'active' : '' }}" href="{{ Route('logs.index') }}">
                <i class="fa-solid fa-desktop"></i> Logs
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[8]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link {{ request()->is('admin/user-massage*') ? 'active' : '' }}" href="{{ Route('admin.messages') }}">
                <i class="bi bi-chat-text"></i> Message
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[9]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link" href="#">
                <i class="bi bi-file-text-fill"></i> Reports
                </a>
            </li>
            @endif

            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[10]['view']) != '1')
            @else
            <li class="nav-item d-none">
                <a class="nav-link" href="#">
                <i class="bi bi-camera-video-fill"></i> Video
                </a>
            </li>
            @endif


            @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[11]['view']) != '1')
            @else
            <li class="nav-item">
                <a class="nav-link settings-toggle" href="javascript:void(0)">
                    <i class="fa-solid fa-gear"></i> Settings
                    <i class="fa-solid fa-chevron-down  arrow-icon @if(Request::is(['admin/user-list*'])) rotated @endif"></i>
                </a>
                <ul class="submenu @if(Request::is(['admin/user-list*'])) show @endif settings-submenu" id="">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/user-list*') ? 'active' : '' }}" href="{{route('user.list') }}">
                            <i class="fa-solid fa-user"></i> Users
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Other navigation items could go here -->
        </ul>

    </div>