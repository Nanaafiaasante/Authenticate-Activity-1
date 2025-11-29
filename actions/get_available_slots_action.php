<?php
/**
 * Get Available Slots for Booking
 * Returns planner's availability and existing bookings
 */

// Catch all errors and return as JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'PHP Error: ' . $errstr]);
    exit;
});

require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

// Get planner_id from query string
if (!isset($_GET['planner_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing planner ID'
    ]);
    exit;
}

$planner_id = intval($_GET['planner_id']);

try {
    // Get planner's available time slots
    $slots = get_planner_slots_ctr($planner_id);

    // Get all upcoming consultations for this planner
    $consultations = get_planner_consultations_ctr($planner_id);

    // Filter to only confirmed/pending consultations
    $booked_slots = [];
    if ($consultations) {
        foreach ($consultations as $consultation) {
            if ($consultation['booking_status'] != 'cancelled') {
                $booked_slots[] = [
                    'date' => $consultation['consultation_date'],
                    'time' => substr($consultation['consultation_time'], 0, 5), // HH:MM
                    'duration' => $consultation['duration_minutes']
                ];
            }
        }
    }

    if ($slots) {
        echo json_encode([
            'status' => 'success',
            'slots' => $slots,
            'booked_slots' => $booked_slots
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No availability set by planner'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}
?>
