<?php
/**
 * Consultation Class
 * Handles consultation booking database operations
 */

require_once dirname(__FILE__) . '/../settings/db_class.php';

class consultation_class extends db_connection {
    
    /**
     * Create a new consultation booking
     */
    public function create_consultation($customer_id, $planner_id, $service_id, $date, $time, $duration, $fee, $notes = '', $location = '', $order_id = null) {
        $conn = $this->db_conn();
        
        $customer_id = mysqli_real_escape_string($conn, $customer_id);
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        $service_id = mysqli_real_escape_string($conn, $service_id);
        $date = mysqli_real_escape_string($conn, $date);
        $time = mysqli_real_escape_string($conn, $time);
        $duration = mysqli_real_escape_string($conn, $duration);
        $fee = mysqli_real_escape_string($conn, $fee);
        $notes = mysqli_real_escape_string($conn, $notes);
        $location = mysqli_real_escape_string($conn, $location);
        
        $order_id_sql = $order_id ? mysqli_real_escape_string($conn, $order_id) : 'NULL';
        
        $sql = "INSERT INTO consultations (customer_id, planner_id, order_id, service_id, consultation_date, consultation_time, 
                duration_minutes, consultation_fee, customer_notes, meeting_location, booking_status, payment_status) 
                VALUES ($customer_id, $planner_id, $order_id_sql, $service_id, '$date', '$time', $duration, $fee, '$notes', '$location', 'pending', 'unpaid')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }
    
    /**
     * Update consultation payment status
     */
    public function update_payment_status($consultation_id, $payment_ref, $status = 'paid') {
        $conn = $this->db_conn();
        
        $consultation_id = mysqli_real_escape_string($conn, $consultation_id);
        $payment_ref = mysqli_real_escape_string($conn, $payment_ref);
        $status = mysqli_real_escape_string($conn, $status);
        
        $sql = "UPDATE consultations SET payment_status = '$status', payment_reference = '$payment_ref', booking_status = 'confirmed' 
                WHERE consultation_id = $consultation_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Get consultation by ID
     */
    public function get_consultation($consultation_id) {
        $conn = $this->db_conn();
        $consultation_id = mysqli_real_escape_string($conn, $consultation_id);
        
        $sql = "SELECT c.*, 
                       cust.customer_name, cust.customer_email, cust.customer_contact,
                       plan.customer_name as planner_name, plan.customer_email as planner_email,
                       s.service_name, s.service_description
                FROM consultations c
                LEFT JOIN customer cust ON c.customer_id = cust.customer_id
                LEFT JOIN customer plan ON c.planner_id = plan.customer_id
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.consultation_id = $consultation_id";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get consultation by payment reference
     */
    public function get_consultation_by_reference($payment_ref) {
        $conn = $this->db_conn();
        $payment_ref = mysqli_real_escape_string($conn, $payment_ref);
        
        $sql = "SELECT * FROM consultations WHERE payment_reference = '$payment_ref'";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get customer's consultations
     */
    public function get_customer_consultations($customer_id) {
        $conn = $this->db_conn();
        $customer_id = mysqli_real_escape_string($conn, $customer_id);
        
        $sql = "SELECT c.*, 
                       plan.customer_name as planner_name, plan.customer_email as planner_email,
                       s.service_name
                FROM consultations c
                LEFT JOIN customer plan ON c.planner_id = plan.customer_id
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.customer_id = $customer_id
                ORDER BY c.consultation_date DESC, c.consultation_time DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get planner's consultations
     */
    public function get_planner_consultations($planner_id, $status = null) {
        $conn = $this->db_conn();
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        
        $sql = "SELECT c.*, 
                       cust.customer_name, cust.customer_email, cust.customer_contact,
                       s.service_name
                FROM consultations c
                LEFT JOIN customer cust ON c.customer_id = cust.customer_id
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.planner_id = $planner_id";
        
        if ($status) {
            $status = mysqli_real_escape_string($conn, $status);
            $sql .= " AND c.booking_status = '$status'";
        }
        
        $sql .= " ORDER BY c.consultation_date DESC, c.consultation_time DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get upcoming consultations for planner
     */
    public function get_upcoming_consultations($planner_id) {
        $conn = $this->db_conn();
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        
        $sql = "SELECT c.*, 
                       cust.customer_name, cust.customer_email, cust.customer_contact,
                       s.service_name
                FROM consultations c
                LEFT JOIN customer cust ON c.customer_id = cust.customer_id
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.planner_id = $planner_id 
                AND c.consultation_date >= CURDATE()
                AND c.booking_status IN ('confirmed', 'pending')
                ORDER BY c.consultation_date ASC, c.consultation_time ASC
                LIMIT 10";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Update consultation status
     */
    public function update_consultation_status($consultation_id, $status, $notes = '') {
        $conn = $this->db_conn();
        
        $consultation_id = mysqli_real_escape_string($conn, $consultation_id);
        $status = mysqli_real_escape_string($conn, $status);
        $notes = mysqli_real_escape_string($conn, $notes);
        
        $sql = "UPDATE consultations SET booking_status = '$status', planner_notes = '$notes' WHERE consultation_id = $consultation_id";
        return $this->db_write_query($sql);
    }
    
    /**
     * Check if time slot is available
     */
    public function is_slot_available($planner_id, $date, $time, $duration) {
        $conn = $this->db_conn();
        
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        $date = mysqli_real_escape_string($conn, $date);
        $time = mysqli_real_escape_string($conn, $time);
        $duration = mysqli_real_escape_string($conn, $duration);
        
        $end_time = date('H:i:s', strtotime($time) + ($duration * 60));
        $end_time = mysqli_real_escape_string($conn, $end_time);
        
        $sql = "SELECT COUNT(*) as count FROM consultations 
                WHERE planner_id = $planner_id 
                AND consultation_date = '$date'
                AND booking_status NOT IN ('cancelled', 'no-show')
                AND (
                    (consultation_time <= '$time' AND DATE_ADD(consultation_time, INTERVAL duration_minutes MINUTE) > '$time') 
                    OR (consultation_time < '$end_time' AND DATE_ADD(consultation_time, INTERVAL duration_minutes MINUTE) >= '$end_time')
                )";
        
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] == 0;
    }
    
    /**
     * Get planner's available time slots
     */
    public function get_planner_slots($planner_id) {
        $conn = $this->db_conn();
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        
        $sql = "SELECT * FROM consultation_slots 
                WHERE planner_id = $planner_id AND is_available = 1 
                ORDER BY day_of_week, start_time";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Add time slot for planner
     */
    public function add_time_slot($planner_id, $day_of_week, $start_time, $end_time) {
        $conn = $this->db_conn();
        
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        $day_of_week = mysqli_real_escape_string($conn, $day_of_week);
        $start_time = mysqli_real_escape_string($conn, $start_time);
        $end_time = mysqli_real_escape_string($conn, $end_time);
        
        $sql = "INSERT INTO consultation_slots (planner_id, day_of_week, start_time, end_time) 
                VALUES ($planner_id, $day_of_week, '$start_time', '$end_time')";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Delete time slot
     */
    public function delete_time_slot($slot_id, $planner_id = null) {
        $conn = $this->db_conn();
        $slot_id = mysqli_real_escape_string($conn, $slot_id);
        
        $sql = "DELETE FROM consultation_slots WHERE slot_id = $slot_id";
        
        // If planner_id provided, verify ownership
        if ($planner_id !== null) {
            $planner_id = mysqli_real_escape_string($conn, $planner_id);
            $sql .= " AND planner_id = $planner_id";
        }
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Get all service types
     */
    public function get_service_types() {
        $sql = "SELECT * FROM service_types WHERE is_active = 1 ORDER BY service_name";
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get planner analytics
     */
    public function get_planner_analytics($planner_id) {
        $conn = $this->db_conn();
        $planner_id = mysqli_real_escape_string($conn, $planner_id);
        
        $analytics = [];
        
        // Total bookings
        $sql = "SELECT COUNT(*) as total FROM consultations WHERE planner_id = $planner_id";
        $result = $this->db_fetch_one($sql);
        $analytics['total_bookings'] = $result ? $result['total'] : 0;
        
        // Total revenue
        $sql = "SELECT SUM(consultation_fee) as revenue FROM consultations 
                WHERE planner_id = $planner_id AND payment_status = 'paid'";
        $result = $this->db_fetch_one($sql);
        $analytics['total_revenue'] = $result ? ($result['revenue'] ?? 0) : 0;
        
        // Upcoming bookings
        $sql = "SELECT COUNT(*) as upcoming FROM consultations 
                WHERE planner_id = $planner_id AND consultation_date >= CURDATE() 
                AND booking_status IN ('confirmed', 'pending')";
        $result = $this->db_fetch_one($sql);
        $analytics['upcoming_bookings'] = $result ? $result['upcoming'] : 0;
        
        // Completed consultations
        $sql = "SELECT COUNT(*) as completed FROM consultations 
                WHERE planner_id = $planner_id AND booking_status = 'completed'";
        $result = $this->db_fetch_one($sql);
        $analytics['completed_consultations'] = $result ? $result['completed'] : 0;
        
        // Most popular service
        $sql = "SELECT s.service_name, COUNT(*) as count 
                FROM consultations c
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.planner_id = $planner_id
                GROUP BY c.service_id
                ORDER BY count DESC
                LIMIT 1";
        $result = $this->db_fetch_one($sql);
        $analytics['popular_service'] = $result ? $result['service_name'] : 'N/A';
        
        // Monthly revenue (last 6 months)
        $sql = "SELECT DATE_FORMAT(consultation_date, '%Y-%m') as month, 
                       SUM(consultation_fee) as revenue
                FROM consultations 
                WHERE planner_id = $planner_id 
                AND payment_status = 'paid'
                AND consultation_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY month
                ORDER BY month DESC";
        $analytics['monthly_revenue'] = $this->db_fetch_all($sql);
        
        // Service breakdown
        $sql = "SELECT s.service_name, COUNT(*) as count, SUM(c.consultation_fee) as revenue
                FROM consultations c
                LEFT JOIN service_types s ON c.service_id = s.service_id
                WHERE c.planner_id = $planner_id AND c.payment_status = 'paid'
                GROUP BY c.service_id
                ORDER BY count DESC";
        $analytics['service_breakdown'] = $this->db_fetch_all($sql);
        
        return $analytics;
    }
}
?>
