@extends('admin/layouts/master')
@section('container')
<style>
    .export-container {
        max-width: 500px;
        margin: 20px auto;
        background: #fff;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .export-title {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    .btn-download {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        background: #775DA6;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn-download:hover {
        background: #2980b9;
    }
    
    .btn-download:disabled {
        background: #bdc3c7;
        cursor: not-allowed;
    }
    
    .loader {
        display: none;
        margin: 30px auto;
        border: 8px solid rgba(119, 93, 166, 0.2);
        border-top: 8px solid #775DA6;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    
    .loading-text {
        text-align: center;
        font-weight: bold;
        color: #555;
        display: none;
        margin-top: 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .alert {
        padding: 12px 16px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
</style>
<main class="main-content">
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #775DA6;">Download Communicating Device Data</h4>
            </div>
            
            <div class="card-body">
                <div class="export-container">
                    <h2 class="export-title">
                        Download Communicating Device Data 
                        <i class="fa-solid fa-tower-cell"></i>
                    </h2>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form id="downloadForm">
                        @csrf
                        <div class="form-group">
                            <label for="date" class="form-label">Select Date</label>
                            <input type="date" 
                                   id="date" 
                                   name="date" 
                                   class="form-control" 
                                   required 
                                   max="{{ date('Y-m-d') }}" />
                        </div>
                        
                        <button type="submit" id="downloadBtn" class="btn-download">
                            <i class="fa-solid fa-download me-2"></i>
                            Download CSV
                        </button>
                    </form>
                    
                    <div class="loader" id="loader"></div>
                    <div class="loading-text" id="loadingText">
                        Preparing download, please wait...
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadForm = document.getElementById('downloadForm');
    const downloadBtn = document.getElementById('downloadBtn');
    const loader = document.getElementById('loader');
    const loadingText = document.getElementById('loadingText');
    
    downloadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const dateInput = document.getElementById('date');
        const date = dateInput.value;
        
        if (!date) {
            alert('Please select a date');
            return;
        }
        
        const token = Date.now().toString();
        
        // Disable button and show loading
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Preparing...';
        loader.style.display = 'block';
        loadingText.style.display = 'block';
        
        // Create form data
        const formData = new FormData();
        formData.append('date', date);
        formData.append('token', token);
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        // Create a temporary form for file download
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = '{{ route("download.communicating.csv") }}';
        tempForm.style.display = 'none';
        
        // Add form data to temp form
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            tempForm.appendChild(input);
        }
        
        document.body.appendChild(tempForm);
        tempForm.submit();
        document.body.removeChild(tempForm);
        
        // Poll for download completion
        const pollInterval = setInterval(() => {
            fetch('{{ route("check-status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => response.json())
            .then(data => {
                if (data.ready) {
                    clearInterval(pollInterval);
                    resetForm();
                }
            })
            .catch(error => {
                console.error('Error checking download status:', error);
                clearInterval(pollInterval);
                resetForm();
            });
        }, 500);
        
        // Fallback timeout to reset form after 30 seconds
        setTimeout(() => {
            clearInterval(pollInterval);
            resetForm();
        }, 30000);
    });
    
    function resetForm() {
        downloadBtn.disabled = false;
        downloadBtn.innerHTML = '<i class="fa-solid fa-download me-2"></i>Download CSV';
        loader.style.display = 'none';
        loadingText.style.display = 'none';
    }
});
</script>
@endsection