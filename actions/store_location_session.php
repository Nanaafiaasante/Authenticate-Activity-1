<?php
/**
 * Store Location in Session
 * Saves user's location coordinates in session for server-side distance calculations
 */

session_start();
header('Content-Type: application/json');

require_once '../classes/location_class.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

if ($latitude && $longitude) {
    Location::setUserLocation($latitude, $longitude);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Location stored in session',
        'latitude' => $latitude,
        'longitude' => $longitude
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid coordinates'
    ]);
}
