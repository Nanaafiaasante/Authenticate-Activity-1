<?php
/**
 * Location Helper Class
 * Handles geocoding, distance calculations, and location-based operations
 */

class Location {
    
    /**
     * Calculate distance between two coordinates using Haversine formula
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @param string $unit Unit of measurement: 'km' or 'mi'
     * @return float|null Distance in specified unit or null if invalid coordinates
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km') {
        // Check if coordinates are valid
        if (!is_numeric($lat1) || !is_numeric($lon1) || !is_numeric($lat2) || !is_numeric($lon2)) {
            return null;
        }
        
        // Radius of the Earth
        $earthRadius = ($unit === 'mi') ? 3959 : 6371; // miles or kilometers
        
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        // Haversine formula
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = $earthRadius * $c;
        
        return round($distance, 2);
    }
    
    /**
     * Geocode a location (city, country) to get coordinates
     * Uses Nominatim OpenStreetMap API (free, no key required)
     * @param string $city City name
     * @param string $country Country name
     * @return array|null Array with 'latitude' and 'longitude' or null if failed
     */
    public static function geocodeLocation($city, $country) {
        // Build search query
        $query = trim($city . ', ' . $country);
        $query = urlencode($query);
        
        // Nominatim API endpoint
        $url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=1";
        
        // Set user agent (required by Nominatim)
        $options = [
            'http' => [
                'header' => "User-Agent: VendorConnectGhana/1.0\r\n"
            ]
        ];
        $context = stream_context_create($options);
        
        // Make API request
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            return [
                'latitude' => floatval($data[0]['lat']),
                'longitude' => floatval($data[0]['lon'])
            ];
        }
        
        return null;
    }
    
    /**
     * Format distance for display
     * @param float $distance Distance in kilometers
     * @return string Formatted distance string (e.g., "5 km" or "500 m")
     */
    public static function formatDistance($distance) {
        if ($distance === null || !is_numeric($distance)) {
            return 'Distance unknown';
        }
        
        if ($distance < 1) {
            // Show in meters for distances less than 1 km
            return round($distance * 1000) . ' m';
        } elseif ($distance < 10) {
            // Show one decimal for distances less than 10 km
            return number_format($distance, 1) . ' km';
        } else {
            // Show whole numbers for larger distances
            return round($distance) . ' km';
        }
    }
    
    /**
     * Get SQL clause for distance calculation (for sorting/filtering in queries)
     * @param float $userLat User's latitude
     * @param float $userLon User's longitude
     * @param string $latField Database field name for latitude
     * @param string $lonField Database field name for longitude
     * @return string SQL formula for distance calculation
     */
    public static function getDistanceSQL($userLat, $userLon, $latField = 'latitude', $lonField = 'longitude') {
        $earthRadius = 6371; // kilometers
        
        $sql = "(
            {$earthRadius} * ACOS(
                COS(RADIANS({$userLat})) * 
                COS(RADIANS({$latField})) * 
                COS(RADIANS({$lonField}) - RADIANS({$userLon})) + 
                SIN(RADIANS({$userLat})) * 
                SIN(RADIANS({$latField}))
            )
        )";
        
        return $sql;
    }
    
    /**
     * Get user's location from session
     * @return array|null Array with 'latitude' and 'longitude' or null
     */
    public static function getUserLocation() {
        if (isset($_SESSION['user_latitude']) && isset($_SESSION['user_longitude'])) {
            return [
                'latitude' => floatval($_SESSION['user_latitude']),
                'longitude' => floatval($_SESSION['user_longitude'])
            ];
        }
        return null;
    }
    
    /**
     * Set user's location in session
     * @param float $latitude User's latitude
     * @param float $longitude User's longitude
     */
    public static function setUserLocation($latitude, $longitude) {
        $_SESSION['user_latitude'] = floatval($latitude);
        $_SESSION['user_longitude'] = floatval($longitude);
        $_SESSION['location_updated_at'] = date('Y-m-d H:i:s');
    }
    
    /**
     * Check if location is within a radius
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @param float $radius Maximum radius in kilometers
     * @return bool True if within radius
     */
    public static function isWithinRadius($lat1, $lon1, $lat2, $lon2, $radius) {
        $distance = self::calculateDistance($lat1, $lon1, $lat2, $lon2);
        return $distance !== null && $distance <= $radius;
    }
    
    /**
     * Reverse geocode coordinates to get city and country
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return array|null Array with 'city' and 'country' or null
     */
    public static function reverseGeocode($latitude, $longitude) {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}";
        
        $options = [
            'http' => [
                'header' => "User-Agent: VendorConnectGhana/1.0\r\n"
            ]
        ];
        $context = stream_context_create($options);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['address'])) {
            $address = $data['address'];
            return [
                'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['county'] ?? '',
                'country' => $address['country'] ?? ''
            ];
        }
        
        return null;
    }
}
