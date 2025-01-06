<?php
session_start();
include('config_sales.php');
include('config_admin.php');

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and trim inputs
    $inputUsername = trim($_POST['username']);
    $inputPassword = trim($_POST['password']);
    $role = $_POST['role']; // Get the role (admin or sales) from the form

    // Define the $ipAddress variable
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // After validating login credentials
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

    // Check if the connection was successful
    if ($conn) {
        // Query to fetch the user record for login along with their role
        $sql = "SELECT Password, Role FROM Users WHERE Username = ?";
        $params = array($inputUsername);
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt === false) {
            die("Prepare Error: " . print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            if (sqlsrv_has_rows($stmt)) {
                // Fetch the user record, including the role and hashed password
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $hashedPassword = $row['Password']; // The password is already in VARBINARY format
                $userRole = $row['Role']; // The user's role in the database

                // Check if the provided role matches the user’s actual role
                if ($userRole !== $role) {
                    $error_message = "Incorrect role for the user!"; // Role mismatch error
                } else {
                    // Generate the hash of the input password using SHA-256
                    $inputPasswordHash = hash('sha256', $inputPassword, true); // `true` to get raw binary data

                    // Verify the entered password against the hashed password
                    if (hash_equals($hashedPassword, $inputPasswordHash)) {
                        $_SESSION['logged_in'] = true;
                        $_SESSION['role'] = $role; // Store the role in session
                        header('Location: dashboard.php'); // Redirect to the dashboard

                        // Log the successful login attempt in AuditLog
                        $action = 'LOGIN SUCCESS';
                        $tableName = 'Users'; // Specify the table name involved in the action
                        logLoginAttempt($conn, $inputUsername, $action, $tableName);
                        exit();
                    } else {
                        $error_message = "Invalid credentials!"; // Incorrect password
                        
                        // Log the failed login attempt
                        $action = 'LOGIN FAILED';
                        $tableName = 'Users'; // Specify the table name involved in the action
                        logLoginAttempt($conn, $inputUsername, $action, $tableName);
                    }
                }
            } else {
                $error_message = "Invalid credentials!"; // Username not found
                
                // Log the failed login attempt
                $action = 'LOGIN FAILED';
                $tableName = 'Users'; // Specify the table name involved in the action
                logLoginAttempt($conn, $inputUsername, $action, $tableName);
            }
        } else {
            die("Execute Error: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt); // Free statement resources
        sqlsrv_close($conn); // Close the connection
    } else {
        $error_message = "Failed to connect to the database!";
    }
}

// Function to log the login attempt
function logLoginAttempt($conn, $username, $action, $tableName) {
    $sql = "INSERT INTO AuditLog (Action, PerformedBy, TableName) VALUES (?, ?, ?)";
    $params = array($action, $username, $tableName);
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if ($stmt === false) {
        die("Prepare Error: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_execute($stmt) === false) {
        die("Execute Error: " . print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($stmt); // Free statement resources
}
?>

<!-- HTML for the login form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <form action="login.php" method="POST">
        <h2>Login</h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="admin">Admin</option>
            <option value="sales">Sales</option>
        </select><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
