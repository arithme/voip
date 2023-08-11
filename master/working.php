<!DOCTYPE html>
<html lang="en">
<head>
    <title>Master IP List</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Add your CSS stylesheets here -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Master IP List</h1>

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
        if (!empty($selectedSSA)) {
            $query .= " WHERE SSA = '$selectedSSA'";
            if (!empty($selectedDistrict)) {
                $query .= " AND DISTRICT = '$selectedDistrict'";
            }
        }

        // Get the total number of records
        $totalRecordsQuery = "SELECT COUNT(*) AS total FROM ($query) AS countTable";
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

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
						<a href="telnet.php" class="Telnet" style="color: white;">Telnet</a> |
                         
						
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
            <a href="index.php?page=<?php echo $currentPage - 1; ?>">Previous</a>
        <?php } ?>

        <?php if ($currentPage < $totalPages) { ?>
            <a href="index.php?page=<?php echo $currentPage + 1; ?>">Next</a>
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
