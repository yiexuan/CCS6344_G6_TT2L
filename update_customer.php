<?php
session_start();
// Ensure the user is logged in and has proper credentials
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<p style='color: red;'>You are not logged in. Please log in to continue.</p>";
    exit;
}

include('config_sales.php');
include('config_admin.php');
include('header.php');

// Initialize variables
$successMessage = "";
$errorMessage = "";

// Ensure the user has the correct role
$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    echo "<p style='color: red;'>You do not have permission to access this page.</p>";
    exit;
}

// Get the CustomerId from the URL
$customerId = intval($_GET['id'] ?? 0);

// Establish connection based on the role
$conn = null;
if ($role == 'admin') {
    include('config_admin.php');
    $conn = $conn_admin; // Use Admin connection
} elseif ($role == 'sales') {
    include('config_sales.php');
    $conn = $conn_sales; // Use Sales connection
} else {
    echo "<p style='color: red;'>Invalid role. Please log in again.</p>";
    exit;
}

// Fetch the customer details
$customer = null;
if ($customerId > 0) {
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

// Handle form submission to update customer details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get customer data from the form
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $memberPoints = $_POST['member_points'] ?? '';
    $tiers = $_POST['tiers'] ?? '';
    $status = $_POST['status'] ?? '';
    $birthday = $_POST['birthday'] ?? '';

    // Validate form inputs
    if (empty($name) || empty($phone) || empty($email) || empty($address)) {
        $errorMessage = "Please fill all the required fields.";
    } else {
        // Prepare the update query
        $updateQuery = "UPDATE CustomersInfo SET 
                            Name = ?, 
                            Phone = ?, 
                            Email = ?, 
                            Address = ?, 
                            Gender = ?, 
                            MemberPoints = ?, 
                            Tiers = ?, 
                            Status = ?, 
                            Birthday = ? 
                        WHERE Id = ?";
        $updateParams = [
            $name,
            $phone,
            $email,
            $address,
            $gender,
            $memberPoints,
            $tiers,
            $status,
            $birthday,
            $customerId
        ];

        // Execute the update query
        $updateStmt = sqlsrv_query($conn, $updateQuery, $updateParams);

        if ($updateStmt === false) {
            $errorMessage = "Error updating customer details.<br />";
            $errorMessage .= "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        } else {
            $successMessage = "Customer details updated successfully.";
            sqlsrv_free_stmt($updateStmt);
        }
    }
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
        <form method="POST" action="update_customer.php?id=<?php echo $customerId; ?>">
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
<select name="gender" id="gender">
    <option value="M" <?php echo ($customer['Gender'] == 'M') ? 'selected' : ''; ?>>Male</option>
    <option value="F" <?php echo ($customer['Gender'] == 'F') ? 'selected' : ''; ?>>Female</option>
    <option value="O" <?php echo ($customer['Gender'] == 'O') ? 'selected' : ''; ?>>Other</option>
</select><br><br>

            
            <label for="member_points">Member Points:</label>
            <input type="number" name="member_points" id="member_points" value="<?php echo htmlspecialchars($customer['MemberPoints']); ?>"><br><br>
            
            <label for="tiers">Tiers:</label>
<select name="tiers" id="tiers" required>
    <option value="Silver" <?php echo ($customer['Tiers'] == 'Silver') ? 'selected' : ''; ?>>Silver</option>
    <option value="Gold" <?php echo ($customer['Tiers'] == 'Gold') ? 'selected' : ''; ?>>Gold</option>
    <option value="Platinium" <?php echo ($customer['Tiers'] == 'Platinium') ? 'selected' : ''; ?>>Platinium</option>
    <option value="Diamond" <?php echo ($customer['Tiers'] == 'Diamond') ? 'selected' : ''; ?>>Diamond</option>
</select><br><br>

<label for="status">Status:</label>
<select name="status" id="status" required>
    <option value="Active" <?php echo ($customer['Status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
    <option value="Inactive" <?php echo ($customer['Status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
</select><br><br>


            
            <label for="birthday">Birthday:</label>
            <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($customer['Birthday']->format('Y-m-d')); ?>"><br><br>
            
            <button type="submit">Update Customer</button>
        </form>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
