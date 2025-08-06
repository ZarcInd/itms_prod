
$(document).ready(function() {
    // Get DOM elements
    const $importBtn = $('#btn-import');
    const $csvFileInput = $('#csv-file-input');
    const $importStatusModal = $('#import-status-modal');
    const $progressBar = $('#import-progress-bar');
    const $importStatus = $('#import-status');
    const $importResults = $('#import-results');

    // Initialize Bootstrap modal
    const statusModal = new bootstrap.Modal($importStatusModal[0]);

    // When import button is clicked, trigger file input click
    $importBtn.on('click', function() {
        $csvFileInput.click();
    });

    // When a file is selected
    $csvFileInput.on('change', function(event) {
        const file = event.target.files[0];

        if (!file) {
            return; // No file selected
        }

        // Validate file is a CSV
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            alert('Please select a valid CSV file.');
            return;
        }

        // Reset the progress and status
        $progressBar.css('width', '0%').removeClass('bg-danger').addClass('bg-primary');
        $importStatus.text('Please wait while your CSV file is being processed...');
        $importResults.html('');

        // Create form data for the AJAX request
        const formData = new FormData();
        formData.append('csv_file', file);

        // Show the status modal
        statusModal.show();

        // Simulate progress (for better UX)
        let progress = 0;
        const progressInterval = setInterval(function() {
            if (progress < 90) {
                progress += 5;
                $progressBar.css('width', progress + '%');
            }
        }, 300);

        // Get the URL from the button's data-url attribute
        const dataUrl = $importBtn.data('url');
        // Send AJAX request to the server
        $.ajax({
            url: dataUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                clearInterval(progressInterval);
            
                // Complete the progress bar
                $progressBar.css('width', '100%');
            
                // Display success message
                $importStatus.text('Import completed successfully!');
            
                // Display import results
                if (data.imported > 0) {
                    $importResults.append(`
                        <div class="alert alert-success">
                            <strong>Success!</strong> ${data.imported} vehicles imported successfully.
                        </div>
                    `);
                }
            
                if (data.skipped > 0) {
                    $importResults.append(`
                        <div class="alert alert-warning">
                            <strong>Notice:</strong> ${data.skipped} records were skipped.
                        </div>
                    `);
                }
            
                if (data.errors && data.errors.length > 0) {
                    let errorList = '<ul class="mb-0">';
                    data.errors.forEach(function(error) {
                        errorList += `<li>${error}</li>`;
                    });
                    errorList += '</ul>';
            
                    $importResults.append(`
                        <div class="alert alert-danger">
                            <strong>Errors:</strong> ${errorList}
                        </div>
                    `);
                }
            
                // Reset file input for future imports
                $csvFileInput.val('');
            
                // Optionally refresh the vehicles list if the function exists
                if (typeof refreshVehiclesList === 'function') {
                    refreshVehiclesList();
                }
            
                // Reload the DataTable after all results are processed
                $('#vehicles-table').DataTable().ajax.reload(null, false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                clearInterval(progressInterval);
            
                // Display error
                $progressBar.css('width', '100%').removeClass('bg-primary').addClass('bg-danger');
            
                $importStatus.text('Error importing CSV file.');
                $importResults.html(`
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${errorThrown}
                    </div>
                `);
            
                // Reset file input
                $csvFileInput.val('');
            }
            
        });
    });
});

// Wait for DOM to be fully loaded


