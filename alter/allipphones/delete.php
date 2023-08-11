<?php
// Include the db.conf file to retrieve database credentials
include('db.conf');

// Create a new database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the record ID is provided
if (!isset($_GET['id'])) {
    // Redirect to the main page if the ID is not provided
    header("Location: allipphones.php");
    exit();
}

// Get the record ID from the query parameters
$id = $_GET['id'];

// Fetch the record from the database
$query = "SELECT * FROM all_ip_phones WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    // Redirect to the main page if the record is not found
    header("Location: allipphones.php");
    exit();
}

// Retrieve the record details
$row = $result->fetch_assoc();
$bsnlSSA = $row['BSNL_SSA'];
$district = $row['DISTRICT'];
$location = $row['LOCATION'];
$psho = $row['PSHO'];
$ipAddress = $row['IPAddress'];
$ipPhoneNo = $row['IPPhoneNo'];
$modelNo = $row['ModelNo'];
$serialNo = $row['SerialNo'];
$installationStatus = $row['InstallationStatus'];

// Check if the delete request is confirmed
if (isset($_POST['delete'])) {
    // Delete the record from the database
    $deleteQuery = "DELETE FROM all_ip_phones WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        // Redirect to the main page after successful deletion
        header("Location: allipphones.php");
        exit();
    } else {
        echo "Error deleting record: " . $deleteStmt->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <style>
        body {
            font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-bottom: 20px;
        }

        .record-details {
            margin-bottom: 20px;
        }

        .record-details label {
            font-weight: bold;
        }

        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            font-weight: bold;
            font-size: medium;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            text-decoration: none;
        }

        .delete-btn {
            display: inline-block;
            padding: 5px 10px;
            font-weight: bold;
            font-size: medium;
            color: white;
            background-color: #dc3545;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete Record</h1>
        <a href="../../allipphones.php" class="action-btn">Back</a>

        <div class="record-details">
            <label for="bsnlSSA">BSNL SSA:</label>
            <p><?php echo $bsnlSSA; ?></p>

            <label for="district">District:</label>
            <p><?php echo $district; ?></p>

            <label for="location">Location:</label>
            <p><?php echo $location; ?></p>

            <label for="psho">PS/HO:</label>
            <p><?php echo $psho; ?></p>

            <label for="ipAddress">IP Address:</label>
            <p><?php echo $ipAddress; ?></p>

            <label for="ipPhoneNo">IP Phone No:</label>
            <p><?php echo $ipPhoneNo; ?></p>

            <label for="modelNo">Model No:</label>
            <p><?php echo $modelNo; ?></p>

            <label for="serialNo">Serial No:</label>
            <p><?php echo $serialNo; ?></p>

            <label for="installationStatus">Installation Status:</label>
            <p><?php echo $installationStatus; ?></p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $id; ?>" method="POST">
            <p>Are you sure you want to delete this record?</p>
            <button type="submit" name="delete" class="delete-btn">Delete</button>
        </form>
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
