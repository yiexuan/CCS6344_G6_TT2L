<?php
// session_start();
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
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Table</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="view_table.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .edit-button {
            background-color: green;
        }
        .delete-button {
            background-color: red;
        }
    </style>
</head>
<body>
    <h1>Customer Information</h1>

    <?php
    if ($conn) {
        // Query to fetch data from CustomersInfo table
        $query = "SELECT * FROM CustomersInfo";  
        $getResults = sqlsrv_query($conn, $query); 

        if ($getResults === false) {
            echo "Error (sqlsrv_query): " . print_r(sqlsrv_errors(), true);
            exit;
        }

        // Fetch and display the results
        echo "<table id='resizableTable'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Id</th>";
        echo "<th>Name</th>";
        echo "<th>Phone</th>";
        echo "<th>Email</th>";
        echo "<th>Address</th>";
        echo "<th>Gender</th>";
        echo "<th>MemberPoints</th>";
        echo "<th>Tiers</th>";
        echo "<th>Status</th>";
        echo "<th>Birthday</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
            echo "<td>" . htmlspecialchars($row['MemberPoints']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Tiers']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
        
            // Check if 'Birthday' is a DateTime object
            $birthday = $row['Birthday'] instanceof DateTime 
                ? $row['Birthday']->format('Y-m-d') // Format the date as 'YYYY-MM-DD'
                : htmlspecialchars($row['Birthday']); // Handle other types or null values
        
            echo "<td>" . $birthday . "</td>";
            echo "<td class='action-buttons'>";
            echo "<a href='update_customer.php?id=" . htmlspecialchars($row['Id']) . "' class='edit-button'><i class='fas fa-edit'></i></a>";
            echo "<a href='delete_customer.php?id=" . htmlspecialchars($row['Id']) . "' class='delete-button'><i class='fas fa-trash'></i></a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        // Free the result resource
        sqlsrv_free_stmt($getResults);
    } else {
        echo "Connection could not be established.<br />";
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($conn);
    ?>
</body>
</html>