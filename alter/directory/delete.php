<!DOCTYPE html>
<html lang="en">
<head>
    <title>Delete Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin: 5px 0;
        }

        form {
            display: inline-block;
            margin-top: 10px;
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

        a {
            margin-left: 10px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
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

            // Display the record details
            ?>
            <h2>Delete Record</h2>
            <p>Are you sure you want to delete the following record?</p>
            <p>Location: <?php echo $location; ?></p>
            <p>Assigned To: <?php echo $assignedTo; ?></p>
            <p>IP Phone No: <?php echo $ipPhoneNo; ?></p>
            <p>IP Address: <?php echo $ipAddress; ?></p>
            <p>Model No: <?php echo $modelNo; ?></p>
            <p>Serial No: <?php echo $serialNo; ?></p>
            <form action="delete.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit">Confirm Delete</button>
            </form>
            <a href="../../directory.php">Cancel</a>
            <?php
        } else {
            echo "Record not found.";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Invalid record ID.";
    }

    // Check if the delete form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $id = $_POST['id'];

        // Prepare and execute the DELETE statement
        $deleteQuery = "DELETE FROM Directory WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
