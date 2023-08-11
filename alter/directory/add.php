<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Record !</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        button[type="submit"],
        button[type="button"] {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        button[type="submit"]:hover,
        button[type="button"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="../../index.php">Dial-100</a></li>
            <li><a href="../../allipphones.php">District IPT</a></li>			
            <li><a href="../../directory.php">Important Phone Directory</a></li>
            <li><a href="../../dashboard.php">Dashboard</a></li>
        </ul>
    </nav>

    <h1>Add A New Record !</h1>
    
    <!-- Add your HTML form for adding a record here -->
    <form action="add.php" method="POST">
        <!-- Add your form fields here -->
        <label for="location">Location:</label>
        <input type="text" name="location" id="location" required>

        <label for="assignedTo">Assigned To:</label>
        <input type="text" name="assignedTo" id="assignedTo" required>

        <label for="ipPhoneNo">IP Phone No:</label>
        <input type="text" name="ipPhoneNo" id="ipPhoneNo" required>

        <label for="ipAddress">IP Address:</label>
        <input type="text" name="ipAddress" id="ipAddress" required>

        <label for="modelNo">Model No:</label>
        <input type="text" name="modelNo" id="modelNo" required>

        <label for="serialNo">Serial No:</label>
        <input type="text" name="serialNo" id="serialNo" required>

        <button type="submit">Add</button>
        <button type="button" onclick="location.href='../../directory.php'">Cancel</button>
        <button type="button" onclick="location.href='../../index.php'">Home</button>
    </form>

    <?php
    // Include the db.conf file to retrieve database credentials
    include('../../db.conf');

    // Create a new database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $location = $_POST['location'];
        $assignedTo = $_POST['assignedTo'];
        $ipPhoneNo = $_POST['ipPhoneNo'];
        $ipAddress = $_POST['ipAddress'];
        $modelNo = $_POST['modelNo'];
        $serialNo = $_POST['serialNo'];

        // Prepare and execute the INSERT statement
        $insertQuery = "INSERT INTO Directory (Location, Assigned_To, IP_Phone_No, IP_Address, Model_No, Serial_Number) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssss", $location, $assignedTo, $ipPhoneNo, $ipAddress, $modelNo, $serialNo);
        
        if ($stmt->execute()) {
            echo "Record added successfully.";
        } else {
            echo "Error adding record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>

    <footer>
        <p>Developed by VoIP Admin</p>
    </footer>

    <!-- Add your JavaScript code here -->
</body>
</html>
