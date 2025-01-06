<?php
// Start session for role-based checking
session_start();

// Ensure $role is set from the session or another source
if (!isset($_SESSION['role'])) {
    // If the role is not set, show an error and exit
    echo "<p style='color: red;'>Role not found. Please log in again.</p>";
    exit;
}

// Assign the role from session
$role = $_SESSION['role'];

// Include header for consistent navigation
include('header.php');

// Check if the user has the 'admin' role
if ($role != 'admin') {
    // If not admin, show an error message and exit
    echo "<p style='color: red;'>You do not have permission to add customers.</p>";
    exit;
}

// If the user is admin, continue with the customer creation process
include('config_admin.php');
$conn = $conn_admin;

// Check the connection
if ($conn === false) {
    echo "Connection could not be established.<br />";
    echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    exit;
}

// Clear the form submission flag if the form is not submitted
if (!isset($_POST['name'])) {
    unset($_SESSION['form_submitted']);
}

// Initialize variables for success and error messages
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['form_submitted'])) {
    // Collect form data
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $memberPoints = trim($_POST['member_points'] ?? '0');
    $tiers = trim($_POST['tiers'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');

    // Validate inputs
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($gender) || empty($tiers) || empty($status)) {
        $errorMessage = "All fields are required except Member Points and Birthday.";
    } else {
        // Prepare the SQL query to insert the customer
        $query = "INSERT INTO CustomersInfo (Name, Phone, Email, Address, Gender, MemberPoints, Tiers, Status, Birthday)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $name,
            $phone,
            $email,
            $address,
            $gender,
            (int)$memberPoints,  // Ensure points are an integer
            $tiers,
            $status,
            $birthday ? date('Y-m-d', strtotime($birthday)) : null,  // Handle date formatting
        ];

        // Execute the query
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            $errorMessage = "Error inserting data.<br />";
            $errorMessage .= "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        } else {
            $successMessage = "Customer added successfully!";
            sqlsrv_free_stmt($stmt);

            // Set session flag to prevent re-insertion on refresh
            $_SESSION['form_submitted'] = true;

            // Redirect to avoid re-submission on refresh
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Close the database connection
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <link rel="stylesheet" href="add_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Add Customer</h1>
    <form method="POST" action="add_customer.php">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="address">Address:</label>
        <textarea name="address" id="address" required></textarea><br><br>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender">
            <option value="M">Male</option>
            <option value="F">Female</option>
            <option value="O">Other</option>
        </select><br><br>

        <label for="member_points">Member Points:</label>
        <input type="number" name="member_points" id="member_points" value="0"><br><br>

        <label for="tiers">Tiers:</label>
        <select name="tiers" id="tiers" required><br><br>
            <option value="Silver">Silver</option>
            <option value="Gold">Gold</option>
            <option value="Platinium">Platinium</option>
            <option value="Diamond">Diamond</option>
        </select><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="">Select</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select><br><br>

        <label for="birthday">Birthday (optional):</label>
        <input type="date" name="birthday" id="birthday"><br><br>

        <button type="submit">Add Customer</button>
    </form>

    <!-- Display success or error messages -->
    <?php
    if (!empty($successMessage)) {
        echo "<p style='color: green;'>$successMessage</p>";
    }
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?>
</body>
</html>
