<?php
include('db.conf');
include('css/dashboard.css');

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$graphDataQuery = "SELECT location, model_3905, model_6941, model_7821, dial_100, ps, ho, ci, helpdesk FROM additional_data";
$graphDataResult = $conn->query($graphDataQuery);

$graphLabels = [];
$graphValues = [];

if ($graphDataResult->num_rows > 0) {
    while ($row = $graphDataResult->fetch_assoc()) {
        // Populate labels (locations) and values for each field in the graph
        $graphLabels[] = $row['location'];
        $graphValues[] = [
            $row['model_3905'],
            $row['model_6941'],
            $row['model_7821'],
            $row['dial_100'],
            $row['ps'],
            $row['ho'],
            $row['ci'],
            $row['helpdesk'],
        ];
    }
}

 // Execute the query
                $query = "SELECT * FROM additional_data";
                $result = $conn->query($query);

                $data = array();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;
                    }
                }

                // Calculate total count for each field
                $model3905Count = 0;
                $model6941Count = 0;
                $model7821Count = 0;
                $dial100Count = 0;
                $psCount = 0;
                $hoCount = 0;
                $ciCount = 0;
                $helpdeskCount = 0;

                foreach ($data as $row) {
                    $model3905Count += $row['model_3905'];
                    $model6941Count += $row['model_6941'];
                    $model7821Count += $row['model_7821'];
                    $dial100Count += $row['dial_100'];
                    $psCount += $row['ps'];
                    $hoCount += $row['ho'];
                    $ciCount += $row['ci'];
                    $helpdeskCount += $row['helpdesk'];
                }
// Define available tables and columns
$tables = [
    "additional_data" => [
        "id", "location", "model_3905", "model_6941", "model_7821", "dial_100", "ps", "ho", "ci", "helpdesk"
    ],
    "all_ip_phones" => [
        "id", "BSNL_SSA", "DISTRICT", "LOCATION", "PSHO", "IPAddress", "IPPhoneNo", "ModelNo", "SerialNo", "InstallationStatus"
    ],
    "directory" => [
        "id", "Location", "Assigned_To", "IP_Phone_No", "IP_Address", "Model_No", "Serial_Number"
    ],
    "ip_phones" => [
        "id", "SSA", "District", "User", "Location", "IPAddress", "DirNumber", "Model", "SerialNo", "InstallationStatus"
    ]
];

