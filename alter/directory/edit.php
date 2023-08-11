<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Record</title>
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

        button[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #555;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container button {
            width: 100px;
            margin-right: 10px;
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
    <h1>Edit Record</h1>
    
    <?php
    // Include the db.conf file to retrieve database credentials
    include('../../db.conf');

    // Create a new database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the record ID is provided in the URL
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];

        // Retrieve the record from the database
        $selectQuery = "SELECT * FROM Directory WHERE id = ?";
        $stmt = $conn->prepare($selectQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $location = $row['Location'];
            $assignedTo = $row['Assigned_To'];
            $ipPhoneNo = $row['IP_Phone_No'];
            $ipAddress = $row['IP_Address'];
            $modelNo = $row['Model_No'];
            $serialNo = $row['Serial_Number'];

            // Display the record in the form for editing
            ?>
            <form action="edit.php" method="POST">
                <!-- Add your form fields here -->
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?php echo $location; ?>" required>

                <label for="assignedTo">Assigned To:</label>
                <input type="text" name="assignedTo" id="assignedTo" value="<?php echo $assignedTo; ?>" required>

                <label for="ipPhoneNo">IP Phone No:</label>
                <input type="text" name="ipPhoneNo" id="ipPhoneNo" value="<?php echo $ipPhoneNo; ?>" required>

                <label for="ipAddress">IP Address:</label>
                <input type="text" name="ipAddress" id="ipAddress" value="<?php echo $ipAddress; ?>" required>

                <label for="modelNo">Model No:</label>
                <input type="text" name="modelNo" id="modelNo" value="<?php echo $modelNo; ?>" required>

                <label for="serialNo">Serial No:</label>
                <input type="text" name="serialNo" id="serialNo" value="<?php echo $serialNo; ?>" required>

                <div class="button-container">
                    <button type="submit">Update</button>
                    <button type="button" onclick="location.href='../../directory.php'">Cancel</button>
                    <button type="button" onclick="location.href='../../index.php'">Home</button>
                </div>
            </form>
            <?php
        } else {
            echo "Record not found.";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Invalid record ID.";
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $id = $_POST['id'];
        $location = $_POST['location'];
        $assignedTo = $_POST['assignedTo'];
        $ipPhoneNo = $_POST['ipPhoneNo'];
        $ipAddress = $_POST['ipAddress'];
        $modelNo = $_POST['modelNo'];
        $serialNo = $_POST['serialNo'];

        // Prepare and execute the UPDATE statement
        $updateQuery = "UPDATE Directory SET Location = ?, Assigned_To = ?, IP_Phone_No = ?, IP_Address = ?, Model_No = ?, Serial_Number = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssi", $location, $assignedTo, $ipPhoneNo, $ipAddress, $modelNo, $serialNo, $id);
        
        if ($stmt->execute()) {
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>

    <!-- Add your JavaScript code here -->

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company. All rights reserved.</p>
    </footer>
</body>
</html>
