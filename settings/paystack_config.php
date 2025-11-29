<?php
/**
 * Paystack Configuration
 * Secure payment gateway settings for Paystack integration
 */

require_once __DIR__ . '/db_cred.php';

// Paystack API Keys
define('PAYSTACK_SECRET_KEY', 'sk_test_45df55dec4edf6996eea14f1427f8316add91e3f');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_0126e6c3ee1d860dcd63402585d54e8fae03e3fe');

// Paystack API URLs
define('PAYSTACK_API_URL', 'https://api.paystack.co');
define('PAYSTACK_INIT_ENDPOINT', PAYSTACK_API_URL . '/transaction/initialize');
define('PAYSTACK_VERIFY_ENDPOINT', PAYSTACK_API_URL . '/transaction/verify/');

// Application Configuration
define('APP_ENVIRONMENT', 'test'); 

// Dynamically determine base URL (works for localhost and live domains)
if (!defined('APP_BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_path = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Go up one level from settings/
    $base_url = $protocol . '://' . $host . $script_path;
    define('APP_BASE_URL', rtrim($base_url, '/'));
}

define('PAYSTACK_CALLBACK_URL', APP_BASE_URL . '/view/paystack_callback.php');

/**
 * Initialize a Paystack transaction
 * 
 * @param float $amount - Amount in GHS (will be converted to pesewas)
 * @param string $email - Customer email address
 * @param string $reference - Optional unique reference
 * @param array $metadata - Optional metadata to attach to transaction
 * @return array - Response with 'status' and 'data' containing authorization_url
 */
function paystack_initialize_transaction($amount, $email, $reference = null, $metadata = []) {
    // Generate reference if not provided
    if (!$reference) {
        $reference = 'PAY-' . uniqid() . '-' . time();
    }
    
    // Convert GHS to pesewas (1 GHS = 100 pesewas)
    $amount_in_pesewas = round($amount * 100);
    
    // Prepare transaction data
    $data = [
        'amount' => $amount_in_pesewas,
        'email' => $email,
        'reference' => $reference,
        'callback_url' => PAYSTACK_CALLBACK_URL,
        'currency' => 'GHS',
        'metadata' => array_merge([
            'app' => 'VendorConnect Ghana',
            'environment' => APP_ENVIRONMENT
        ], $metadata)
    ];
    
    error_log("Initializing Paystack transaction: " . json_encode($data));
    
    // Make API request
    $response = paystack_api_request('POST', PAYSTACK_INIT_ENDPOINT, $data);
    
    return $response;
}

/**
 * Verify a Paystack transaction
 * 
 * @param string $reference - Transaction reference to verify
 * @return array - Response with transaction details
 */
function paystack_verify_transaction($reference) {
    error_log("Verifying Paystack transaction: $reference");
    
    $response = paystack_api_request('GET', PAYSTACK_VERIFY_ENDPOINT . $reference);
    
    return $response;
}

/**
 * Make a request to Paystack API
 * 
 * @param string $method - HTTP method (GET, POST, etc)
 * @param string $url - Full API endpoint URL
 * @param array $data - Optional data to send
 * @return array - API response decoded as array
 */
function paystack_api_request($method, $url, $data = null) {
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    // Set headers with authorization
    $headers = [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json',
        'Cache-Control: no-cache'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Send data for POST/PUT requests
    if ($method !== 'GET' && $data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);
    
    // Handle curl errors
    if ($curl_error) {
        error_log("Paystack API CURL Error: $curl_error");
        return [
            'status' => false,
            'message' => 'Connection error: ' . $curl_error
        ];
    }
    
    // Decode response
    $result = json_decode($response, true);
    
    // Log response for debugging
    error_log("Paystack API Response (HTTP $http_code): " . json_encode($result));
    
    // Handle non-200 responses
    if ($http_code !== 200) {
        error_log("Paystack API Error: HTTP $http_code");
        return [
            'status' => false,
            'message' => $result['message'] ?? 'API request failed'
        ];
    }
    
    return $result;
}

/**
 * Convert amount from pesewas to GHS
 * 
 * @param int $pesewas - Amount in pesewas
 * @return float - Amount in GHS
 */
function pesewas_to_ghs($pesewas) {
    return $pesewas / 100;
}

/**
 * Convert amount from GHS to pesewas
 * 
 * @param float $ghs - Amount in GHS
 * @return int - Amount in pesewas
 */
function ghs_to_pesewas($ghs) {
    return round($ghs * 100);
}

/**
 * Get currency symbol for display
 * 
 * @param string $currency - Currency code
 * @return string - Currency symbol
 */
function get_currency_symbol($currency = 'GHS') {
    $symbols = [
        'GHS' => '₵',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'NGN' => '₦'
    ];
    
    return $symbols[$currency] ?? $currency;
}

/**
 * Format amount for display
 * 
 * @param float $amount - Amount to format
 * @param string $currency - Currency code
 * @return string - Formatted amount with symbol
 */
function format_currency($amount, $currency = 'GHS') {
    $symbol = get_currency_symbol($currency);
    return $symbol . number_format($amount, 2);
}

/**
 * Validate payment reference format
 * 
 * @param string $reference - Reference to validate
 * @return bool - True if valid format
 */
function is_valid_payment_reference($reference) {
    // Check if reference matches expected patterns
    return preg_match('/^(VCG|PAY|SUB)-\d+-\d+$/', $reference) === 1;
}

/**
 * Generate unique payment reference
 * 
 * @param string $prefix - Prefix for reference (VCG, SUB, etc)
 * @param int $customer_id - Customer ID
 * @return string - Unique reference
 */
function generate_payment_reference($prefix = 'PAY', $customer_id = null) {
    $customer_part = $customer_id ?? 'GUEST';
    return $prefix . '-' . $customer_part . '-' . time();
}
?>
