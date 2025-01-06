<?php
$serverName = "192.168.100.79";
// $serverName = "192.168.0.10";
$connectionInfo = array(
    "Database" => "TestDb",
    "UID" => "Sales",
    "PWD" => 'Pa$$w0rd',
    "Encrypt" => "yes",
    "TrustServerCertificate" => "yes"
);

$conn_sales = sqlsrv_connect($serverName, $connectionInfo);

if ($conn_sales) {
    // echo "Connection established.<br />";

    // Query to fetch data from CustomersInfo table
  
} else {
    echo "Connection could not be established.<br />";
    die(print_r(sqlsrv_errors(), true));
}

// sqlsrv_close($conn);
?>