<?php
// Include the db.conf file to retrieve database credentials
include('db.conf');

// Create a new database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a file was uploaded
if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
    // Get the temporary file name
    $tmpFileName = $_FILES['csvFile']['tmp_name'];

    // Open the CSV file for reading
    if (($handle = fopen($tmpFileName, "r")) !== false) {
        // Skip the header row
        fgetcsv($handle);

        // Prepare the SQL statement
        $sql = "INSERT INTO masterip (SSA, DISTRICT, LOCATION, PS_HO, WAN_ID, NOC_End, Office_End, LAN_ID, LAN_GATEWAY) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bind_param("sssssssss", $ssa, $district, $location, $psHo, $wanId, $nocEnd, $officeEnd, $lanId, $lanGateway);

        // Read the CSV data line by line
        while (($data = fgetcsv($handle)) !== false) {
            // Handle missing or empty values in the CSV
            $ssa = isset($data[0]) ? $data[0] : '';
            $district = isset($data[1]) ? $data[1] : '';
            $location = isset($data[2]) ? $data[2] : '';
            $psHo = isset($data[3]) ? $data[3] : '';
            $wanId = isset($data[4]) ? $data[4] : '';
            $nocEnd = isset($data[5]) ? $data[5] : '';
            $officeEnd = isset($data[6]) ? $data[6] : '';
            $lanId = isset($data[7]) ? $data[7] : '';
            $lanGateway = isset($data[8]) ? $data[8] : '';

            // Execute the statement
            $stmt->execute();
        }

        // Close the prepared statement
        $stmt->close();

        // Close the CSV file
        fclose($handle);

        // Redirect back to the index.php page with success message
        header("Location: index.php?import=success");
        exit();
    }
}

// Redirect back to the index.php page with error message
header("Location: index.php?import=error");
exit();
?>
