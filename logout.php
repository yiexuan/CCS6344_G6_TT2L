<?php
session_start();
session_unset(); // Clear the session
session_destroy(); // Destroy the session

// Redirect to login page after logout
header('Location: login.php');
exit();
?>
