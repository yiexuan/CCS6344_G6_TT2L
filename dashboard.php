<?php
session_start();
include('config_sales.php');
include('config_admin.php');
include('header.php');

// Check if the admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Initialize role and connection variables
$role = $_SESSION['role'] ?? ''; // Get role from session (default empty string)
$conn = null; // Declare the $conn variable

// Set the title based on the role
$pageTitle = '';
if ($role == 'admin') {
    $pageTitle = 'Admin Dashboard';
} elseif ($role == 'sales') {
    $pageTitle = 'Sales Dashboard';
} else {
    $pageTitle = 'User Dashboard'; // Default title for undefined roles
}

// Check the role and set the appropriate database connection
if ($role == 'admin') {
    include('config_admin.php');
    $conn = $conn_admin; // Use the Admin connection
} elseif ($role == 'sales') {
    include('config_sales.php');
    $conn = $conn_sales; // Use the Sales connection
} else {
    $error_message = "Invalid role!";
    echo "<p style='color: red;'>$error_message</p>";
    exit();
}

// Initialize data variables
$totalCustomers = 0;
$activeCustomers = 0;
$inactiveCustomers = 0;
$birthdaysThisMonth = 0;

if ($conn) {
    // Fetch customer data using queries
    $queries = [
        "total" => "SELECT COUNT(*) AS total FROM CustomersInfo",
        "active" => "SELECT COUNT(*) AS total FROM CustomersInfo WHERE Status = 'Active'",
        "inactive" => "SELECT COUNT(*) AS total FROM CustomersInfo WHERE Status = 'Inactive'",
        "birthdays" => "SELECT COUNT(*) AS total FROM CustomersInfo WHERE MONTH(Birthday) = MONTH(GETDATE())"
    ];

    // Execute each query and assign results to variables
    foreach ($queries as $key => $query) {
        $result = sqlsrv_query($conn, $query);
        if ($result !== false) {
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            ${$key . 'Customers'} = $row['total'];
            sqlsrv_free_stmt($result);
        } else {
            echo "Error executing query: $key <br />";
            die(print_r(sqlsrv_errors(), true));
        }
    }
} else {
    echo "Connection could not be established.<br />";
    die(print_r(sqlsrv_errors(), true));
}

// Initialize search variables
$searchResults = "";
$errorMessage = "";

// Handle the form submission for searching customers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = trim($_POST['id'] ?? '');
    $customerName = trim($_POST['name'] ?? '');

    if (empty($customerId) && empty($customerName)) {
        $errorMessage = "Please enter either a Customer ID or a Customer Name to search.";
    } else {
        // Prepare SQL query for customer search
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

        // Process and display search results
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
    <title><?php echo $pageTitle; ?></title> <!-- Dynamic title based on role -->
    <link rel="stylesheet" href="dashboard.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="dashboard-container">
        <h1><?php echo $pageTitle; ?></h1> <!-- Dynamic title in the page content -->
        <div class="key-data">
            <div class="data-block">
                <p>Total Customers</p>
                <p><?php echo $totalCustomers; ?></p>
            </div>
            <div class="data-block">
                <p>Active Customers</p>
                <p><?php echo $activeCustomers; ?></p>
            </div>
            <div class="data-block">
                <p>Inactive Customers</p>
                <p><?php echo $inactiveCustomers; ?></p>
            </div>
            <div class="data-block">
                <p>Customers with Birthdays This Month</p>
                <p><?php echo $birthdaysThisMonth; ?></p>
            </div>
        </div>
        <button class="add-customer-btn" onclick="window.location.href='add_customer.php'">Add Customer</button>
        <div class="customer-table">
            <div class="search-bar">
                <form method="POST" action="dashboard.php">
                    <label for="id">Customer ID:</label>
                    <input type="text" name="id" id="id" placeholder="Enter Customer ID"><br><br>
                    
                    <label for="name">Customer Name:</label>
                    <input type="text" name="name" id="name" placeholder="Enter Customer Name"><br><br>
                    
                    <button type="submit">Search</button>
                </form>
            </div>
            <?php
            if (!empty($errorMessage)) {
                echo "<p style='color: red;'>$errorMessage</p>";
            }
            ?>
            <h2>Search Results</h2>
            <?php echo $searchResults; ?>
            <?php include('view_table.php'); ?>
            <?php include('view_auditlog.php'); ?>
        </div>
    </div>
</body>
</html>
