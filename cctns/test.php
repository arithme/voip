<?php
// Include the db.conf file to get the database credentials
require_once('db.conf');

// Connect to the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the current page number from the URL
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10;
$startFrom = ($currentPage - 1) * $recordsPerPage;

// Generate options for BSNL SSA filter
$ssaQuery = "SELECT DISTINCT BSNL_SSA FROM cctns_master";
$ssaResult = $connection->query($ssaQuery);
$ssaOptions = '';

if ($ssaResult && $ssaResult->num_rows > 0) {
    while ($row = $ssaResult->fetch_assoc()) {
        $bsnlSSA = $row['BSNL_SSA'];
        $selected = (isset($_GET['ssa']) && $_GET['ssa'] === $bsnlSSA) ? 'selected' : '';
        $ssaOptions .= "<option value='$bsnlSSA' $selected>$bsnlSSA</option>";
    }
}

// Generate options for District filter based on the selected SSA
$districtOptions = '';
if (isset($_GET['ssa'])) {
    $selectedSSA = $_GET['ssa'];
    $districtQuery = "SELECT DISTINCT DISTRICT FROM cctns_master WHERE BSNL_SSA = ?";
    $districtStmt = $connection->prepare($districtQuery);
    $districtStmt->bind_param("s", $selectedSSA);
    $districtStmt->execute();
    $districtResult = $districtStmt->get_result();

    if ($districtResult && $districtResult->num_rows > 0) {
        while ($row = $districtResult->fetch_assoc()) {
            $district = $row['DISTRICT'];
            $selected = (isset($_GET['district']) && $_GET['district'] === $district) ? 'selected' : '';
            $districtOptions .= "<option value='$district' $selected>$district</option>";
        }
    } else {
        $districtOptions .= "<option value='' disabled>No districts found</option>";
    }
} else {
    $districtOptions .= "<option value='' disabled>Select SSA first</option>";
}

// Generate the SQL query based on the selected filters
$query = "SELECT * FROM cctns_master WHERE 1=1";
if (!empty($_GET['ssa'])) {
    $selectedSSA = $_GET['ssa'];
    $query .= " AND BSNL_SSA = '$selectedSSA'";
}
if (!empty($_GET['district'])) {
    $selectedDistrict = $_GET['district'];
    $query .= " AND DISTRICT = '$selectedDistrict'";
}

// Query to fetch filtered data from the "cctns_master" table with pagination
$query .= " LIMIT $startFrom, $recordsPerPage";
$result = mysqli_query($connection, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Calculate the total number of pages for pagination
$totalRecordsQuery = "SELECT COUNT(*) as total FROM cctns_master WHERE 1=1";
if (!empty($_GET['ssa'])) {
    $selectedSSA = $_GET['ssa'];
    $totalRecordsQuery .= " AND BSNL_SSA = '$selectedSSA'";
}
if (!empty($_GET['district'])) {
    $selectedDistrict = $_GET['district'];
    $totalRecordsQuery .= " AND DISTRICT = '$selectedDistrict'";
}

$totalRecordsResult = mysqli_query($connection, $totalRecordsQuery);
$totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
$totalRecords = $totalRecordsRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sample Index Page</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Add your CSS styles here */
		/* Add your CSS styles here */
		.pagination {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            background-color: #f1f1f1;
            color: black;
            margin: 0 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <header>
        <div class="search-bar">
            <!-- Search bar goes here -->
        </div>
    </header>
    <h1>Data from cctns_master Table</h1>
    <div class="filter-form">
        <form action="index.php" method="GET">
            <label for="ssa">BSNL SSA:</label>
            <select name="ssa" id="ssa">
                <option value="">All</option>
                <?php echo $ssaOptions; ?>
            </select>
            <label for="district">District:</label>
            <select name="district" id="district">
                <option value="">All</option>
                <?php echo $districtOptions; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>BSNL SSA</th>
            <th>DISTRICT</th>
            <th>LOCATION</th>
            <th>PS/HO</th>
            <th>PHASE</th>
            <th>Router</th>
            <th>VoIP</th>
            <th>Video Conference</th>
            <th>Primary Link</th>
            <th>Secondary Link</th>
            <th>DATE</th>
            <th>Landline No.</th>
            <th>STATUS</th>
            <th>USER-ID</th>
            <th>STATIC-IP</th>
        </tr>
        <?php
        // Fetch and display the data from the "cctns_master" table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['BSNL_SSA']}</td>";
            echo "<td>{$row['DISTRICT']}</td>";
            echo "<td>{$row['LOCATION']}</td>";
            echo "<td>{$row['PS_HO']}</td>";
            echo "<td>{$row['PHASE']}</td>";
            echo "<td>{$row['Router']}</td>";
            echo "<td>{$row['VoIP']}</td>";
            echo "<td>{$row['Video_Conference']}</td>";
            echo "<td>{$row['Primary_Link']}</td>";
            echo "<td>{$row['Secondary_Link']}</td>";
            echo "<td>{$row['DATE']}</td>";
            echo "<td>{$row['Landline_No']}</td>";
            echo "<td>{$row['STATUS']}</td>";
            echo "<td>{$row['USER_ID']}</td>";
            echo "<td>{$row['STATIC_IP']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <div class="pagination">
        <?php if ($currentPage > 1) { ?>
            <a href="index.php?page=<?php echo $currentPage - 1; ?>">Previous</a>
        <?php } ?>

        <?php if ($currentPage < $totalPages) { ?>
            <a href="index.php?page=<?php echo $currentPage + 1; ?>">Next</a>
        <?php } ?>

        <p>Page <?php echo $currentPage; ?> out of <?php echo $totalPages; ?></p>
    </div>
</body>
</html>
