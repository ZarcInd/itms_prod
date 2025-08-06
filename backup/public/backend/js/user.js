 $(document).ready(function () {
    var table = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        columns: [
            {data: 'DT_RowIndex'},
            {data: 'name'},
            {data: 'email'},
            {data: 'phone'},
            {data: 'status'},
            {data: 'action'},
        ]
    });
    
});

$(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        let deleteUrl = $(this).data('url');
        if (confirm("Are you sure you want to delete this User?")) {
            $.ajax({
                url: deleteUrl, // Or use a named route if needed
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        showNotification(response.message, 'success');
                        $('#user-table').DataTable().ajax.reload(null, false); // 🔄 Refresh table
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
                    $('#user-table').DataTable().ajax.reload(null, false); // Reload table
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


 // Password toggle visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.querySelector('i').classList.remove('bi-eye');
                this.querySelector('i').classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.querySelector('i').classList.remove('bi-eye-slash');
                this.querySelector('i').classList.add('bi-eye');
            }
        });
    });

    // Password strength meter
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');

        // Calculate password strength
        let strength = 0;
        const regexes = [
            /[a-z]+/, // lowercase
            /[A-Z]+/, // uppercase
            /[0-9]+/, // numbers
            /[$@#&!]+/, // special characters
            /.{8,}/ // at least 8 characters
        ];

        // Check each criteria
        regexes.forEach(regex => {
            if (regex.test(password)) {
                strength += 1;
            }
        });

        // Add extra points for length
        if (password.length > 12) strength += 1;

        // Calculate percentage
        let percentage = 0;
        let text = '';
        let color = '';

        switch (strength) {
            case 0:
                percentage = 0;
                text = 'Password strength: Too weak';
                color = '#dc3545'; // red
                break;
            case 1:
            case 2:
                percentage = 25;
                text = 'Password strength: Weak';
                color = '#dc3545'; // red
                break;
            case 3:
                percentage = 50;
                text = 'Password strength: Medium';
                color = '#ffc107'; // yellow
                break;
            case 4:
                percentage = 75;
                text = 'Password strength: Strong';
                color = '#28a745'; // green
                break;
            case 5:
            case 6:
                percentage = 100;
                text = 'Password strength: Very strong';
                color = '#28a745'; // green
                break;
        }

        // Update UI
        strengthBar.style.width = percentage + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    });

    // Confirm password validation
    document.getElementById('new_password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirmPassword = this.value;

        if (password === confirmPassword && password !== '') {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '';
        }
    });



    // Check all checkboxes
        document.getElementById('checkAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Uncheck all checkboxes
        document.getElementById('uncheckAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Column-specific toggles
        document.querySelectorAll('.column-selector .btn').forEach(button => {
            button.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                const columnCheckboxes = document.querySelectorAll(`.${column}-checkbox`);
                
                // Check if all checkboxes in this column are checked
                const allChecked = Array.from(columnCheckboxes).every(checkbox => checkbox.checked);
                
                // Toggle: if all are checked, uncheck all; otherwise, check all
                columnCheckboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
            });
        });



$(document).ready(function() {
    $('.form_submit').on('submit', function(event){
        event.preventDefault();

        $('.submit_button').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        var form_data = new FormData(this);
        var action_url = $(this).attr('action'); //  Get URL from form attribute
        var data_url = $(this).data('url');

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
                     
                        //  Refresh DataTable
                        $('#vehicles-table').DataTable().ajax.reload(null, false); // false = stay on current page
                        alert(data.message);
                        location.href = data_url;
                        
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

