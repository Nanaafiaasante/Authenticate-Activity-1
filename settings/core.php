<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//for header redirection
ob_start();

//function to check for login
function check_login() {
    return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
}

//alias function for check_login
function is_logged_in() {
    return check_login();
}

//function to get user ID
function get_user_id() {
    if (check_login()) {
        return $_SESSION['customer_id'];
    }
    return null;
}

//function to check for role (admin, customer, etc)
function check_admin() {
    return check_login() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

//function to check if user has administrative privileges
function has_admin_privileges() {
    return check_login() && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

?>