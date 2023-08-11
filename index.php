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
$ssaQuery = "SELECT DISTINCT SSA FROM ip_phones";
$ssaResult = $conn->query($ssaQuery);
$ssaOptions = '';

// Generate options for SSA filter
if ($ssaResult->num_rows > 0) {
    while ($row = $ssaResult->fetch_assoc()) {
        $ssa = $row['SSA'];
        $selected = ($selectedSSA == $ssa) ? 'selected' : '';
        $ssaOptions .= "<option value='$ssa' $selected>$ssa</option>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dial-100 Directory</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
	
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

        .pagination {
            margin-top: 10px;
        }

        .pagination a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>Dial-100 Directory</h1>
    <a href="alter/dial-100/add.php" class="add-btn">Add A New Record!</a>
       <nav>
        <ul>
            <li><a href="index.php">Dial-100</a></li>
            <li><a href="allipphones.php">District IPT</a></li>
            <li><a href="directory.php">Important Phone Directory</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
			<li><a href="cctns/index.php">CCTNS</a></li>
			<li><a href="master/index.php">Master IP</a></li>
            <!-- Add more menu items as needed -->
        </ul>
        <form action="index.php" method="GET" id="search-form">
            <input type="text" name="search" placeholder="Search">
            <button type="submit">Search</button>
        </form>
    </nav>

    <form action="index.php" method="GET" id="filter-form" class="filter-form">
        <label for="ssa">SSA:</label>
        <select name="ssa" id="ssa">
            <option value="">All</option>
            <?php echo $ssaOptions; ?>
        </select>

        <?php
        // Generate options for District filter based on the selected SSA
        $districtOptions = '';
        if (!empty($selectedSSA)) {
            $districtQuery = "SELECT DISTINCT District FROM ip_phones WHERE SSA = ?";

            $districtStmt = $conn->prepare($districtQuery);
            $districtStmt->bind_param("s", $selectedSSA);
            $districtStmt->execute();
            $districtResult = $districtStmt->get_result();

            if ($districtResult->num_rows > 0) {
                while ($row = $districtResult->fetch_assoc()) {
                    $district = $row['District'];
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

    <table>
        <tr>
            <th>SSA</th>
            <th>District</th>
            <th>User</th>
            <th>Location</th>
            <th>IP Address</th>
            <th>Directory Number</th>
            <th>Model</th>
            <th>Serial Number</th>
            <th>Installation Status</th>
            <th>Action</th>
        </tr>

        <?php
        // Generate the SQL query based on the selected filters
        $query = "SELECT * FROM ip_phones";
        if (!empty($selectedSSA)) {
            $query .= " WHERE SSA = '$selectedSSA'";
            if (!empty($selectedDistrict)) {
                $query .= " AND District = '$selectedDistrict'";
            }
        }

        // Retrieve the search query
        $searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

        // Modify the SQL query to include the search functionality
        if (!empty($searchQuery)) {
            $query .= " WHERE (SSA LIKE '%$searchQuery%' OR District LIKE '%$searchQuery%' OR User LIKE '%$searchQuery%' OR Location LIKE '%$searchQuery%' OR IPAddress LIKE '%$searchQuery%' OR DirNumber LIKE '%$searchQuery%' OR Model LIKE '%$searchQuery%' OR SerialNo LIKE '%$searchQuery%' OR InstallationStatus LIKE '%$searchQuery%')";
        }

        // Execute the modified query
        $result = $conn->query($query);

        // Get the total number of records
        $selectDataQuery = "SELECT COUNT(*) AS total FROM ($query) AS countTable";
        $totalRecordsResult = $conn->query($selectDataQuery);
        $totalRecords = 0;

        if ($totalRecordsResult && $totalRecordsResult->num_rows > 0) {
            $totalRecords = $totalRecordsResult->fetch_assoc()['total'];
            echo "<p class='record-count'>Total Record found: " . $totalRecords . "</p>";
        }

        // Add pagination to the SQL query
        $query .= " LIMIT $limit OFFSET $offset";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
				$id = $row['id'];
                $ssa = $row['SSA'];
                $district = $row['District'];
                $user = $row['User'];
                $location = $row['Location'];
                $ipAddress = $row['IPAddress'];
                $dirNumber = $row['DirNumber'];
                $model = $row['Model'];
                $serialNo = $row['SerialNo'];
                $installationStatus = $row['InstallationStatus'];

                ?>

                <tr>
					<td><?php echo $id; ?></td>
                    <td><?php echo $ssa; ?></td>
                    <td><?php echo $district; ?></td>
                    <td><?php echo $user; ?></td>
                    <td><?php echo $location; ?></td>
                    <td><a href="http://<?php echo $ipAddress; ?>" target="_blank" style="color: white;"><?php echo $ipAddress; ?></a></td>
                    <td><?php echo $dirNumber; ?></td>
                    <td><?php echo $model; ?></td>
                    <td><?php echo $serialNo; ?></td>
                    <td><?php echo $installationStatus; ?></td>
                    <td>
                        
						<a href="alter/dial-100/edit.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Edit">&#x270E;</a>
						<a href="alter/dial-100/delete.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Delete">&#x1F5D1;</a>
                        
						
                    </td>
                </tr>
            <?php
            }
        } else {
            echo "<tr><td colspan='10'>No records found.</td></tr>";
        }

        // Calculate the total number of pages
        $totalPages = ceil($totalRecords / $limit);
        ?>

    </table>

  	<div class="pagination">
    <?php if ($currentPage > 1) { ?>
        <a href="index.php?page=<?php echo $currentPage - 1; ?>">Previous</a>
    <?php } ?>

    <?php if ($currentPage < $totalPages) { ?>
        <a href="index.php?page=<?php echo $currentPage + 1; ?>">Next</a>
    <?php } ?>

    <span><?php echo "Page " . $currentPage . " of " . $totalPages; ?></span>
</div>


    <div class="footer">

    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
