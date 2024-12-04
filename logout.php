<?php
session_start();  // Start or resume the current session

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or home page
header("Location: login.php");
exit();
?>
