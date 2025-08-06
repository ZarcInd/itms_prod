$(document).ready(function() {
    // Toggle sidebar on mobile
    $('#toggle-sidebar').click(function() {
          $('.sidebar').toggleClass('show');
    });
    
    // Initialize map
    const map = L.map('maps').setView([23.5937, 78.9629], 5); // Center of India
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Custom marker icon class
    const CustomMarkerIcon = L.divIcon({
        className: 'custom-marker',
        iconSize: [22, 22],
        iconAnchor: [11, 22]
    });
    
    // Add markers at various locations in India
    const locations = [
        [28.6139, 77.2090], // Delhi
        [19.0760, 72.8777], // Mumbai
        [12.9716, 77.5946], // Bangalore
        [17.3850, 78.4867], // Hyderabad
        [22.5726, 88.3639], // Kolkata
        [13.0827, 80.2707], // Chennai
        [23.0225, 72.5714], // Ahmedabad
        [26.9124, 75.7873], // Jaipur
        [25.5941, 85.1376], // Patna
        [22.7196, 75.8577], // Indore
        [23.2599, 77.4126]  // Bhopal
    ];
    
    // Add markers to the map
    locations.forEach(loc => {
        L.marker(loc, {icon: CustomMarkerIcon}).addTo(map);
    });

   
});



  // Get elements
  const userDropdown = document.getElementById('userDropdown');
  const dropdownMenu = document.getElementById('userDropdownMenu');

  // Toggle dropdown when clicking on the user profile
  userDropdown.addEventListener('click', function(e) {
      dropdownMenu.classList.toggle('show');
      e.stopPropagation();
  });

  // Close dropdown when clicking elsewhere on the page
  document.addEventListener('click', function(e) {
      if (!userDropdown.contains(e.target)) {
          dropdownMenu.classList.remove('show');
      }
  });

    const vehicles = {
        bus1: {
            name: "Bus 1",
            location: "Nagpur, India",
            speed: "10 km/h",
            pinId: "pin-nagpur",
            group: "buses",
            icon: "https://cdn-icons-png.flaticon.com/512/61/61205.png",
            lat: 21.1458,
            lng: 79.0882
        },
        bus2: {
            name: "Bus 2",
            location: "Chennai, India",
            speed: "15 km/h",
            pinId: "pin-chennai",
            group: "buses",
            icon: "https://cdn-icons-png.flaticon.com/512/61/61205.png",
            lat: 13.0827,
            lng: 80.2707
        },
        truck1: {
            name: "Truck 1",
            location: "Mumbai, India",
            speed: "20 km/h",
            pinId: "pin-mumbai",
            group: "trucks",
            icon: "https://cdn-icons-png.flaticon.com/512/743/743007.png",
            lat: 19.0760,
            lng: 72.8777
        },
        truck2: {
            name: "Truck 2",
            location: "Delhi, India",
            speed: "8 km/h",
            pinId: "pin-delhi",
            group: "trucks",
            icon: "https://cdn-icons-png.flaticon.com/512/743/743007.png",
            lat: 28.6139,
            lng: 77.2090
        },
        delivery1: {
            name: "Delivery 1",
            location: "Hyderabad, India",
            speed: "12 km/h",
            pinId: "pin-hyderabad",
            group: "delivery",
            icon: "https://cdn-icons-png.flaticon.com/512/1995/1995622.png",
            lat: 17.3850,
            lng: 78.4867
        }
       
};


      

  /**
 * Profile Image Change Functionality
 * For Laravel user profile with #7e57c2 color theme
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get references to the elements
    const profileImgContainer = document.querySelector('.position-relative');
    const profileImg = profileImgContainer.querySelector('img');
    const editButton = profileImgContainer.querySelector('.btn');
    
    // Create a hidden file input element
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'file'; // Changed to 'file' to match controller
    fileInput.accept = 'image/*';
    fileInput.style.display = 'none';
    profileImgContainer.appendChild(fileInput);
    
    // Add click event to the edit button
    
    
    // Handle file selection
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Check file size and type
            if (!validateImageFile(this.files[0])) {
                return;
            }
            
            // Display loading spinner
            showLoading(profileImg);
            
            // Preview image before upload
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update image preview
                profileImg.src = e.target.result;
                
                // Upload the image to server
                uploadImageToServer(fileInput.files[0]);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    /**
     * Shows a loading spinner overlay on the image
     */
    function showLoading(imgElement) {
        // Create loading overlay
        const overlay = document.createElement('div');
        overlay.classList.add('loading-overlay');
        overlay.style.position = 'absolute';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(126, 87, 194, 0.3)'; // #7e57c2 with opacity
        overlay.style.borderRadius = '50%';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        
        // Create spinner
        const spinner = document.createElement('div');
        spinner.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
        spinner.style.color = 'white';
        spinner.style.fontSize = '2rem';
        spinner.classList.add('spinner-icon');
        
        // Add animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .spinner-icon {
                animation: spin 1s linear infinite;
            }
        `;
        document.head.appendChild(style);
        
        overlay.appendChild(spinner);
        imgElement.parentNode.appendChild(overlay);
    }
    
    /**
     * Removes the loading spinner overlay
     */
    function hideLoading(imgElement) {
        const overlay = imgElement.parentNode.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    /**
     * Shows a message to the user
     */
    function showMessage(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.classList.add('toast-container', 'position-fixed', 'bottom-0', 'end-0', 'p-3');
            document.body.appendChild(toastContainer);
        }
        
        // Create toast
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.classList.add('toast', 'show');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        // Set background color based on message type
        let bgColor = '#7e57c2'; // Default purple theme
        let iconClass = 'bi-info-circle';
        
        if (type === 'success') {
            bgColor = '#7e57c2'; // Keep theme color for success
            iconClass = 'bi-check-circle';
        } else if (type === 'error') {
            bgColor = '#dc3545'; // Bootstrap danger color
            iconClass = 'bi-exclamation-circle';
        }
        
        toast.style.backgroundColor = bgColor;
        toast.style.color = 'white';
        
        // Create toast content
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        // Add to container
        toastContainer.appendChild(toast);
        
        // Create dismiss button functionality
        const closeBtn = toast.querySelector('.btn-close');
        closeBtn.addEventListener('click', function() {
            toast.remove();
        });
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    /**
     * Uploads image to server
     * Matches your controller implementation
     */
    function uploadImageToServer(file) {
        // Create form data
        const formData = new FormData();
        formData.append('file', file); // Changed to 'file' to match controller
        formData.append('_token', getCSRFToken()); // Get Laravel CSRF token
        
        // Show loading indicator
        showLoading(profileImg);
        
        // Make AJAX request
        fetch('/admin/profile/update-image', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRFToken()
                // No Content-Type header with FormData (browser sets it with boundary)
            }
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading indicator
            hideLoading(profileImg);
            
            if (data.status) {
                // Success
                showMessage(data.message, 'success');
                
                // Update image with new path if provided
                if (data.file) {
                    profileImg.src = '/' + data.file;
                }
            } else {
                // Error
                showMessage(data.message, 'error');
                
                // Revert to original image if available
                if (originalImageSrc) {
                    profileImg.src = originalImageSrc;
                }
            }
        })
        .catch(error => {
            // Hide loading indicator
            hideLoading(profileImg);
            
            // Show error
            showMessage('Error uploading image. Please try again.', 'error');
            console.error('Upload error:', error);
            
            // Revert to original image if available
            if (originalImageSrc) {
                profileImg.src = originalImageSrc;
            }
        });
    }
    
   
    /**
     * Gets Laravel CSRF token from meta tag
     */
    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
});

/**
 * Validates an image file before upload
 * @param {File} file - The file to validate
 * @returns {boolean} Whether the file is valid
 */
function validateImageFile(file) {
    // Check file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        showMessage('Please select a valid image file (JPG, JPEG, PNG, GIF)', 'error');
        return false;
    }
    
    // Check file size (max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        showMessage('Image size should be less than 2MB', 'error');
        return false;
    }
    
    return true;
}

/**
 * Shows message function definition outside of DOMContentLoaded
 * for use by validateImageFile
 */
function showMessage(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.classList.add('toast-container', 'position-fixed', 'bottom-0', 'end-0', 'p-3');
        document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.classList.add('toast', 'show');
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Set background color based on message type
    let bgColor = '#7e57c2'; // Default purple theme
    let iconClass = 'bi-info-circle';
    
    if (type === 'success') {
        bgColor = '#7e57c2'; // Keep theme color for success
        iconClass = 'bi-check-circle';
    } else if (type === 'error') {
        bgColor = '#dc3545'; // Bootstrap danger color
        iconClass = 'bi-exclamation-circle';
    }
    
    toast.style.backgroundColor = bgColor;
    toast.style.color = 'white';
    
    // Create toast content
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${iconClass} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Create dismiss button functionality
    const closeBtn = toast.querySelector('.btn-close');
    closeBtn.addEventListener('click', function() {
        toast.remove();
    });
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}


