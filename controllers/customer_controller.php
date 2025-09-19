<?php

require_once '../classes/customer_class.php';

/**
 * Customer Controller
 * Creates an instance of the customer class and runs the methods
 */

/**
 * Register a new customer
 * @param array $kwargs - associative array containing customer data
 * @return int|false - customer ID if successful, false if failed
 */
function register_customer_ctr($kwargs)
{
    $customer = new Customer();
    
    // Extract required parameters
    $customer_name = $kwargs['customer_name'] ?? '';
    $customer_email = $kwargs['customer_email'] ?? '';
    $customer_pass = $kwargs['customer_pass'] ?? '';
    $customer_country = $kwargs['customer_country'] ?? '';
    $customer_city = $kwargs['customer_city'] ?? '';
    $customer_contact = $kwargs['customer_contact'] ?? '';
    $user_role = $kwargs['user_role'] ?? 2; // Default to customer role (2)
    
    // Validate required fields
    if (empty($customer_name) || empty($customer_email) || empty($customer_pass) || 
        empty($customer_country) || empty($customer_city) || empty($customer_contact)) {
        return false;
    }
    
    // Register the customer
    $customer_id = $customer->addCustomer(
        $customer_name, 
        $customer_email, 
        $customer_pass, 
        $customer_country, 
        $customer_city, 
        $customer_contact, 
        $user_role
    );
    
    return $customer_id;
}

/**
 * Get customer by email
 * @param string $email
 * @return array|false
 */
function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

/**
 * Get customer by ID
 * @param int $customer_id
 * @return array|false
 */
function get_customer_by_id_ctr($customer_id)
{
    $customer = new Customer();
    return $customer->getCustomerById($customer_id);
}

/**
 * Check if email is available for registration
 * @param string $email
 * @return bool
 */
function check_email_availability_ctr($email)
{
    $customer = new Customer();
    return $customer->isEmailAvailable($email);
}

/**
 * Update customer information
 * @param array $kwargs - associative array containing customer data including customer_id
 * @return bool
 */
function update_customer_ctr($kwargs)
{
    $customer = new Customer();
    
    $customer_id = $kwargs['customer_id'] ?? 0;
    $customer_name = $kwargs['customer_name'] ?? '';
    $customer_email = $kwargs['customer_email'] ?? '';
    $customer_country = $kwargs['customer_country'] ?? '';
    $customer_city = $kwargs['customer_city'] ?? '';
    $customer_contact = $kwargs['customer_contact'] ?? '';
    $customer_image = $kwargs['customer_image'] ?? null;
    
    if (empty($customer_id) || empty($customer_name) || empty($customer_email)) {
        return false;
    }
    
    return $customer->editCustomer(
        $customer_id, 
        $customer_name, 
        $customer_email, 
        $customer_country, 
        $customer_city, 
        $customer_contact, 
        $customer_image
    );
}

/**
 * Delete a customer
 * @param int $customer_id
 * @return bool
 */
function delete_customer_ctr($customer_id)
{
    $customer = new Customer();
    return $customer->deleteCustomer($customer_id);
}

/**
 * Get all customers (admin function)
 * @return array
 */
function get_all_customers_ctr()
{
    $customer = new Customer();
    return $customer->getAllCustomers();
}

/**
 * Update customer password
 * @param int $customer_id
 * @param string $new_password
 * @return bool
 */
function update_customer_password_ctr($customer_id, $new_password)
{
    $customer = new Customer();
    return $customer->updatePassword($customer_id, $new_password);
}

/**
 * Login customer with email and password
 * @param string $email
 * @param string $password
 * @return array|false - customer data if login successful, false if failed
 */
function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    return $customer->loginCustomer($email, $password);
}