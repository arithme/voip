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
if (isset($_GET['search'])) {
    $searchKeyword = $_GET['search'];

    // Generate the SQL query with the search condition for each column
    $query .= " AND (BSNL_SSA LIKE '%$searchKeyword%'
               OR DISTRICT LIKE '%$searchKeyword%'
               OR LOCATION LIKE '%$searchKeyword%'
               OR PS_HO LIKE '%$searchKeyword%'
               OR PHASE LIKE '%$searchKeyword%'
               OR Router LIKE '%$searchKeyword%'
               OR VoIP LIKE '%$searchKeyword%'
               OR Video_Conference LIKE '%$searchKeyword%'
               OR Primary_Link LIKE '%$searchKeyword%'
               OR Secondary_Link LIKE '%$searchKeyword%'
               OR DATE LIKE '%$searchKeyword%'
               OR Landline_No LIKE '%$searchKeyword%'
               OR STATUS LIKE '%$searchKeyword%'
               OR USER_ID LIKE '%$searchKeyword%'
               OR STATIC_IP LIKE '%$searchKeyword%')";
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

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = $_GET['search'];
    $totalRecordsQuery .= " AND (BSNL_SSA LIKE '%$searchKeyword%'
               OR DISTRICT LIKE '%$searchKeyword%'
               OR LOCATION LIKE '%$searchKeyword%'
               OR PS_HO LIKE '%$searchKeyword%'
               OR PHASE LIKE '%$searchKeyword%'
               OR Router LIKE '%$searchKeyword%'
               OR VoIP LIKE '%$searchKeyword%'
               OR Video_Conference LIKE '%$searchKeyword%'
               OR Primary_Link LIKE '%$searchKeyword%'
               OR Secondary_Link LIKE '%$searchKeyword%'
               OR DATE LIKE '%$searchKeyword%'
               OR Landline_No LIKE '%$searchKeyword%'
               OR STATUS LIKE '%$searchKeyword%'
               OR USER_ID LIKE '%$searchKeyword%'
               OR STATIC_IP LIKE '%$searchKeyword%')";
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
    <title>cctns</title>
    <link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/menu.css">

  
</head>
<body>
 <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
			<li><a href="../index.php">Dial-100</a></li>
            <li><a href="../allipphones.php">District IPT</a></li>			
			<li><a href="../directory.php">Important Phone Directory</a></li>
			<li><a href="../master/index.php">Master IP</a></li>
			<li><a href="../dashboard.php">Dashboard</a></li>
           
        </ul>
       <form action="index.php" method="GET" class="filter-form">
    <input type="text" name="search" placeholder="Search..." class="search-input">
    <button type="submit" class="search-button">Search</button>
</form>
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
    </nav>
   
    <h1>cctns_master </h1>
	 <?php if (isset($_GET['search']) && !empty($_GET['search'])) : ?>
        <div class="total-records">
            Total Records Found: <?php echo $totalRecords; ?>
        </div>
    <?php endif; ?>

   
    <table border="1">
        <tr>
           
            <th>BSNL SSA</th>
            <th>DISTRICT</th>
            <th>LOCATION</th>
            <th>PS/HO</th>
            <th>PHASE</th>
            <th>Router</th>
            
            
            <th>Primary Link</th>
            <th>Secondary Link</th>
           
            <th>Landline No.</th>
            <th>STATUS</th>
            <th>USER-ID</th>
            <th>STATIC-IP</th>
        </tr>
        <?php
        // Fetch and display the data from the "cctns_master" table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            
            echo "<td>{$row['BSNL_SSA']}</td>";
            echo "<td>{$row['DISTRICT']}</td>";
            echo "<td>{$row['LOCATION']}</td>";
            echo "<td>{$row['PS_HO']}</td>";
            echo "<td>{$row['PHASE']}</td>";
            echo "<td>{$row['Router']}</td>";
           
            
            echo "<td>{$row['Primary_Link']}</td>";
            echo "<td>{$row['Secondary_Link']}</td>";
            
            echo "<td>{$row['Landline_No']}</td>";
            echo "<td>{$row['STATUS']}</td>";
            echo "<td>{$row['USER_ID']}</td>";
            echo "<td>{$row['STATIC_IP']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
	<footer>
    <!-- Pagination -->
    <div class="pagination">
        <?php if ($currentPage > 1) { ?>
            <a href="index.php?page=<?php echo $currentPage - 1; ?>" class="pagination-link">Previous</a>
        <?php } ?>

        <?php if ($currentPage < $totalPages) { ?>
            <a href="index.php?page=<?php echo $currentPage + 1; ?>" class="pagination-link">Next</a>
        <?php } ?>

        <p class="pagination-info">Page <?php echo $currentPage; ?> out of <?php echo $totalPages; ?></p>
    </div>
</footer>
   
</body>
</html>
