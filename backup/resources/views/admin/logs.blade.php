@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
        <div class="container-fluid py-4">
            <h1 class="mb-4">Logs</h1>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4 d-flex justify-content-end">
                            <div class="filters d-flex gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="groupDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Group <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="groupDropdown">
                                        <li><a class="dropdown-item" href="#">All Groups</a></li>
                                        <li><a class="dropdown-item" href="#">Group 1</a></li>
                                        <li><a class="dropdown-item" href="#">Group 2</a></li>
                                    </ul>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="vehicleDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Vehicle <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="vehicleDropdown">
                                        <li><a class="dropdown-item" href="#">All Vehicles</a></li>
                                        <li><a class="dropdown-item" href="#">Vehicle 1</a></li>
                                        <li><a class="dropdown-item" href="#">Vehicle 2</a></li>
                                    </ul>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="dateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Date <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dateDropdown">
                                        <li><a class="dropdown-item" href="#">All Dates</a></li>
                                        <li><a class="dropdown-item" href="#">Today</a></li>
                                        <li><a class="dropdown-item" href="#">Yesterday</a></li>
                                    </ul>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="startDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Start <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="startDropdown">
                                        <li><a class="dropdown-item" href="#">Any Start</a></li>
                                        <li><a class="dropdown-item" href="#">Morning</a></li>
                                        <li><a class="dropdown-item" href="#">Afternoon</a></li>
                                    </ul>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle" type="button" id="endDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        End <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="endDropdown">
                                        <li><a class="dropdown-item" href="#">Any End</a></li>
                                        <li><a class="dropdown-item" href="#">Morning</a></li>
                                        <li><a class="dropdown-item" href="#">Afternoon</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="logs-table" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Tracking raw data</th>
                                        <th>Health raw data</th>
                                        <th>Alert raw data</th>
                                        <th>Login raw data</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button id="download-logs" class="btn btn-primary">Download logs</button>
                            <div class="pagination-info">
                                <span>Page 1 of 10</span>
                            </div>
                            <div class="pagination-controls">
                                <button class="btn btn-light me-2">Previous</button>
                                <button class="btn btn-light">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    
</main>
<script>
    $(document).ready(function() {
        $('#logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("logs.data") }}',
            columns: [
                { data: 'vehicle', name: 'vehicle' },
                { data: 'tracking_raw_data', name: 'tracking_raw_data' },
                { data: 'health_raw_data', name: 'health_raw_data' },
                { data: 'alert_raw_data', name: 'alert_raw_data' },
                { data: 'login_raw_data', name: 'login_raw_data' },
            ],
            paging: true,
            lengthChange: false,
            searching: false,
            ordering: false,
            info: false,
            autoWidth: false,
            pageLength: 9,
            dom: 't', // Only show table, remove default controls
            drawCallback: function(settings) {
                $('.pagination-info').html('Page ' + (settings._iDisplayStart / settings._iDisplayLength + 1) + ' of ' + Math.ceil(settings.fnRecordsTotal() / settings._iDisplayLength));
            }
        });

        // Custom pagination buttons
        $('.pagination-controls .btn:contains("Previous")').on('click', function() {
            $('#logs-table').DataTable().page('previous').draw('page');
        });

        $('.pagination-controls .btn:contains("Next")').on('click', function() {
            $('#logs-table').DataTable().page('next').draw('page');
        });

        // Filter controls
        $('.dropdown-item').on('click', function(e) {
            e.preventDefault();
            const filter = $(this).closest('.dropdown').find('.dropdown-toggle').text().trim().toLowerCase();
            const value = $(this).text();
            
            // Update button text
            $(this).closest('.dropdown').find('.dropdown-toggle').text(value + ' ');
            $(this).closest('.dropdown').find('.dropdown-toggle').append('<i class="fa fa-chevron-down"></i>');
            
            // Apply filter
            const table = $('#logs-table').DataTable();
            table.ajax.reload();
        });

        // Download logs button
        $('#download-logs').on('click', function() {
            alert('Download logs functionality would be implemented here');
        });
    });
</script>

@endsection