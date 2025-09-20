<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with a message
header('Location: login.php?message=logged_out');
exit();
?>