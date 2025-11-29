<?php
/**
 * Reverse Geocoding Action
 * Proxy for Nominatim API to avoid CORS issues
 */

header('Content-Type: application/json');

// Get coordinates from request
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if (!$lat || !$lon) {
    echo json_encode([
        'error' => true,
        'message' => 'Invalid coordinates'
    ]);
    exit;
}

// Validate coordinates
if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    echo json_encode([
        'error' => true,
        'message' => 'Coordinates out of range'
    ]);
    exit;
}

// Build Nominatim request URL
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&addressdetails=1";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Set User-Agent (required by Nominatim)
curl_setopt($ch, CURLOPT_USERAGENT, 'VendorConnect Ghana/1.0 (Wedding Platform)');

// Add headers
$headers = [
    'Accept: application/json',
    'Accept-Language: en'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle errors
if ($curl_error) {
    error_log("Nominatim API Error: " . $curl_error);
    echo json_encode([
        'error' => true,
        'message' => 'Geocoding service unavailable',
        'fallback' => true
    ]);
    exit;
}

if ($http_code !== 200) {
    error_log("Nominatim HTTP Error: " . $http_code);
    echo json_encode([
        'error' => true,
        'message' => 'Geocoding request failed',
        'fallback' => true
    ]);
    exit;
}

// Return response
echo $response;
?>
