<?php
/**
 * Clear Location from Session
 * Removes user's location from session
 */

session_start();
header('Content-Type: application/json');

unset($_SESSION['user_latitude']);
unset($_SESSION['user_longitude']);
unset($_SESSION['location_updated_at']);

echo json_encode([
    'status' => 'success',
    'message' => 'Location cleared from session'
]);
