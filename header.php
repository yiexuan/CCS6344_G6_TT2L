<?php
// session_start();

// Check if the admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}
?>

<!-- Navigation Bar (Header) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="header.css"> <!-- Link to your CSS file -->
</head>
<body>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_customer.php">Add Customer</a></li>
        </ul>
        <ul>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>
</body>
</html>
