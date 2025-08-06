<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Prime Edge - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- jQuery -->
    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.min.css">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.3/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://kit.fontawesome.com/68cf4124e0.js" crossorigin="anonymous"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('public/backend/css/style.css')}}">
    @include('sweetalert::alert')
    <style>
         #map {

            height: 400px;

            }
        .custom-toast {
            background-color: #6c5ce7 !important;
            color: white !important;
        }
        .white-toast {
            background-color: white !important;
            color: #333 !important;
            border: 1px solid #ddd;
        }

        bg-primary-custom {
            background-color: var(--primary-color) !important;
        }
        
        .text-primary-custom {
            color: var(--primary-color) !important;
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-color-dark);
            border-color: var(--primary-color-dark);
            color: white;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .table-header {
            background-color: var(--primary-color);
            color: white;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: var(--primary-color-light) !important;
            border-color: var(--primary-color-light) !important;
            color: white !important;
        }
         
         .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
          background-color: #775DA6 !important;
          border: none;
          border-right: 1px solid #aaa;
          border-top-left-radius: 4px;
          border-bottom-left-radius: 4px;
          color: #fff !important;
          cursor: pointer;
          font-size: 1em;
          font-weight: bold;
          padding: 0 4px;
          position: absolute;
          left: 0;
          top: 0;
      }
      
      .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #775DA6 !important;
        border: 1px solid #775DA6;
        border-radius: 4px;
        box-sizing: border-box;
        display: inline-block;
        margin-left: 5px;
        margin-top: 5px;
        padding: 0;
        padding-left: 20px;
        position: relative;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: bottom;
        white-space: nowrap;
    }
      
      .datetime-container {
            background: #775DA6;
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 5px 10px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(119, 93, 166, 0.3);
            border: 1px solid rgba(119, 93, 166, 0.3);
        }

        .datetime-display {
            color: white;
            font-size: 19px;
            font-weight: 700;
            text-shadow: 0 0 15px rgba(119, 93, 166, 0.8);
            letter-spacing: 1px;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 15px rgba(119, 93, 166, 0.8); }
            to { text-shadow: 0 0 25px rgba(119, 93, 166, 1); }
        }

        .running-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #25f223;
            border-radius: 50%;
            margin-left: 10px;
            animation: blink 1s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(119, 93, 166, 0.8);
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
      
        .pagination {
           --bs-pagination-active-bg: #775DA6 !important;
          --bs-pagination-color: #775DA6 !important;
           --bs-pagination-active-border-color: #775DA6 !important;
      } 
      
      
      

    </style>

</head>
<body>
    <div class="global-loader">
    <div class="spinner-container">
        <div class="spinner"></div>
    </div>
