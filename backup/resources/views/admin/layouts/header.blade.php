<nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link d-lg-none" id="toggle-sidebar" style="font-size: 25px; color: #898989;">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-lg-none" href="#">Prime Edge</a>
            <!-- Search Form -->
          

            <form class="search-form position-relative d-none d-md-block" >
                <i class="bi bi-search search-icon"></i>
                 <input id="searchInput" class="form-control rounded-pill" type="search" placeholder="Search..." aria-label="Search">
                 <ul id="searchResults" class="list-group position-absolute w-100" style="z-index: 999;"></ul>
            </form>
                    <div class="datetime-container">
                      <div class="datetime-display">
                           <i class="bi bi-clock-fill"></i>
                          <span id="current-time">Loading...</span>
                          <span class="running-indicator"></span>
                      </div>
                  </div>
            <!-- Right side navbar items -->
            <div class="ms-auto d-flex align-items-center">
                <div class="nav-item px-2">
                    <a href="#" class="nav-link">
                    <i class="bi bi-question-circle-fill nav-icon"></i>
                    </a>
                </div>
                <div class="nav-item px-2">
                    <a href="#" class="nav-link">
                        <i class="bi bi-bell-fill nav-icon">
                            <span class="notification-badge"></span>
                        </i>
                    </a>
                </div>
                <div class="nav-item px-2 ms-2">
                    <div class="d-flex align-items-center dropdown-toggle" id="userDropdown">
                       @php
                        $user = Auth::guard('admin')->user();
                       @endphp 

                      
                    @if($user->profile_image && file_exists(public_path($user->profile_image)))
                         <img src="{{url('public/'.$user->profile_image)}}"  alt="User" class="user-avatar">
                        @else
                        <img src="{{url('public/1142545574.png')}}"  alt="User" class="user-avatar">
                        @endif
                        <div class="ms-2 d-none d-lg-block">
                            <div class="fw-semibold small">{{$user->name}}</div>
                            <div class="text-muted small">Admin</div>
                        </div>
                    </div>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <!-- Profile Item -->
                        @if($subadmin_role_t === 'sub-admin'  && isset($subadmin_acess[12]['view']) != '1')
                        @else 
                        <a href="{{ route('admin.profile') }}" class="dropdown-item">
                          <i class="fa-solid fa-user-pen navs-link"></i> Update Profile
                        </a>
                        @endif
                        <!-- Logout Item -->
                        <a href="#" class="dropdown-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <i class="fa-solid fa-right-from-bracket navs-link"></i> <button type="submit" class="w-full text-left">
                                     Logout
                                </button>
                            </form>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>