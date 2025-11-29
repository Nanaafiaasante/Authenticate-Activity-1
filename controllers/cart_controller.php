<?php
/**
 * Cart Controller
 * Handles cart operations by wrapping cart_class methods
 * Acts as middleware between action scripts and the cart model
 */

require_once(__DIR__ . '/../classes/cart_class.php');

/**
 * Add item to cart
 * 
 * @param int $product_id - The product ID
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @param int $quantity - The quantity to add
 * @return bool - True on success, false on failure
 */
function add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity, $selected_items = [])
{
    $cart = new Cart();
    return $cart->add_to_cart($product_id, $customer_id, $ip_address, $quantity, $selected_items);
}

/**
 * Update cart item quantity
 * 
 * @param int $product_id - The product ID
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @param int $quantity - The new quantity
 * @return bool - True on success, false on failure
 */
function update_cart_item_ctr($product_id, $customer_id, $ip_address, $quantity)
{
    $cart = new Cart();
    return $cart->update_cart_quantity($product_id, $customer_id, $ip_address, $quantity);
}

/**
 * Remove item from cart
 * 
 * @param int $product_id - The product ID
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return bool - True on success, false on failure
 */
function remove_from_cart_ctr($product_id, $customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->remove_from_cart($product_id, $customer_id, $ip_address);
}

/**
 * Get user's cart items
 * 
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return array|false - Array of cart items, false on failure
 */
function get_user_cart_ctr($customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->get_user_cart($customer_id, $ip_address);
}

/**
 * Get cart item count
 * 
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return int - Total items in cart
 */
function get_cart_count_ctr($customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->get_cart_count($customer_id, $ip_address);
}

/**
 * Get cart total amount
 * 
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return float - Total cart amount
 */
function get_cart_total_ctr($customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->get_cart_total($customer_id, $ip_address);
}

/**
 * Empty the entire cart
 * 
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return bool - True on success, false on failure
 */
function empty_cart_ctr($customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->empty_cart($customer_id, $ip_address);
}

/**
 * Check if product exists in cart
 * 
 * @param int $product_id - The product ID
 * @param int $customer_id - The customer ID (null for guest)
 * @param string $ip_address - The IP address
 * @return array|false - Cart item if exists, false otherwise
 */
function check_product_in_cart_ctr($product_id, $customer_id, $ip_address)
{
    $cart = new Cart();
    return $cart->check_product_exists($product_id, $customer_id, $ip_address);
}

/**
 * Transfer guest cart to logged-in user
 * 
 * @param string $ip_address - The IP address
 * @param int $customer_id - The customer ID to transfer to
 * @return bool - True on success, false on failure
 */
function transfer_guest_cart_ctr($ip_address, $customer_id)
{
    $cart = new Cart();
    return $cart->transfer_guest_cart($ip_address, $customer_id);
}

?>
