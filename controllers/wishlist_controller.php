<?php
/**
 * Wishlist Controller
 * Business logic layer for wishlist operations
 */

require_once(__DIR__ . '/../classes/wishlist_class.php');

/**
 * Add product to wishlist
 */
function add_to_wishlist_ctr($customer_id, $product_id)
{
    $wishlist = new Wishlist();
    return $wishlist->add_to_wishlist($customer_id, $product_id);
}

/**
 * Remove product from wishlist
 */
function remove_from_wishlist_ctr($customer_id, $product_id)
{
    $wishlist = new Wishlist();
    return $wishlist->remove_from_wishlist($customer_id, $product_id);
}

/**
 * Get all wishlist items for a customer
 */
function get_wishlist_items_ctr($customer_id)
{
    $wishlist = new Wishlist();
    return $wishlist->get_wishlist_items($customer_id);
}

/**
 * Get wishlist item count
 */
function get_wishlist_count_ctr($customer_id)
{
    $wishlist = new Wishlist();
    return $wishlist->get_wishlist_count($customer_id);
}

/**
 * Check if product is in wishlist
 */
function check_in_wishlist_ctr($customer_id, $product_id)
{
    $wishlist = new Wishlist();
    return $wishlist->check_in_wishlist($customer_id, $product_id);
}
?>
