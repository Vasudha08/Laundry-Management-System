<?php
// Start the session
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect to the login page or home page
header("Location: index.php"); // Change to your actual login page if needed
exit();
?>
