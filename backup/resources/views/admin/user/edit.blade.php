@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="border-bottom: 2px solid #7e57c2;">
                        <h5 class="mb-0">Edit User</h5>
                        <span class="badge bg-light text-dark">Security</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.update') }}"  data-url="{{ route('user.list') }}" method="POST" class="form_submit">
                            @csrf
                            <input type="hidden" class="form-control" id="id" name="id" value="{{$user->id}}">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="email" name="email" value="{{$user->email}}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{$user->phone}}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%; background-color: #7e57c2;"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="password-strength"></div>
                                </div>
                                <small class="text-muted" id="password-strength-text">Password strength: Too weak</small>
                            </div>
                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                                    <div class="btn-group">
            <button type="button" class="btn btn-primary" id="checkAll">Check All</button>
            <button type="button" class="btn" id="uncheckAll">Uncheck All</button>
        </div>

        <!-- Column-specific Check/Uncheck buttons -->
        <div class="column-selector">
            <span>Select specific columns: </span>
            <button type="button" class="btn btn-primary" data-column="add">Add</button>
            <button type="button" class="btn btn-primary" data-column="view">View</button>
            <button type="button" class="btn btn-primary" data-column="edit">Update</button>
            <button type="button" class="btn btn-primary" data-column="data_view">Data View</button>
            <button type="button" class="btn btn-primary" data-column="status">Status</button>
            <button type="button" class="btn btn-primary" data-column="delete">Delete</button>
            <button type="button" class="btn btn-primary" data-column="download">Download</button>
            <button type="button" class="btn btn-primary" data-column="import">Import</button>
            <button type="button" class="btn btn-primary" data-column="export">Export</button>
        </div>

        <!-- Permission Table -->
                                <div class="table-responsive mt-4" id="access">
                                    <table class="table text-center" id="user_access">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Add</th>
                                                <th>View</th>
                                                <th>Update</th>
                                                <th>Data View</th>
                                                <th>Status</th>
                                                <th>Delete</th>
                                                <th>Download</th>
                                                <th>Import</th>
                                                <th>Export</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                           @php
                                                        $user_acc = json_decode($user->user_access, true);
                                                    @endphp
                                                    @foreach($module as $key => $permission)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="access[{{ $permission->id }}][m_id]" value="{{ $permission->id }}">
                                                            <input type="hidden" name="access[{{ $permission->id }}][m_idd]" value="{{ $permission->id }}">
                                                            <input type="hidden" name="access[{{ $permission->id }}][m_name]" value="{{ $permission->name }}">
                                                            {{ ucfirst(str_replace("_"," ",$permission->name)) }}
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][add]" class="permission-checkbox add-checkbox"
                                                                {{ isset($user_acc[$permission->id]['add']) && $user_acc[$permission->id]['add'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][view]" class="permission-checkbox view-checkbox"
                                                                {{ isset($user_acc[$permission->id]['view']) && $user_acc[$permission->id]['view'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][edit]" class="permission-checkbox edit-checkbox"
                                                                {{ isset($user_acc[$permission->id]['edit']) && $user_acc[$permission->id]['edit'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][data_view]" class="permission-checkbox data_view-checkbox"
                                                                {{ isset($user_acc[$permission->id]['data_view']) && $user_acc[$permission->id]['data_view'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][status]" class="permission-checkbox status-checkbox"
                                                                {{ isset($user_acc[$permission->id]['status']) && $user_acc[$permission->id]['status'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][delete]" class="permission-checkbox delete-checkbox"
                                                                {{ isset($user_acc[$permission->id]['delete']) && $user_acc[$permission->id]['delete'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][download]" class="permission-checkbox download-checkbox"
                                                                {{ isset($user_acc[$permission->id]['download']) && $user_acc[$permission->id]['download'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][import]" class="permission-checkbox import-checkbox"
                                                                {{ isset($user_acc[$permission->id]['import']) && $user_acc[$permission->id]['import'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" value="1" name="access[{{ $permission->id }}][export]" class="permission-checkbox export-checkbox"
                                                                {{ isset($user_acc[$permission->id]['export']) && $user_acc[$permission->id]['export'] == '1' ? 'checked' : '' }}>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg submit_button" style="background-color: #7e57c2; color: white;">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
   <script src="{{ asset('public/backend/js/user.js')}}"></script>
@endsection