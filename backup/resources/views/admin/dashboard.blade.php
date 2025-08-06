@extends('admin/layouts/master')
@section('container')
<main class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Overview</h2>
            
            <!-- Stats Cards -->
            <div class="row">
               <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h4 class="display-6 fw-bold">80</h4>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                        Total vehicles
                                     </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold">40</h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot green-dot"></div>
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                             Online vehicles
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h3 class="display-6 fw-bold">40</h3>
                                        <div class="text-muted" style="color: #898989 !important;font-size: larger;">
                                        <div id="statusDot" class="status-dot red-dot"></div>
                                        <svg class="bus-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4,16c0,0.88 0.39,1.67 1,2.22V20c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1h8v1c0,0.55 0.45,1 1,1h1c0.55,0 1,-0.45 1,-1v-1.78c0.61,-0.55 1,-1.34 1,-2.22V6c0,-3.5 -3.58,-4 -8,-4s-8,0.5 -8,4v10zM7.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5S6.67,14 7.5,14s1.5,0.67 1.5,1.5S8.33,17 7.5,17zM16.5,17c-0.83,0 -1.5,-0.67 -1.5,-1.5s0.67,-1.5 1.5,-1.5 1.5,0.67 1.5,1.5 -0.67,1.5 -1.5,1.5zM18,11H6V6h12v5z"/>
                                        </svg>
                                             Offline vehicles
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body p-0">
                            <div id="maps" class="map-container"></div>
                        </div>
                    </div>
               </div>
               <div class="col-lg-4 col-md-4 col-sm-12">
                  <!-- Activity and Messages -->
                <div class="row">
                    <!-- User Activity -->
                    <div class="col-lg-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">User activity</h5>
                                <button class="btn btn-sm btn-link text-muted p-0">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                    <li class="list-group-item text-muted">Lorem ipsum dolor sit amet</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages -->
                    <div class="col-lg-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Messages</h5>
                                <button class="btn btn-sm btn-link text-muted p-0">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="message-item d-flex align-items-center">
                                    <img src="/api/placeholder/40/40" alt="User" class="rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Albert Flores</div>
                                        <div class="small text-muted">Lorem ipsum dolor sit amet</div>
                                    </div>
                                    <div class="message-status"></div>
                                </div>
                                <div class="message-item d-flex align-items-center">
                                    <img src="/api/placeholder/40/40" alt="User" class="rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Leslie Alexander</div>
                                        <div class="small text-muted">Lorem ipsum dolor sit amet</div>
                                    </div>
                                    <div class="message-status"></div>
                                </div>
                                <div class="message-item d-flex align-items-center">
                                    <img src="/api/placeholder/40/40" alt="User" class="rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Devon Lane</div>
                                        <div class="small text-muted">Lorem ipsum dolor sit amet</div>
                                    </div>
                                    <div class="message-status"></div>
                                </div>
                                <div class="message-item d-flex align-items-center">
                                    <img src="/api/placeholder/40/40" alt="User" class="rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Eleanor Cooper</div>
                                        <div class="small text-muted">Lorem ipsum dolor sit amet</div>
                                    </div>
                                    <div class="message-status"></div>
                                </div>
                                <div class="message-item d-flex align-items-center">
                                    <img src="/api/placeholder/40/40" alt="User" class="rounded-circle me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Esther Howard</div>
                                        <div class="small text-muted">Lorem ipsum dolor sit amet</div>
                                    </div>
                                    <div class="message-status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               </div>
            </div>
            <!-- Map -->
            
            
            
        </div>
    </main>
    @endsection    