</div>
     @php 
             
              
              $search = [
                  'vehicles' => 'Vehicles',
                  'device' => 'Device',
                  'route_replay' => 'Route Replay',
                  'rawdata'=>'Rawdata',
                  'download_communicating'=>'Download communicating',
                  'users' => 'Users',
                  'update_profile' => 'Update Profile',
              ];
          @endphp
     @php
                            $user_t = auth('admin')->user();
                            $user_role_t = $user_t->role;
                            $accesss_t = json_decode($user_t->user_access, true);
                                $access_new_t = array();
                                if ($accesss_t != null) {
                                    foreach ($accesss_t as $a) {
                                        $access_new_t[$a['m_id']] = $a;
                                    }
                                } else {
                                    $access_new_t = $accesss_t;
                                }
                            if($user_role_t === 'sub-admin'){
                                    $subadmin_acess = $access_new_t; 
                                    $subadmin_role_t = $user_role_t; 
                                }else{
                                    $subadmin_acess = ''; 
                                    $subadmin_role_t = ''; 
                                }
                        @endphp
    <!-- Navbar -->
    @include('admin/layouts.header') 

    <!-- Sidebar -->
    @include('admin/layouts.sidebar')

    <!-- Main Content -->
    @yield('container')
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <!-- DataTables -->
   
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/backend/js/script.js')}}"></script>
    
    <script>
      function updateDateTime() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const formattedDateTime = `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
            document.getElementById('current-time').textContent = formattedDateTime;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

       // Function to show/hide loader
   
    function toggleLoader(show) {
        if (show) {
            $('.global-loader').fadeIn(300);
        } else {
            $('.global-loader').fadeOut(300);
        }
    }
      
    document.addEventListener('DOMContentLoaded', function () {
    const searchItems = @json($search);
    const input = document.getElementById('searchInput');
    const results = document.getElementById('searchResults');
    const form = document.getElementById('searchForm');
    
      function performAjaxSearch(queryKey) {
        if (!queryKey) return;
        toggleLoader(true);

        switch (queryKey) {
            //case 'overview':
            //    window.location.href = "{{ route('admin.dashboard') }}";
             //   break;
           // case 'live_tracking':
           //     window.location.href = "{{ route('admin.live-tracking') }}";
           //     break;
            case 'vehicles':
                window.location.href = "{{ route('vehicles.index') }}";
                break;
            case 'device':
                window.location.href = "{{ route('device.index') }}";
                break;
            case 'route_replay':
                window.location.href = "{{ route('route.map') }}";
                break;
           // case 'health':
             //   window.location.href = "{{ route('admin.dashboard') }}";
              //  break;
           // case 'logs':
             //   window.location.href = "{{ route('logs.index') }}";
               // break;
           // case 'message':
            //    window.location.href = "{{ route('admin.messages') }}";
            //    break;
           // case 'report':
            //    window.location.href = "{{ route('admin.dashboard') }}";
          //      break;
            // case 'video':
             //   window.location.href = "{{ route('admin.dashboard') }}";
             //   break;
            case 'download_communicating':
                window.location.href = "{{ route('download.communicating') }}";
                break;
             case 'rawdata':
                window.location.href = "{{ route('rawdata.index') }}";
                break;
                 case 'users':
                window.location.href = "{{ route('user.list') }}";
                break;
            case 'update_profile':
                window.location.href = "{{ route('admin.profile') }}";
                break;
            default:
                alert("Invalid search term.");
                break;
        }
    }

    // Live suggestions
    input.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        results.innerHTML = '';

        if (query.length === 0) return;

        Object.entries(searchItems).forEach(([key, value]) => {
            if (value.toLowerCase().includes(query)) {
                const li = document.createElement('li');
                li.className = 'list-group-item list-group-item-action';
                li.textContent = value;
                li.setAttribute('data-key', key);
                li.onclick = () => {
                    $('#searchInput').val(value);
                    performAjaxSearch(key);
                };
                results.appendChild(li);
            }
        });
    });

    // Press Enter
    $('#searchInput').on('keyup', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let query = $(this).val();
            let key = Object.keys(searchItems).find(k => searchItems[k].toLowerCase() === query.toLowerCase());

            if (key) {
                performAjaxSearch(key);
            } else {
                alert("No match found.");
            }
        }
    });

      // Form submit
     

   
   
      document.addEventListener('click', function (e) {
          if (!input.contains(e.target) && !results.contains(e.target)) {
              results.innerHTML = '';
          }
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        }
        
        // Custom styling for purple toasts
        toastr.options.styleCustom = {
            'purple': {
                'css': {
                    'background-color': '#6c5ce7',
                    'color': 'white'
                }
            },
            'white': {
                'css': {
                    'background-color': 'white',
                    'color': '#333',
                    'border': '1px solid #ddd'
                }
            }
        };

        @if(session('success_purple'))
            var toast = toastr.success("{{ session('success_purple') }}");
            $(toast.el).css(toastr.options.styleCustom.purple.css);
        @endif
        
        @if(session('success_white'))
            var toast = toastr.success("{{ session('success_white') }}");
            $(toast.el).css(toastr.options.styleCustom.white.css);
        @endif
    });

    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


         document.addEventListener('DOMContentLoaded', function () {
            const toggleButtons = document.querySelectorAll('.settings-toggle');
        
            toggleButtons.forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
        
                    // Get the corresponding submenu and arrow icon
                    const submenu = this.nextElementSibling;
                    const arrowIcon = this.querySelector('.arrow-icon');
        
                    if (submenu && submenu.classList.contains('settings-submenu')) {
                        submenu.classList.toggle('show');
                        arrowIcon.classList.toggle('rotated');
                    }
                });
            });
        });


</script>
</body>
</html>