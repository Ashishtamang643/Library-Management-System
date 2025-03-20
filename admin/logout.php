<?php
session_start(); // Start session if not already started
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // Redirect to homepage
exit(); // Ensure script stops executing after redirection
?>
