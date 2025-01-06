<?php
session_start();
// Include the database connection from config.php
// include('config.php');
include('config_sales.php');
include('config_admin.php');
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

if ($conn === false) {
    echo "Connection could not be established.<br />";
    echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    exit;
}

// Initialize variables
$searchResults = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get inputs from the form
    $customerId = trim($_POST['id'] ?? '');
    $customerName = trim($_POST['name'] ?? '');

    if (empty($customerId) && empty($customerName)) {
        $errorMessage = "Please enter either a Customer ID or a Customer Name to search.";
    } else {
        // Prepare the SQL query with placeholders
        $query = "SELECT * FROM CustomersInfo WHERE 1=1";
        $params = [];

        if (!empty($customerId)) {
            $query .= " AND Id = ?";
            $params[] = (int)$customerId; // Ensure ID is an integer
        }

        if (!empty($customerName)) {
            $query .= " AND Name LIKE ?";
            $params[] = '%' . $customerName . '%'; // Allow partial matches
        }

        // Execute the query securely
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            echo "Error in SQL query.<br />";
            echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
            sqlsrv_close($conn);
            exit;
        }

        // Process results
        if (sqlsrv_has_rows($stmt)) {
            $searchResults = "<table border='1'>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Member Points</th>
                    <th>Tiers</th>
                    <th>Status</th>
                    <th>Birthday</th>
                </tr>";
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $searchResults .= "<tr>
                    <td>" . htmlspecialchars($row['Id']) . "</td>
                    <td>" . htmlspecialchars($row['Name']) . "</td>
                    <td>" . htmlspecialchars($row['Phone']) . "</td>
                    <td>" . htmlspecialchars($row['Email']) . "</td>
                    <td>" . htmlspecialchars($row['Address']) . "</td>
                    <td>" . htmlspecialchars($row['Gender']) . "</td>
                    <td>" . htmlspecialchars($row['MemberPoints']) . "</td>
                    <td>" . htmlspecialchars($row['Tiers']) . "</td>
                    <td>" . htmlspecialchars($row['Status']) . "</td>
                    <td>" . ($row['Birthday'] instanceof DateTime ? $row['Birthday']->format('Y-m-d') : htmlspecialchars($row['Birthday'])) . "</td>
                </tr>";
            }
            $searchResults .= "</table>";
        } else {
            $searchResults = "<p>No customer found matching the criteria.</p>";
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
    <title>Find Customer</title>
    <link rel="stylesheet" href="find_customer.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Find Customer</h1>
    <form method="POST" action="find_customer.php">
        <label for="id">Customer ID:</label>
        <input type="text" name="id" id="id" placeholder="Enter Customer ID"><br><br>
        
        <label for="name">Customer Name:</label>
        <input type="text" name="name" id="name" placeholder="Enter Customer Name"><br><br>
        
        <button type="submit">Search</button>
    </form>

   
    <?php
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?>

    <h2>Search Results</h2>
    <?php echo $searchResults; ?>
</body>
</html>