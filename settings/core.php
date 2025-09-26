// Settings/core.php
<?php
session_start();

//for header redirection
ob_start();

//function to check for login
function check_login() {
    return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
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

// Auto-redirect if not logged in (optional - remove if not needed)
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login/login.php");
    exit;
}

?>