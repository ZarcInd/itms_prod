
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
                $('#device-table').DataTable().ajax.reload(null, false);
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
    const deviceModal = document.getElementById('device-modal');
    const closeBtn = deviceModal.querySelector('.btn-close');

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(deviceModal);

    // Open modal when "Add Vehicle" button is clicked
    addVehicleBtn.addEventListener('click', function () {
        modal.show();
    });

    // Close modal when close button is clicked
    closeBtn.addEventListener('click', function () {
        modal.hide();
    });

    // Optional: Close modal when clicking outside the modal dialog
    deviceModal.addEventListener('click', function (event) {
        if (event.target === deviceModal) {
            modal.hide();
        }
    });
});

    
function formatDateTime(dateStr) {
    if (!dateStr) return '';
    // Split input format: "DD-MM-YYYY HH:mm:ss"
    const [datePart, timePart] = dateStr.split(' ');
    const [day, month, year] = datePart.split('-');
    const [hours, minutes] = timePart.split(':');
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}T${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
}



    // Fill modal inputs
    function devicedit(id, el) {
        const modal = $('#edit_device-modal');
        let status = $(el).data('status');
        // Fill modal inputs
        modal.find('#id').val($(el).data('id'));
        modal.find('#device_id').val($(el).data('device_id'));
        modal.find('#vehicle_no').val($(el).data('vehicle_no'));
        modal.find('#agency').val($(el).data('agency'));
        modal.find('#depot').val($(el).data('depot'));
        modal.find('#protocol').val($(el).data('protocol'));
        modal.find('#region_id').val($(el).data('region_id'));
        // Show modal
        modal.modal('show');
    }

            let deviceDataInterval = null;  // For clearing interval
          let deviceRefarshUrl = null;    // For storing refresh URL

          function deviceinf(deviceId, button) {
              const devicedata = button.dataset;

              // Store refresh URL (only once)
              deviceRefarshUrl = devicedata.refars_url;

              // Function to populate modal
              function populateDeviceModal(data) {
                  document.getElementById('modal-device_id_m').textContent = data.device_id;
                  document.getElementById('modal-vehicle_no_m').textContent = data.vehicle_no;
                  document.getElementById('modal-protocol_m').textContent = data.protocol;
                  document.getElementById('modal-lat_m').textContent = data.lat;
                  document.getElementById('modal-lon_m').textContent = data.lon;
                  document.getElementById('modal-time_in_packet_m').textContent = data.time_in_packt;
                  document.getElementById('modal-natwork_m').textContent = data.natwork;
                  document.getElementById('modal-packet_status_m').textContent = data.packet_status;
                  document.getElementById('modal-gps_signal_m').textContent = data.gps_signal;
              }

              // Initial population
              populateDeviceModal(devicedata);

              // Open modal
              const modalElement = document.getElementById('show_device-modal');
              const modal = new bootstrap.Modal(modalElement);
              modal.show();

              // After modal shown, start interval refresh
              modalElement.addEventListener('shown.bs.modal', function () {
                  // Start refreshing every 30 seconds
                  deviceDataInterval = setInterval(function () {
                      fetchDeviceData(function (newData) {
                          populateDeviceModal(newData);
                      });
                  }, 60000); // 30 sec
              }, { once: true });

              // Clear interval when modal hidden
              modalElement.addEventListener('hidden.bs.modal', function () {
                  if (deviceDataInterval) {
                      clearInterval(deviceDataInterval);
                      deviceDataInterval = null;
                  }
              });
          }

          // Fetch new device data from server
          function fetchDeviceData(callback) {
              if (!deviceRefarshUrl) {
                  console.error('Refresh URL is not set');
                  return;
              }

              fetch(deviceRefarshUrl)
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
                      console.error('Error fetching device data:', error);
                  });
          }


    


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
            url: action_url, // 👈 Use dynamic URL
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
                        $('#device-modal').modal('hide');
                        $('#edit_device-modal').modal('hide');
                        $('#add_form')[0].reset(); // 🔥 Clear form
                        // 👉 Refresh DataTable
                        $('#device-table').DataTable().ajax.reload(null, false); // false = stay on current page
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
            error: function(data) {
                alert('Something went wrong. Please try again.');
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
                a.download = "device.csv"; // Always CSV
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
        if (confirm("Are you sure you want to delete this device?")) {
            $.ajax({
                url: deleteUrl, // Or use a named route if needed
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        showNotification(response.message, 'success');
                        $('#device-table').DataTable().ajax.reload(null, false); //  Refresh table
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
                    $('#device-table').DataTable().ajax.reload(null, false); // Reload table
                } else {
                    showNotification('Status change failed.', 'error');
                }
            },
            error: function() {
                showNotification('Error toggling status.', 'error');
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

