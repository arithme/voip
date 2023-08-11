<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>Dashboard</h1>
    </div>

    <div class="content">
        <?php
        // Include the db.conf file to retrieve database credentials
        include('db.conf');

        // Create a new database connection
        $conn = new mysqli($db_host, $db_username, $db_password, $db_database);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to retrieve total unique SSAs
        $query = "SELECT COUNT(DISTINCT SSA) AS totalSSA FROM masterip";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalSSA = $row['totalSSA'];

            echo "<p>Total Unique SSAs: $totalSSA</p>";
        } else {
            echo "<p>No SSAs found.</p>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Add your additional dashboard components and functionality here -->

    <script src="js/script.js"></script>
</body>
</html>
