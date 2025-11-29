/**
 * Location Manager
 * Handles getting and storing user's current location for distance-based filtering
 */

const LocationManager = {
    // Check if location is stored and still valid (refresh every 24 hours)
    isLocationStale: function() {
        const lastUpdated = localStorage.getItem('location_updated_at');
        if (!lastUpdated) return true;
        
        const lastTime = new Date(lastUpdated);
        const now = new Date();
        const hoursDiff = (now - lastTime) / (1000 * 60 * 60);
        
        return hoursDiff > 24; // Refresh if older than 24 hours
    },
    
    // Get stored location
    getStoredLocation: function() {
        const lat = localStorage.getItem('user_latitude');
        const lng = localStorage.getItem('user_longitude');
        
        if (lat && lng && !this.isLocationStale()) {
            return {
                latitude: parseFloat(lat),
                longitude: parseFloat(lng)
            };
        }
        return null;
    },
    
    // Store location in localStorage and session
    storeLocation: function(latitude, longitude) {
        localStorage.setItem('user_latitude', latitude);
        localStorage.setItem('user_longitude', longitude);
        localStorage.setItem('location_updated_at', new Date().toISOString());
        
        // Also store in session via AJAX
        $.ajax({
            url: '../actions/store_location_session.php',
            type: 'POST',
            data: {
                latitude: latitude,
                longitude: longitude
            },
            dataType: 'json',
            success: function(response) {
                console.log('Location stored in session:', response);
            },
            error: function() {
                console.log('Failed to store location in session');
            }
        });
    },
    
    // Get user's current location via browser geolocation
    getCurrentLocation: function(successCallback, errorCallback) {
        if (!navigator.geolocation) {
            if (errorCallback) {
                errorCallback('Geolocation not supported');
            }
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const location = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                
                // Store the location
                LocationManager.storeLocation(location.latitude, location.longitude);
                
                if (successCallback) {
                    successCallback(location);
                }
            },
            function(error) {
                let errorMessage = 'Unable to get location';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location permission denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location unavailable';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Location request timed out';
                        break;
                }
                
                if (errorCallback) {
                    errorCallback(errorMessage);
                }
            },
            {
                enableHighAccuracy: false,
                timeout: 10000,
                maximumAge: 0
            }
        );
    },
    
    // Initialize location - get stored or request new
    initialize: function(options = {}) {
        const showNotification = options.showNotification !== false;
        const autoRequest = options.autoRequest !== false;
        
        // First check if we have stored location
        const stored = this.getStoredLocation();
        
        if (stored) {
            console.log('Using stored location:', stored);
            if (options.onSuccess) {
                options.onSuccess(stored);
            }
            return;
        }
        
        // If no stored location and autoRequest is true, ask for permission
        if (autoRequest) {
            this.getCurrentLocation(
                function(location) {
                    console.log('Got current location:', location);
                    if (showNotification) {
                        // Show subtle notification
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Location Enabled',
                                text: 'Showing services near you',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    }
                    if (options.onSuccess) {
                        options.onSuccess(location);
                    }
                },
                function(error) {
                    console.log('Location error:', error);
                    if (options.onError) {
                        options.onError(error);
                    }
                }
            );
        }
    },
    
    // Clear stored location
    clearLocation: function() {
        localStorage.removeItem('user_latitude');
        localStorage.removeItem('user_longitude');
        localStorage.removeItem('location_updated_at');
        
        // Clear from session
        $.ajax({
            url: '../actions/clear_location_session.php',
            type: 'POST',
            dataType: 'json'
        });
    },
    
    // Calculate distance between two points (Haversine formula)
    calculateDistance: function(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in kilometers
        const dLat = this.deg2rad(lat2 - lat1);
        const dLon = this.deg2rad(lon2 - lon1);
        
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        
        return distance;
    },
    
    // Helper: degrees to radians
    deg2rad: function(deg) {
        return deg * (Math.PI/180);
    },
    
    // Format distance for display
    formatDistance: function(distanceKm) {
        if (distanceKm < 1) {
            return Math.round(distanceKm * 1000) + ' m away';
        } else if (distanceKm < 10) {
            return distanceKm.toFixed(1) + ' km away';
        } else {
            return Math.round(distanceKm) + ' km away';
        }
    }
};

// Auto-initialize on page load for product listing pages
$(document).ready(function() {
    // Only auto-init on pages that show products
    const productPages = ['/view/all_products.php', '/index.php', '/view/shop.php'];
    const currentPath = window.location.pathname;
    
    if (productPages.some(page => currentPath.includes(page))) {
        LocationManager.initialize({
            showNotification: false, // Don't show notification on every page load
            autoRequest: false, // Don't auto-request, wait for user to trigger
            onSuccess: function(location) {
                console.log('Location ready:', location);
                // Trigger products reload with location if needed
                if (typeof loadProducts === 'function') {
                    loadProducts();
                }
            }
        });
    }
});
