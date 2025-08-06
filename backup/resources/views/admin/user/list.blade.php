@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
    <h2 class="mb-4">Users</h2>
        <div class="card">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Users</h4>
                <div>
                 @if($role === 'sub-admin' && (!isset($accesss['add']) || $accesss['add'] != '1'))
                 
                  @else    
                <a href="{{ route('user.add') }}" class="btn btn-sm btn-light me-2" id="btn-exportcsv">
                     <i class="fas fa-plus"></i> Add User
                </a>
                @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="user-table">
                        <thead class="table-header">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

</main>

<script src="{{ asset('public/backend/js/user.js')}}"></script>
@endsection