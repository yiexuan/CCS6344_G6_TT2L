<?php
// Start the session
// session_start();

// Check if the user is logged in and has the 'admin' role
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Include the appropriate configuration file for the database
    include('config_admin.php'); // Assuming using admin connection

    // Connect to the database
    $conn = $conn_admin; // Adjust according to your role-based connection

    if ($conn) {
        // Query to fetch all records from the AuditLog table
        $sql = "SELECT LogID, Action, PerformedBy, TableName, Timestamp FROM AuditLog";
    

        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt === false) {
            die("Error fetching AuditLog: " . print_r(sqlsrv_errors(), true));
        }

        // Display the records in an HTML table
        echo "<h2>Audit Log</h2>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>LogID</th><th>Action</th><th>PerformedBy</th><th>TableName</th><th>Timestamp</th></tr>";

        // Fetch and display each row
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['LogID'] . "</td>";
            echo "<td>" . $row['Action'] . "</td>";
            echo "<td>" . $row['PerformedBy'] . "</td>";
            echo "<td>" . $row['TableName'] . "</td>";
            echo "<td>" . $row['Timestamp']->format('Y-m-d H:i:s') . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Free the statement resources
        sqlsrv_free_stmt($stmt);

        // Close the connection
        sqlsrv_close($conn);
    } else {
        echo "Failed to connect to the database!";
    }
} else {
    // If the user is not logged in or not an admin, show an access denied message
    echo "Access denied. Only administrators can view the audit log.";
}
?>