document.addEventListener('DOMContentLoaded', function () {
    // Get references to DOM elements
    const addVehicleBtn = document.getElementById('btn-add');
    const vehicleModal = document.getElementById('vehicle-modal');
    const closeBtn = vehicleModal.querySelector('.btn-close');

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(vehicleModal);

    // Open modal when "Add Vehicle" button is clicked
    addVehicleBtn.addEventListener('click', function () {
        modal.show();
    });

    // Close modal when close button is clicked
    closeBtn.addEventListener('click', function () {
        modal.hide();
    });

    // Optional: Close modal when clicking outside the modal dialog
    vehicleModal.addEventListener('click', function (event) {
        if (event.target === vehicleModal) {
            modal.hide();
        }
    });
});



    // Fill modal inputs
    function vicaledit(id, el) {
        const modal = $('#edit_vehicle-modal');
        
        // Fill modal inputs
        modal.find('#vehicle_id').val($(el).data('id'));
        modal.find('#vehicle_no').val($(el).data('vehicle_no'));
        modal.find('#vehicle_code').val($(el).data('vehicle_code'));
        modal.find('#device_id').val($(el).data('device_id'));
        modal.find('#city').val($(el).data('city'));
        modal.find('#agency').val($(el).data('agency'));
        modal.find('#operator').val($(el).data('operator'));
        modal.find('#depot').val($(el).data('depot'));
        modal.find('#vehicle_type').val($(el).data('vehicle_type'));
        modal.find('#seating_capacity').val($(el).data('seating_capacity'));
        modal.find('#region').val($(el).data('region'));
        modal.find('#etim_frequency').val($(el).data('etim_frequency'));
        modal.find('#service_category').val($(el).data('service_category'));
        modal.find('#fuel_type').val($(el).data('fuel_type'));
        modal.find('#dispatch_type').val($(el).data('dispatch_type'));
        modal.find('#route_name').val($(el).data('route_name'));
        modal.find('#service_start_time').val($(el).data('service_start_time'));
        modal.find('#service_end_time').val($(el).data('service_end_time'));
        
        // Set toggle switches
        ['gst_on_ticket', 'surcharge_on_ticket', 'collection_on_etim', 'gps_from_etim', 'forward_to_shuttl'].forEach(id => {
            // Debug the actual value received
            // console.log(`${id} value:`, $(el).data(id), typeof $(el).data(id));
            setToggleCheckbox(id, $(el).data(id), modal);
        });
        
        // Show modal
        modal.modal('show');
    }

          let vehicleDataInterval = null; // For clearing interval
let refarsh_url = null; // For storing refresh URL

function vicalinf(vehicleId, button) {
    const vehicleData = button.dataset;

    // Populate the modal with vehicle information
    function populateVehicleModal(data) {
        document.getElementById('modal-ignition').textContent = data.ignition;
        document.getElementById('modal-driver_id').textContent = data.driver_id;
      document.getElementById('modal-vehicle_no').textContent = data.vehicle_no;
        document.getElementById('modal-date_time').textContent = data.date_time;
        document.getElementById('modal-gps').textContent = data.gps;
        document.getElementById('modal-lat').textContent = data.lat;
        document.getElementById('modal-lat_dir').textContent = data.lat_dir;
        document.getElementById('modal-lon').textContent = data.lon;
        document.getElementById('modal-lon_dir').textContent = data.lon_dir;
        document.getElementById('modal-route_no').textContent = data.route_no;
        document.getElementById('modal-speed_kmh').textContent = data.speed_kmh;
        document.getElementById('modal-odo_meter').textContent = data.odo_meter;
        document.getElementById('modal-live_address').textContent = data.live_address;
    }

    // Set refarsh_url from button data (passed initially)
    refarsh_url = vehicleData.refars_url;

    // Populate modal initially
    populateVehicleModal(vehicleData);

    // Show modal
    const modalElement = document.getElementById('show_vehicle-modal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    modalElement.addEventListener('shown.bs.modal', function () {
        // Create map container if not exist
        if (!document.getElementById('vehicle-location-map')) {
            const mapContainer = document.createElement('div');
            mapContainer.id = 'vehicle-location-map';
            mapContainer.style.height = '300px';
            mapContainer.style.width = '100%';
            mapContainer.style.marginTop = '20px';

            const mapTitle = document.createElement('h5');
            mapTitle.textContent = 'Vehicle Location';
            mapTitle.style.marginTop = '20px';

            const modalBody = document.querySelector('#show_vehicle-modal .modal-body');
            modalBody.appendChild(mapTitle);
            modalBody.appendChild(mapContainer);
        }

        // Initialize map with initial data
        initializeVehicleMap(vehicleData.lat, vehicleData.lon, vehicleData.lat_dir, vehicleData.lon_dir, vehicleData.loca_img);

        // Start refreshing data every 30 sec (only if not already started)
        if (!vehicleDataInterval && refarsh_url) {
            vehicleDataInterval = setInterval(function () {
                fetchVehicleData(function (newData) {
                    populateVehicleModal(newData);
                    initializeVehicleMap(newData.lat, newData.lon, newData.lat_dir, newData.lon_dir, newData.loca_img);
                });
            }, 60000); // 10 seconds
        }

    }, { once: true });

    // Stop refresh when modal closes
    modalElement.addEventListener('hidden.bs.modal', function () {
        if (vehicleDataInterval) {
            clearInterval(vehicleDataInterval);
            vehicleDataInterval = null;
        }
        refarsh_url = null; // Clear URL after modal close
    });
}