$searchResults = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedTable = $_POST["table"];
    $searchQuery = $_POST["query"];

    // Perform search in the selected table
    if (isset($tables[$selectedTable])) {
        $columns = $tables[$selectedTable];

        // Construct the query
        $selectDataQuery = "SELECT * FROM " . $selectedTable . " WHERE ";
        $whereConditions = [];
        foreach ($columns as $column) {
            $whereConditions[] = $column . " LIKE '%" . $searchQuery . "%'";
        }
        $selectDataQuery .= implode(" OR ", $whereConditions);

        // Execute the query
        $result = $conn->query($selectDataQuery);

        if ($result) {
            if ($result->num_rows > 0) {
                $searchResults .= "<h3>Search Results</h3>";
                $searchResults .= "Table: " . $selectedTable . "<br>";
                $searchResults .= "Search Query: " . $searchQuery . "<br>";
                $searchResults .= "<hr>";
                $searchResults .= "<table>";
                $searchResults .= "<tr>";
                foreach ($columns as $column) {
                    $searchResults .= "<th>" . $column . "</th>";
                }
                $searchResults .= "</tr>";
                while ($row = $result->fetch_assoc()) {
                    $searchResults .= "<tr>";
                    foreach ($columns as $column) {
                        $value = isset($row[$column]) ? $row[$column] : "";
                        $searchResults .= "<td>" . $value . "</td>";
                    }
                    $searchResults .= "</tr>";
                }
                $searchResults .= "</table>";
            } else {
                $searchResults .= "<p>No results found.</p>";
            }
        } else {
            $searchResults .= "<p>Error executing the query: " . $conn->error . "</p>";
        }
    } else {
        $searchResults .= "<p>Invalid table selected.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
</head>
<body>
	
    <div class="app">
        <div class="app-header">
             <li><a href="index.php">Dial-100</a></li>
            <li><a href="allipphones.php">District IPT</a></li>
            <li><a href="directory.php">Important Phone Directory</a></li>
            <li><a href="master/index.php">Master IP </a></li>
			<li><a href="cctns/index.php">CCTNS</a></li>
            <form class="search-form" method="POST" action="">
                <input type="text" name="query" class="search-input" placeholder="Enter search query" required>
                <select name="table" class="table-select">
                    <option value="" disabled selected>Select a table</option>
                    <option value="additional_data">Summary</option>
                    <option value="all_ip_phones">District</option>
                    <option value="directory">PHQ/HO</option>
                    <option value="ip_phones">Dial-100 </option>
                </select>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div class="app-body">
            <!-- Body content -->
            <div class="search-results">
                <?php echo $searchResults; ?>
            </div>
			 
				 <div class="model-counts">
                <div class="model-card model-3905">
                    <div class="model-count-title">Model 3905</div>
                    <div class="model-count-value"><?php echo isset($model3905Count) ? $model3905Count : 0; ?></div>
                </div>
                <div class="model-card model-6941">
                    <div class="model-count-title">Model 6941</div>
                    <div class="model-count-value"><?php echo isset($model6941Count) ? $model6941Count : 0; ?></div>
                </div>
                <div class="model-card model-7821">
                    <div class="model-count-title">Model 7821</div>
                    <div class="model-count-value"><?php echo isset($model7821Count) ? $model7821Count : 0; ?></div>
                </div>
				
    </div>
			<div class="graph-section">
            <canvas id="graphCanvas"></canvas>
        </div>
		<div class="model-counts">
            <div class="model-card model-ps">
                <div class="model-count-title">PS Count</div>
                <div class="model-count-value"><?php echo isset($psCount) ? $psCount : 0; ?></div>
            </div>
            <div class="model-card model-ho">
                <div class="model-count-title">HO Count</div>
                <div class="model-count-value"><?php echo isset($hoCount) ? $hoCount : 0; ?></div>
            </div>
            <div class="model-card model-ci">
                <div class="model-count-title">CI Count</div>
                <div class="model-count-value"><?php echo isset($ciCount) ? $ciCount : 0; ?></div>
            </div>
            
        </div>
    
    </div>
	  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // JavaScript code to generate the graph
        function generateGraph() {
            // Use the PHP-fetched graph data for labels and values
            const labels = <?php echo json_encode($graphLabels); ?>;
            const data = <?php echo json_encode($graphValues); ?>;
            const colors = ['rgba(75, 192, 192, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(201, 203, 207, 0.8)'];

            const graphCanvas = document.getElementById('graphCanvas');

            new Chart(graphCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Model 3905',
                            data: data.map(item => item[0]),
                            backgroundColor: colors[0],
                            borderColor: colors[0],
                            borderWidth: 1
                        },
                        {
                            label: 'Model 6941',
                            data: data.map(item => item[1]),
                            backgroundColor: colors[1],
                            borderColor: colors[1],
                            borderWidth: 1
                        },
                        {
                            label: 'Model 7821',
                            data: data.map(item => item[2]),
                            backgroundColor: colors[2],
                            borderColor: colors[2],
                            borderWidth: 1
                        },
                        // Add more datasets for other fields as needed
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: true
                            }
                        },
                        y: {
                            grid: {
                                display: true
                            }
                        }
                    }
                }
            });
        }

        // Call the function to generate the graph when the page loads
        document.addEventListener('DOMContentLoaded', generateGraph);
    </script>
</body>
</html>
				
        </div>
    </div>
</body>
</html>
