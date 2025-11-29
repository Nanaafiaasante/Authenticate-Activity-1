<?php
/**
 * Consultation Controller
 * Handles business logic for consultation bookings
 */

require_once dirname(__FILE__) . '/../classes/consultation_class.php';

/**
 * Create a new consultation booking
 */
function create_consultation_ctr($customer_id, $planner_id, $service_id, $date, $time, $duration, $fee, $notes = '', $location = '', $order_id = null) {
    $consultation = new consultation_class();
    return $consultation->create_consultation($customer_id, $planner_id, $service_id, $date, $time, $duration, $fee, $notes, $location, $order_id);
}

/**
 * Update consultation payment status
 */
function update_consultation_payment_ctr($consultation_id, $payment_ref, $status = 'paid') {
    $consultation = new consultation_class();
    return $consultation->update_payment_status($consultation_id, $payment_ref, $status);
}

/**
 * Get consultation by ID
 */
function get_consultation_ctr($consultation_id) {
    $consultation = new consultation_class();
    return $consultation->get_consultation($consultation_id);
}

/**
 * Get consultation by payment reference
 */
function get_consultation_by_reference_ctr($payment_ref) {
    $consultation = new consultation_class();
    return $consultation->get_consultation_by_reference($payment_ref);
}

/**
 * Get customer's consultations
 */
function get_customer_consultations_ctr($customer_id) {
    $consultation = new consultation_class();
    return $consultation->get_customer_consultations($customer_id);
}

/**
 * Get planner's consultations
 */
function get_planner_consultations_ctr($planner_id, $status = null) {
    $consultation = new consultation_class();
    return $consultation->get_planner_consultations($planner_id, $status);
}

/**
 * Get upcoming consultations for planner
 */
function get_upcoming_consultations_ctr($planner_id) {
    $consultation = new consultation_class();
    return $consultation->get_upcoming_consultations($planner_id);
}

/**
 * Update consultation status
 */
function update_consultation_status_ctr($consultation_id, $status, $notes = '') {
    $consultation = new consultation_class();
    return $consultation->update_consultation_status($consultation_id, $status, $notes);
}

/**
 * Check if time slot is available
 */
function is_slot_available_ctr($planner_id, $date, $time, $duration) {
    $consultation = new consultation_class();
    return $consultation->is_slot_available($planner_id, $date, $time, $duration);
}

/**
 * Get planner's available time slots
 */
function get_planner_slots_ctr($planner_id) {
    $consultation = new consultation_class();
    return $consultation->get_planner_slots($planner_id);
}

/**
 * Add time slot for planner
 */
function add_time_slot_ctr($planner_id, $day_of_week, $start_time, $end_time) {
    $consultation = new consultation_class();
    return $consultation->add_time_slot($planner_id, $day_of_week, $start_time, $end_time);
}

/**
 * Delete time slot
 */
function delete_time_slot_ctr($slot_id, $planner_id = null) {
    $consultation = new consultation_class();
    return $consultation->delete_time_slot($slot_id, $planner_id);
}

/**
 * Get all service types
 */
function get_service_types_ctr() {
    $consultation = new consultation_class();
    return $consultation->get_service_types();
}

/**
 * Get planner analytics
 */
function get_planner_analytics_ctr($planner_id) {
    $consultation = new consultation_class();
    return $consultation->get_planner_analytics($planner_id);
}
?>
