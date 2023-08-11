<!DOCTYPE html>
<html lang="en">
<head>
    <title>Important Phone Directory</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    
    <style>
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
		.add-btn {
            display: inline-block;
            padding: 5px 10px;
            font-weight: bold;
            font-size: medium;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

    <h1>Important Phone Directory</h1>
	 <a href="alter/directory/add.php" class="add-btn">Add Record</a>
    <nav>
        <ul>
            <li><a href="index.php">Dial-100</a></li>
            <li><a href="allipphones.php">District IPT</a></li>			
			<li><a href="directory.php">Important Phone Directory</a></li>
			<li><a href="cctns/index.php">CCTNS</a></li>
			<li><a href="master/index.php">Master IP</a></li>
			<li><a href="dashboard.php">Dashboard</a></li>
			
            
            <!-- Add more menu items as needed -->
        </ul>
        <form action="directory.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </form>
    </nav>


    <?php
    // Include the db.conf file to retrieve database credentials
    include('db.conf');

    // Create a new database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Pagination variables
    $limit = 10; // Number of records to display per page
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
    $offset = ($currentPage - 1) * $limit; // Offset for SQL query

    ?>

    <table>
        <tr>
            <th colspan="8">
                <?php
                // Retrieve search query if provided
                $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

                // Modify the SELECT query to include the search filter and pagination
                $selectDataQuery = "SELECT COUNT(*) AS total FROM Directory WHERE 
                    Location LIKE '%$searchQuery%' OR 
                    Assigned_To LIKE '%$searchQuery%' OR 
                    IP_Phone_No LIKE '%$searchQuery%' OR 
                    IP_Address LIKE '%$searchQuery%' OR 
                    Model_No LIKE '%$searchQuery%' OR 
                    Serial_Number LIKE '%$searchQuery%'";

                // Execute the query to get the total records count
                $result = $conn->query($selectDataQuery);

                if ($result && $result->num_rows > 0) {
                    $totalRecords = $result->fetch_assoc()['total'];
                    echo "Total Records Found: " . $totalRecords;
                } else {
                    echo "No records found.";
                }
                ?>
            </th>
        </tr>
        <tr>
            <th>S No</th>
            <th>Location</th>
            <th>Assigned To</th>
            <th>IP Phone No</th>
            <th>IP Address</th>
            <th>Model No</th>
            <th>Serial No</th>
            <th>Action</th>
        </tr>

        <?php
        // Retrieve search query if provided
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

        // Modify the SELECT query to include the search filter and pagination
        $selectDataQuery = "SELECT * FROM Directory WHERE 
            Location LIKE '%$searchQuery%' OR 
            Assigned_To LIKE '%$searchQuery%' OR 
            IP_Phone_No LIKE '%$searchQuery%' OR 
            IP_Address LIKE '%$searchQuery%' OR 
            Model_No LIKE '%$searchQuery%' OR 
            Serial_Number LIKE '%$searchQuery%' LIMIT $limit OFFSET $offset";

        // Execute the query
        $result = $conn->query($selectDataQuery);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $location = $row['Location'];
                $assignedTo = $row['Assigned_To'];
                $ipPhoneNo = $row['IP_Phone_No'];
                $ipAddress = $row['IP_Address'];
                $modelNo = $row['Model_No'];
                $serialNo = $row['Serial_Number'];

                ?>

                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $location; ?></td>
                    <td><?php echo $assignedTo; ?></td>
                    <td><?php echo $ipPhoneNo; ?></td>
                    <td><a href="http://<?php echo $ipAddress; ?>" target="_blank" style="color: white;"><?php echo $ipAddress; ?></a></td>
                    <td><?php echo $modelNo; ?></td>
                    <td><?php echo $serialNo; ?></td>
                   	<td>
						
						<a href="alter/directory/edit.php?id=<?php echo $id; ?>" class="action-btn" title="Edit">&#x270E;</a>
						<a href="alter/directory/delete.php?id=<?php echo $id; ?>" class="action-btn" title="Edit">&#x1F5D1;</a>
					</td>
                </tr>
            <?php
            }
        } else {
            echo "<tr><td colspan='8'>No records found.</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>

    <div class="pagination">
        <?php if ($currentPage > 1) { ?>
            <a href="directory.php?page=<?php echo $currentPage - 1; ?>">&lt;&lt; Previous</a>
        <?php } ?>

        <?php if ($result && $result->num_rows >= $limit) { ?>
            <a href="directory.php?page=<?php echo $currentPage + 1; ?>">Next &gt;&gt;</a>
        <?php } ?>
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