// Fetch latest vehicle data using refarsh_url
function fetchVehicleData(callback) {
    if (!refarsh_url) {
        console.error('Refresh URL is not set');
        return;
    }

    fetch(refarsh_url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            callback(data);
        })
        .catch(error => {
            console.error('Error fetching vehicle data:', error);
        });
}

    
    // Function to initialize map with vehicle location
     let vehicleMap; // make it global to reuse across modal opens

    function initializeVehicleMap(lat, lon, latDir, lonDir, loc_img) {
        const latitude = parseFloat(lat) * (latDir === 'S' ? -1 : 1);
        const longitude = parseFloat(lon) * (lonDir === 'W' ? -1 : 1);
        const loimgurl =  loc_img;
        // Check if map already exists to avoid duplication
        if (vehicleMap) {
            vehicleMap.setView([latitude, longitude], 15);
            if (vehicleMap.marker) {
                vehicleMap.marker.setLatLng([latitude, longitude]);
            } else {
                vehicleMap.marker = L.marker([latitude, longitude], {
                    icon: customVehicleIcon
                }).addTo(vehicleMap);
            }
            return;
        }

        vehicleMap = L.map('vehicle-location-map').setView([latitude, longitude], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(vehicleMap);

        // Use custom marker icon (your uploaded PNG)
        window.customVehicleIcon = L.icon({
            iconUrl: loimgurl, // replace with your real image path
            iconSize: [40, 40], // adjust size
            iconAnchor: [20, 40]
        });

        vehicleMap.marker = L.marker([latitude, longitude], {
            icon: customVehicleIcon
        }).addTo(vehicleMap);
    }

    

function setToggleCheckbox(id, value, container = $(document)) {
    const $checkbox = container.find('#' + id);
    
    // Enhanced check to handle more value formats, case-insensitive comparison
    const isChecked = value !== null && 
        (value === true || 
         value === 1 || 
         value === '1' ||
         (typeof value === 'string' && ['yes', 'true', 'on'].includes(value.toLowerCase())));
    
    $checkbox.prop('checked', isChecked).val(isChecked ? 'Yes' : 'No');
    $checkbox.closest('.toggle-container')
        .find('.toggle-value')
        .text(isChecked ? 'Yes' : 'No')
        .removeClass('on off')  // Remove both classes first
        .addClass(isChecked ? 'on' : 'off');
}


$(document).ready(function() {
    // Initialize toggle switches
    $('input[type="checkbox"]').on('change', function() {
        const $valueDisplay = $(this).closest('.toggle-container').find('.toggle-value');
        
        if ($(this).is(':checked')) {
            $(this).val('Yes');
            $valueDisplay.text('Yes').removeClass('off').addClass('on');
        } else {
            $(this).val('No');
            $valueDisplay.text('No').removeClass('on').addClass('off');
        }
      
    });
    
    // Example of how to get all values when submitting a form
    // You can add this to your form submit event
    function getAllToggleValues() {
        const values = {};
        $('input[type="checkbox"]').each(function() {
            values[$(this).attr('name')] = $(this).val();
        });
        return values;
    }
    
    // Example of how to programmatically set a toggle
    // For example, to turn on the first toggle:
    // $('#gst_on_ticket').prop('checked', true).trigger('change');
});

$(document).ready(function() {
    $('.form_submit').on('submit', function(event){
        event.preventDefault();

        $('.submit_button').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        var form_data = new FormData(this);
        var action_url = $(this).attr('action'); //  Get URL from form attribute

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            type: "POST",
            url: action_url, //  Use dynamic URL
            data: form_data,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == true) {
                    $('.submit_button').html('Submit').prop('disabled', false);
                    $('#ajax_message')
                        .addClass('alert-success')
                        .removeClass('alert-danger hidden')
                        .html('<li>' + data.message + '</li>');
                     // 👉 Close modal
                        $('#vehicle-modal').modal('hide');
                        $('#edit_vehicle-modal').modal('hide');
                        $('#add_form')[0].reset(); // 🔥 Clear form
                        $('input[type="checkbox"]').each(function () {
                            $(this).prop('checked', false).val('No'); // Uncheck and set value to "No"
                            const $valueDisplay = $(this).closest('.toggle-container').find('.toggle-value');
                            $valueDisplay.text('No').removeClass('on').addClass('off'); // Update label if present
                        });
                        //  Refresh DataTable
                        $('#vehicles-table').DataTable().ajax.reload(null, false); // false = stay on current page
                        alert(data.message);
                        
                    // Redirect on success
                } else {
                    $('#ajax_message')
                        .addClass('alert-danger')
                        .removeClass('alert-success hidden')
                        .html('<li>' + data.message + '</li>');
                         alert(data.message);
                }

                setTimeout(function() {
                    $('#ajax_message').addClass('hidden');
                }, 5000);

                $('.submit_button').html('Submit').prop('disabled', false);
            },
            error: function() {
                alert('Something went wrong...');
                $('.submit_button').html('Submit').prop('disabled', false);
            }
        });
    });
});

