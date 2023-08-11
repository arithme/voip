<?php
// Include the db.conf file to retrieve database credentials
include('db.conf');

// Create a new database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $ssa = sanitizeInput($_POST['ssa']);
    $district = sanitizeInput($_POST['district']);
    $user = sanitizeInput($_POST['user']);
    $location = sanitizeInput($_POST['location']);
    $ipAddress = sanitizeInput($_POST['ipAddress']);
    $dirNumber = sanitizeInput($_POST['dirNumber']);
    $model = sanitizeInput($_POST['model']);
    $serialNo = sanitizeInput($_POST['serialNo']);
    $installationStatus = sanitizeInput($_POST['installationStatus']);

    // Insert the new record into the database
    $insertQuery = "INSERT INTO ip_phones (SSA, District, User, Location, IPAddress, DirNumber, Model, SerialNo, InstallationStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("sssssssss", $ssa, $district, $user, $location, $ipAddress, $dirNumber, $model, $serialNo, $installationStatus);

    if ($insertStmt->execute()) {
        // Redirect to the main page after successful insert
        header("Location: index.php");
        exit();
    } else {
        echo "Error inserting record: " . $insertStmt->error;
    }
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    
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

        a {
            display: inline-block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Record</h1>
        <a href="../../index.php" class="action-btn">Back</a>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="ssa">SSA:</label>
            <input type="text" name="ssa" id="ssa" required>

            <label for="district">District:</label>
            <input type="text" name="district" id="district" required>

            <label for="user">User:</label>
            <input type="text" name="user" id="user" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required>

            <label for="ipAddress">IP Address:</label>
            <input type="text" name="ipAddress" id="ipAddress" required>

            <label for="dirNumber">Directory Number:</label>
            <input type="text" name="dirNumber" id="dirNumber" required>

            <label for="model">Model:</label>
            <input type="text" name="model" id="model" required>

            <label for="serialNo">Serial Number:</label>
            <input type="text" name="serialNo" id="serialNo" required>

            <label for="installationStatus">Installation Status:</label>
            <input type="text" name="installationStatus" id="installationStatus" required>

            <button type="submit">Add Record</button>
        </form>
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
