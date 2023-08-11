<!DOCTYPE html>
<html lang="en">
<head>
    <title>Master IP List</title>
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
		 /* Pagination CSS */
        .pagination {
            margin-top: 20px;
        }

        .pagination a,
        .pagination .current-page {
            display: inline-block;
            padding: 5px 10px;
            font-weight: bold;
            font-size: medium;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
        }

        .pagination .current-page {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Master IP List</h1>
	<a href="alter/allipphones/add.php" class="add-btn">Add A New Record!</a>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
			<li><a href="../index.php">Dial-100</a></li>
            <li><a href="../allipphones.php">District IPT</a></li>			
			<li><a href="../directory.php">Important Phone Directory</a></li>
			<li><a href="../cctns/index.php">CCTNS</a></li>
			<li><a href="../dashboard.php">Dashboard</a></li>
        </ul>
        <form action="index.php" method="GET" class="search-form">
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
    $ssaQuery = "SELECT DISTINCT SSA FROM masterip";
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

    <form action="index.php" method="GET">
        <label for="ssa">SSA:</label>
        <select name="ssa" id="ssa">
            <option value="">All</option>
            <?php echo $ssaOptions; ?>
        </select>

        <?php
        // Generate options for District filter based on the selected SSA
        $districtOptions = '';
        if (!empty($selectedSSA)) {
            $districtQuery = "SELECT DISTINCT DISTRICT FROM masterip WHERE SSA = ?";
            $districtStmt = $conn->prepare($districtQuery);
            $districtStmt->bind_param("s", $selectedSSA);
            $districtStmt->execute();
            $districtResult = $districtStmt->get_result();

            if ($districtResult->num_rows > 0) {
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
	<?php
// Calculate the total number of records based on the filter criteria
$totalRecords = 0; // Initialize the totalRecords variable

// Check if the filter conditions and search term are valid before executing the SQL query
if (!empty($selectedSSA) || !empty($selectedDistrict) || isset($_GET['search'])) {
    // Generate the SQL query based on the selected filters and search term
    $query = "SELECT * FROM masterip WHERE 1 = 1"; // Start with a base query

    if (!empty($selectedSSA)) {
        $query .= " AND SSA = '$selectedSSA'";
    }

    if (!empty($selectedDistrict)) {
        $query .= " AND DISTRICT = '$selectedDistrict'";
    }

    if (isset($_GET['search'])) {
        $searchTerm = sanitizeInput($_GET['search']);
        $query .= " AND (SSA LIKE '%$searchTerm%' OR DISTRICT LIKE '%$searchTerm%' OR LOCATION LIKE '%$searchTerm%' OR PS_HO LIKE '%$searchTerm%' OR WAN_ID LIKE '%$searchTerm%' OR NOC_End LIKE '%$searchTerm%' OR Office_End LIKE '%$searchTerm%' OR LAN_ID LIKE '%$searchTerm%' OR LAN_GATEWAY LIKE '%$searchTerm%')";
    }

    // Execute the SQL query
    $result = $conn->query($query);

    // Check if the query executed successfully
    if ($result === false) {
        die("Error executing the SQL query: " . $conn->error);
    }

    // Get the total number of records
    $totalRecords = $result->num_rows;
}
?>
<div class="record-count">
    Total Records: <?php echo $totalRecords; ?>
</div>

    <table>
        <tr>
            <th>SSA</th>
            <th>District</th>
            <th>Location</th>
            <th>PS_HO</th>
            <th>WAN ID</th>
            <th>NOC End</th>
            <th>Office End</th>
            <th>LAN ID</th>
            <th>LAN Gateway</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        // Generate the SQL query based on the selected filters
				$query = "SELECT * FROM masterip";
		$whereClause = '';

		if (!empty($selectedSSA)) {
			$whereClause .= " WHERE SSA = '$selectedSSA'";
			if (!empty($selectedDistrict)) {
				$whereClause .= " AND DISTRICT = '$selectedDistrict'";
			}
		}

		if (isset($_GET['search'])) {
			$searchTerm = sanitizeInput($_GET['search']);
			if (!empty($whereClause)) {
				$whereClause .= " AND (SSA LIKE '%$searchTerm%' OR DISTRICT LIKE '%$searchTerm%' OR LOCATION LIKE '%$searchTerm%' OR PS_HO LIKE '%$searchTerm%' OR WAN_ID LIKE '%$searchTerm%' OR NOC_End LIKE '%$searchTerm%' OR Office_End LIKE '%$searchTerm%' OR LAN_ID LIKE '%$searchTerm%' OR LAN_GATEWAY LIKE '%$searchTerm%')";
			} else {
				$whereClause .= " WHERE (SSA LIKE '%$searchTerm%' OR DISTRICT LIKE '%$searchTerm%' OR LOCATION LIKE '%$searchTerm%' OR PS_HO LIKE '%$searchTerm%' OR WAN_ID LIKE '%$searchTerm%' OR NOC_End LIKE '%$searchTerm%' OR Office_End LIKE '%$searchTerm%' OR LAN_ID LIKE '%$searchTerm%' OR LAN_GATEWAY LIKE '%$searchTerm%')";
			}
		}

			// Combine the main query with the whereClause
			$query .= $whereClause;
					// Get the total number of records
				   $totalRecordsQuery = "SELECT COUNT(*) AS total FROM ($query) AS countTable";
			$totalRecordsResult = $conn->query($totalRecordsQuery);

			if ($totalRecordsResult === false) {
				die("Error: " . $conn->error); // Display the specific error message
			}

			$totalRecordsData = $totalRecordsResult->fetch_assoc();
			$totalRecords = $totalRecordsData['total'];
        // Calculate the total number of pages
        $totalPages = ceil($totalRecords / $limit);

        // Add pagination to the SQL query
        $query .= " LIMIT $limit OFFSET $offset";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ssa = $row['SSA'];
                $district = $row['DISTRICT'];
                $location = $row['LOCATION'];
                $psHo = $row['PS_HO'];
                $wanId = $row['WAN_ID'];
                $nocEnd = $row['NOC_End'];
                $officeEnd = $row['Office_End'];
                $lanId = $row['LAN_ID'];
                $lanGateway = $row['LAN_GATEWAY'];
                $status = '';

                // Perform ping and set status based on the result
                // Perform ping and set status based on the result
				if (isset($_GET['ping']) && $_GET['ping'] === $lanGateway) {
					$pingResult = exec("ping -c 1 $lanGateway");

					// Check the exit status of the ping command
					if ($pingResult === "") {
						$status = 'Failed';
					} else {
						// Parse the ping output to extract the response time
						preg_match('/time=([\d.]+) ms/', $pingResult, $matches);

						if (isset($matches[1])) {
							$responseTime = $matches[1];
							$status = "Success ({$responseTime} ms)";
						} else {
							$status = 'Failed';
						}
					}
				}

                ?>

                <tr>
                    <td><?php echo $ssa; ?></td>
                    <td><?php echo $district; ?></td>
                    <td><?php echo $location; ?></td>
                    <td><?php echo $psHo; ?></td>
                    <td><?php echo $wanId; ?></td>
                    <td><?php echo $nocEnd; ?></td>
                    <td><?php echo $officeEnd; ?></td>
                    <td><?php echo $lanId; ?></td>
                    <td><a href="ping.php" class="ping" data-ip="<?php echo $lanGateway; ?>" style="color: white;"><?php echo $lanGateway; ?></a></td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <a href="#" class="view" style="color: white;">View</a> |
						<a href="#" class="edit" style="color: white;">Edit</a> |
						<a href="telnet.php?ip=<?php echo $lanGateway; ?>" class="Telnet" style="color: white;">Telnet</a> |
                         
						
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
        <a href="index.php?page=<?php echo $currentPage - 1; ?>" class="action-btn">Previous</a>
    <?php } ?>

    <?php echo "Page $currentPage of $totalPages"; ?>

    <?php if ($currentPage < $totalPages) { ?>
        <a href="index.php?page=<?php echo $currentPage + 1; ?>" class="action-btn">Next</a>
    <?php } ?>
</div>


    <!-- Add your JavaScript code here -->
    <script src="js/script.js"></script>
    <script>
        // Ping functionality
        var pingLinks = document.querySelectorAll('.ping');

        pingLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var ip = this.getAttribute('data-ip');
                pingIP(ip);
            });
        });

        function pingIP(ip) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'ping.php?ip=' + encodeURIComponent(ip), true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    updateStatus(ip, response.status);
                }
            };

            xhr.send();
        }

        function updateStatus(ip, status) {
            var statusCell = document.querySelector('a[data-ip="' + ip + '"]').parentNode.nextElementSibling;
            statusCell.textContent = status;
        }
    </script>

</body>
</html>
