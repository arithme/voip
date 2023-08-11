<?php
// Include the db.conf file to retrieve database credentials
include('db.conf');

// Create a new database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Specify the IP phone models
$models = ['Model-3905', 'Model-6941', 'Model-7821'];

// Query to get the count of the specified IP phone models by location
$query = "SELECT Location, ";
$query .= "SUM(CASE WHEN Model = 'Model-3905' THEN 1 ELSE 0 END) AS `Model-3905`, ";
$query .= "SUM(CASE WHEN Model = 'Model-6941' THEN 1 ELSE 0 END) AS `Model-6941`, ";
$query .= "SUM(CASE WHEN Model = 'Model-7821' THEN 1 ELSE 0 END) AS `Model-7821` ";
$query .= "FROM ip_phones ";
$query .= "GROUP BY Location WITH ROLLUP";

// Execute the query
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "Location\tModel-3905\tModel-6941\tModel-7821\n";
    
    while ($row = $result->fetch_assoc()) {
        echo $row['Location'] . "\t" . $row['Model-3905'] . "\t" . $row['Model-6941'] . "\t" . $row['Model-7821'] . "\n";
    }
}

// Close the database connection
$conn->close();
?>
