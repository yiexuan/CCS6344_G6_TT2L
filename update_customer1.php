<?php
session_start();
// Include the database connection from config.php
// include('config.php');
// include('config_sales.php');
// include('config_admin.php');

// Ensure $role is set from the session or another source
if (!isset($_SESSION['role'])) {
    // If the role is not set, show an error and exit
    echo "<p style='color: red;'>Role not found. Please log in again.</p>";
    exit;
}

// Assign the role from session
$role = $_SESSION['role'];

include('header.php');


if ($role == 'admin') {
    // Include the Admin config file
    include('config_admin.php');
    $conn = $conn_admin; // Use the Admin connection
} elseif ($role == 'sales') {
    // Include the Sales config file
    include('config_sales.php');
    $conn = $conn_sales; // Use the Sales connection
} else {
    $error_message = "Invalid role!";
}


// Initialize variables
$successMessage = "";
$errorMessage = "";
$customer = null;

// Get the CustomerId from the URL
$customerId = intval($_GET['id'] ?? 0);

if ($customerId > 0) {
    // Fetch customer details
    $query = "SELECT * FROM CustomersInfo WHERE Id = ?";
    $params = [$customerId];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $errorMessage = "Error fetching customer details.<br />";
        $errorMessage .= "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $customer = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$customer) {
            $errorMessage = "Customer not found.";
        }
        sqlsrv_free_stmt($stmt);
    }
} else {
    $errorMessage = "Invalid Customer ID.";
}

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
    <link rel="stylesheet" href="update_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Update Customer</h1>
    <?php
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    if (!empty($successMessage)) {
        echo "<p style='color: green;'>$successMessage</p>";
    }
    ?>

    <?php if ($customer): ?>
        <form method="POST" action="update_customer.php">
            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['Id']); ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($customer['Name']); ?>"><br><br>
            
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($customer['Phone']); ?>"><br><br>
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($customer['Email']); ?>"><br><br>
            
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($customer['Address']); ?>"><br><br>
            
            <label for="gender">Gender:</label>
            <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($customer['Gender']); ?>"><br><br>
            
            <label for="member_points">Member Points:</label>
            <input type="number" name="member_points" id="member_points" value="<?php echo htmlspecialchars($customer['MemberPoints']); ?>"><br><br>
            
            <label for="tiers">Tiers:</label>
            <input type="text" name="tiers" id="tiers" value="<?php echo htmlspecialchars($customer['Tiers']); ?>"><br><br>
            
            <label for="status">Status:</label>
            <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($customer['Status']); ?>"><br><br>
            
            <label for="birthday">Birthday:</label>
            <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($customer['Birthday']->format('Y-m-d')); ?>"><br><br>
            
            <button type="submit">Update Customer</button>
        </form>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>