<?php

require_once '../settings/db_class.php';

/**
 * Customer class that extends database connection
 * Contains customer methods: add customer, edit customer, delete customer, etc.
 */
class Customer extends db_connection
{
    private $customer_id;
    private $customer_name;
    private $customer_email;
    private $customer_pass;
    private $customer_country;
    private $customer_city;
    private $customer_contact;
    private $customer_image;
    private $user_role;

    public function __construct($customer_id = null)
    {
        // Initialize database connection
        $this->db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    /**
     * Load customer data from database
     */
    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            $this->customer_name = $result['customer_name'];
            $this->customer_email = $result['customer_email'];
            $this->customer_pass = $result['customer_pass'];
            $this->customer_country = $result['customer_country'];
            $this->customer_city = $result['customer_city'];
            $this->customer_contact = $result['customer_contact'];
            $this->customer_image = $result['customer_image'];
            $this->user_role = $result['user_role'];
            return true;
        }
        return false;
    }

    /**
     * Add a new customer to the database
     */
    public function addCustomer($customer_name, $customer_email, $customer_pass, $customer_country, $customer_city, $customer_contact, $user_role = 2)
    {
        // Check if email already exists
        if ($this->getCustomerByEmail($customer_email)) {
            return false; // Email already exists
        }

        // Hash the password
        $hashed_password = password_hash($customer_pass, PASSWORD_DEFAULT);
        
        // Prepare and execute insert statement
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $customer_name, $customer_email, $hashed_password, $customer_country, $customer_city, $customer_contact, $user_role);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Edit/Update customer information
     */
    public function editCustomer($customer_id, $customer_name, $customer_email, $customer_country, $customer_city, $customer_contact, $customer_image = null)
    {
        $stmt = $this->db->prepare("UPDATE customer SET customer_name = ?, customer_email = ?, customer_country = ?, customer_city = ?, customer_contact = ?, customer_image = ? WHERE customer_id = ?");
        $stmt->bind_param("ssssssi", $customer_name, $customer_email, $customer_country, $customer_city, $customer_contact, $customer_image, $customer_id);
        
        return $stmt->execute();
    }

    /**
     * Delete a customer from the database
     */
    public function deleteCustomer($customer_id)
    {
        $stmt = $this->db->prepare("DELETE FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        
        return $stmt->execute();
    }

    /**
     * Get customer by email
     */
    public function getCustomerByEmail($customer_email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $customer_email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById($customer_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Check if email is available for registration
     */
    public function isEmailAvailable($customer_email)
    {
        $customer = $this->getCustomerByEmail($customer_email);
        return $customer === null || $customer === false;
    }

    /**
     * Update customer password
     */
    public function updatePassword($customer_id, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $hashed_password, $customer_id);
        
        return $stmt->execute();
    }

    /**
     * Get all customers (for admin purposes)
     */
    public function getAllCustomers()
    {
        $stmt = $this->db->prepare("SELECT customer_id, customer_name, customer_email, customer_country, customer_city, customer_contact, user_role FROM customer ORDER BY customer_id DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Login customer with email and password
     * @param string $customer_email
     * @param string $customer_password
     * @return array|false - customer data if login successful, false if failed
     */
    public function loginCustomer($customer_email, $customer_password)
    {
        // Get customer by email
        $customer = $this->getCustomerByEmail($customer_email);
        
        if (!$customer) {
            return false; // Customer not found
        }
        
        // Verify password
        if (password_verify($customer_password, $customer['customer_pass'])) {
            // Remove password from returned data for security
            unset($customer['customer_pass']);
            return $customer;
        }
        
        return false; // Invalid password
    }

    // Getters
    public function getCustomerId() { return $this->customer_id; }
    public function getCustomerName() { return $this->customer_name; }
    public function getCustomerEmail() { return $this->customer_email; }
    public function getCustomerCountry() { return $this->customer_country; }
    public function getCustomerCity() { return $this->customer_city; }
    public function getCustomerContact() { return $this->customer_contact; }
    public function getCustomerImage() { return $this->customer_image; }
    public function getUserRole() { return $this->user_role; }
}