<?php
session_start();
// Ensure the role is available in the session
if (!isset($_SESSION['role'])) {
    echo "<p style='color: red;'>Role not found. Please log in again.</p>";
    exit;
}

// Get the role from the session
$role = $_SESSION['role'];

// Include header for consistent navigation
include('header.php');

// Check if the user has the 'admin' role
if ($role != 'admin') {
    // If not admin, show an error message and exit
    echo "<p style='color: red;'>You do not have permission to delete customers.</p>";
    exit;
}

// Include the database connection based on the role
include('config_admin.php');
$conn = $conn_admin;

// Check if the connection is successful
if ($conn === false) {
    echo "Connection could not be established.<br />";
    echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    exit;
}

// Initialize variables
$successMessage = "";
$errorMessage = "";

// Get the CustomerId from the URL
$customerId = trim($_GET['id'] ?? '');

if (empty($customerId) || !is_numeric($customerId)) {
    $errorMessage = "Invalid Customer ID.";
} else {
    // Prepare and execute the DELETE query
    $query = "DELETE FROM CustomersInfo WHERE Id = ?";
    $params = [$customerId];

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $errorMessage = "Error deleting customer.<br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $rowsAffected = sqlsrv_rows_affected($stmt);
        if ($rowsAffected > 0) {
            $successMessage = "Customer with ID $customerId deleted successfully!";
        } else {
            $errorMessage = "No customer found with ID $customerId.";
        }
        sqlsrv_free_stmt($stmt);
    }
}

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Delete Customer</h1>
    <?php
    if (!empty($successMessage)) {
        echo "<p style='color: green;'>$successMessage</p>";
    }
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
