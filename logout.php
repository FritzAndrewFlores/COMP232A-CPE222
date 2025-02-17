<?php
session_start(); // Start the session (must be at the very beginning)

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page (index.php)
header("Location: index.php");
exit;
?>