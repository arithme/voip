<!DOCTYPE html>
<html lang="en">
<head>
    <title>District IP Phones</title>
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

        .record-count {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form label {
            margin-right: 10px;
        }

        .filter-form select,
        .filter-form input[type="text"] {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-right: 10px;
        }

        .filter-form button[type="submit"] {
            padding: 5px 10px;
            font-weight: bold;
            font-size: medium;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-form button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>District IP Phones</h1>
    <a href="alter/allipphones/add.php" class="add-btn">Add A New Record!</a>
    <nav>
        <ul>
            <li><a href="index.php">Dial-100</a></li>
            <li><a href="allipphones.php">District IPT</a></li>
            <li><a href="directory.php">Important Phone Directory</a></li>
			<li><a href="dashboard.php">Dashboard</a></li>
			<li><a href="cctns/index.php">CCTNS</a></li>
			<li><a href="master/index.php">Master IP</a></li>
        </ul>
        <form action="allipphones.php" method="GET" class="search-form">
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

    // Function to sanitize input data
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Filter variables
    $selectedSSA = isset($_GET['ssa']) ? sanitizeInput($_GET['ssa']) : '';
    $selectedDistrict = isset($_GET['district']) ? sanitizeInput($_GET['district']) : '';

    // Pagination variables
    $limit = 10; // Number of records to display per page
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
    $offset = ($currentPage - 1) * $limit; // Offset for SQL query

    // Get the distinct SSAs from the table
    $ssaQuery = "SELECT DISTINCT BSNL_SSA FROM all_ip_phones";
    $ssaResult = $conn->query($ssaQuery);
    $ssaOptions = '';

    // Generate options for SSA filter
    if ($ssaResult->num_rows > 0) {
        while ($row = $ssaResult->fetch_assoc()) {
            $ssa = $row['BSNL_SSA'];
            $selected = ($selectedSSA == $ssa) ? 'selected' : '';
            $ssaOptions .= "<option value='$ssa' $selected>$ssa</option>";
        }
    }
    ?>

    <div class="filter-form">
        <form action="allipphones.php" method="GET">
            <label for="ssa">BSNL SSA:</label>
            <select name="ssa" id="ssa">
                <option value="">All</option>
                <?php echo $ssaOptions; ?>
            </select>

            <?php
            // Generate options for District filter based on the selected SSA
            $districtOptions = '';
            if (!empty($selectedSSA)) {
                $districtQuery = "SELECT DISTINCT DISTRICT FROM all_ip_phones WHERE BSNL_SSA = ?";
                $districtStmt = $conn->prepare($districtQuery);
                $districtStmt->bind_param("s", $selectedSSA);
                $districtStmt->execute();
                $districtResult = $districtStmt->get_result();

                if ($districtResult && $districtResult->num_rows > 0) {
                    while ($row = $districtResult->fetch_assoc()) {
                        $district = $row['DISTRICT'];
                        $selected = ($selectedDistrict == $district) ? 'selected' : '';
                        $districtOptions .= "<option value='$district' $selected>$district</option>";
                    }
                } else {
                    $districtOptions .= "<option value='' disabled>No districts found</option>";
                }
            } else {
                $districtOptions .= "<option value='' disabled>Select SSA first</option>";
            }
            ?>

            <label for="district">District:</label>
            <select name="district" id="district">
                <option value="">All</option>
                <?php echo $districtOptions; ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>S No</th>
            <th>BSNL SSA</th>
            <th>District</th>
            <th>Location</th>
            <th>PS/HO</th>
            <th>IP Address</th>
            <th>IP Phone No</th>
            <th>Model No</th>
            <th>Serial No</th>
            <th>Installation Status</th>
            <th>Action</th>
        </tr>

        <?php
        // Generate the SQL query based on the selected filters
        $query = "SELECT * FROM all_ip_phones WHERE 1=1";
        if (!empty($selectedSSA)) {
            $query .= " AND BSNL_SSA = '$selectedSSA'";
        }
        if (!empty($selectedDistrict)) {
            $query .= " AND DISTRICT = '$selectedDistrict'";
        }

        // Apply search filter if provided
        $searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
        if (!empty($searchQuery)) {
            $query .= " AND (BSNL_SSA LIKE '%$searchQuery%' OR District LIKE '%$searchQuery%' OR Location LIKE '%$searchQuery%' OR PSHO LIKE '%$searchQuery%' OR IPAddress LIKE '%$searchQuery%' OR IPPhoneNo LIKE '%$searchQuery%' OR ModelNo LIKE '%$searchQuery%' OR SerialNo LIKE '%$searchQuery%' OR InstallationStatus LIKE '%$searchQuery%')";
        }

        // Get the total number of records
        $totalRecordsQuery = "SELECT COUNT(*) AS total FROM ($query) AS countTable";
        $totalRecordsResult = $conn->query($totalRecordsQuery);

        if ($totalRecordsResult && $totalRecordsResult->num_rows > 0) {
            $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

            // Calculate the total number of pages
            $totalPages = ceil($totalRecords / $limit);
        } else {
            $totalRecords = 0;
            $totalPages = 0;
        }

        // Add pagination to the SQL query
        $query .= " LIMIT $limit OFFSET $offset";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $bsnlSSA = $row['BSNL_SSA'];
                $district = $row['DISTRICT'];
                $location = $row['LOCATION'];
                $psho = $row['PSHO'];
                $ipAddress = $row['IPAddress'];
                $ipPhoneNo = $row['IPPhoneNo'];
                $modelNo = $row['ModelNo'];
                $serialNo = $row['SerialNo'];
                $installationStatus = $row['InstallationStatus'];

                ?>

                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $bsnlSSA; ?></td>
                    <td><?php echo $district; ?></td>
                    <td><?php echo $location; ?></td>
                    <td><?php echo $psho; ?></td>
                    <td><a href="http://<?php echo $ipAddress; ?>" target="_blank" style="color: white;"><?php echo $ipAddress; ?></a></td>
                    <td><?php echo $ipPhoneNo; ?></td>
                    <td><?php echo $modelNo; ?></td>
                    <td><?php echo $serialNo; ?></td>
                    <td><?php echo $installationStatus; ?></td>
                    <td>
                        
						<a href="alter/allipphones/edit.php?id=<?php echo $id; ?>" class="action-btn" title="Edit">&#x270E;</a>
						<a href="alter/allipphones/delete.php?id=<?php echo $id; ?>" class="action-btn" title="Edit">&#x1F5D1;</a>
                    </td>
                </tr>
            <?php
            }
        } else {
            echo "<tr><td colspan='11'>No records found.</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>

    <div class="pagination">
        <?php if ($currentPage > 1) { ?>
            <a href="allipphones.php?page=<?php echo $currentPage - 1; ?>">Previous</a>
        <?php } ?>

        <?php if ($currentPage < $totalPages) { ?>
            <a href="allipphones.php?page=<?php echo $currentPage + 1; ?>">Next</a>
        <?php } ?>
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