function resetFormAndCheckboxes() {
    $('#add_form')[0].reset();
    $('input[type="checkbox"]').each(function () {
        $(this).prop('checked', false).val('No');
        const $valueDisplay = $(this).closest('.toggle-container').find('.toggle-value');
        $valueDisplay.text('No').removeClass('on').addClass('off');
    });
}

// Use this when needed
resetFormAndCheckboxes();

// CSV Download Handler
$(document).ready(function() {
    $('#btn-export').on('click', function(e) {
        e.preventDefault();
        
      const status = $('#csv-file-input-filter').val();
        // Show loading state
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...');
        $btn.prop('disabled', true);
        const exportUrl = $btn.attr('href') + '?status=' + encodeURIComponent(status);
        // Make AJAX request to download CSV
        $.ajax({
            url: exportUrl,
            method: 'GET',
            xhrFields: {
                responseType: 'blob'
            },
            beforeSend: function() {
                $btn.prop('disabled', true);
                $btn.html('Exporting...'); // Optional loading state
            },
            success: function(blob) {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "vehicles.csv"; // Always CSV
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url); // Cleanup
                showNotification('CSV exported successfully!', 'success');
            },
            error: function(xhr) {
                console.error('Export failed:', xhr.responseText);
                showNotification('Failed to export CSV. Please try again.', 'error');
            },
            complete: function() {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
            }
        });
        
        
    });
    
    // Helper function to extract filename from headers
    function getFilenameFromHeader(xhr) {
        const disposition = xhr.getResponseHeader('Content-Disposition');
        if (disposition && disposition.includes('filename=')) {
            return disposition.split('filename=')[1].replace(/['"]/g, '');
        }
        return null;
    }
    

    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        let deleteUrl = $(this).data('url');
        if (confirm("Are you sure you want to delete this vehicle?")) {
            $.ajax({
                url: deleteUrl, // Or use a named route if needed
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        showNotification(response.message, 'success');
                        $('#vehicles-table').DataTable().ajax.reload(null, false); // 🔄 Refresh table
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function () {
                    showNotification('Something went wrong.', 'error');
                }
            });
        }
    });
    
    
    $(document).on('click', '.btn-toggle-status', function () {
        let id = $(this).data('id');
        let currentStatus = $(this).data('status'); // 1 or 0
        let newStatus = currentStatus == 1 ? 0 : 1;
    
        let toggleUrl = $(this).data('url');
         toggleLoader(true);
        $.ajax({
            url: toggleUrl,
            type: 'POST',
            data: {
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                status: newStatus
            },
            success: function(response) {
                if (response.status) {
                    showNotification(response.message, 'success');
                    $('#vehicles-table').DataTable().ajax.reload(null, false); // Reload table
                    toggleLoader(false); 
                } else {
                    showNotification('Status change failed.', 'error');
                    toggleLoader(false); 
                }
            },
            error: function() {
                showNotification('Error toggling status.', 'error');
                toggleLoader(false); 
            }
        });
    });
    
    
    // Helper function for notifications (implement as needed)
    function showNotification(message, type) {
        // You can replace this with your preferred notification system
        if (typeof toastr !== 'undefined') {
            // If you're using toastr
            toastr[type](message);
        } else {
            // Simple alert fallback
            alert(message);
        }
    }
}); 
