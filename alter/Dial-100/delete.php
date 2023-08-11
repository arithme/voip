<?php
include('db.conf');

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "Invalid ID";
    exit;
}

$query = "SELECT * FROM ip_phones WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows <= 0) {
    echo "Record not found";
    exit;
}

$row = $result->fetch_assoc();
$ssa = $row['SSA'];
$district = $row['District'];
$user = $row['User'];
$location = $row['Location'];
$ipAddress = $row['IPAddress'];
$dirNumber = $row['DirNumber'];
$model = $row['Model'];
$serialNo = $row['SerialNo'];
$installationStatus = $row['InstallationStatus'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmDelete = isset($_POST['confirm_delete']) ? $_POST['confirm_delete'] : false;

    if ($confirmDelete) {
        $deleteQuery = "DELETE FROM ip_phones WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } else {
        // If not confirmed, redirect back to the record
        header("Location: delete.php?id=" . $id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dial-100 Directory - Delete Record</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
    <style>
        body {
            font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .confirmation {
            color: red;
            font-weight: bold;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

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
    </style>
</head>
<body>
    <h1>Delete Record</h1>
    <a href="../../index.php">Back to List</a>

    <div class="container">
        <form action="" method="POST">
            <label for="ssa">SSA:</label>
            <input type="text" name="ssa" id="ssa" value="<?php echo $ssa; ?>" readonly>

            <label for="district">District:</label>
            <input type="text" name="district" id="district" value="<?php echo $district; ?>" readonly>

            <label for="user">User:</label>
            <input type="text" name="user" id="user" value="<?php echo $user; ?>" readonly>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo $location; ?>" readonly>

            <label for="ipAddress">IP Address:</label>
            <input type="text" name="ipAddress" id="ipAddress" value="<?php echo $ipAddress; ?>" readonly>

            <label for="dirNumber">Directory Number:</label>
            <input type="text" name="dirNumber" id="dirNumber" value="<?php echo $dirNumber; ?>" readonly>

            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="<?php echo $model; ?>" readonly>

            <label for="serialNo">Serial Number:</label>
            <input type="text" name="serialNo" id="serialNo" value="<?php echo $serialNo; ?>" readonly>

            <label for="installationStatus">Installation Status:</label>
            <input type="text" name="installationStatus" id="installationStatus" value="<?php echo $installationStatus; ?>" readonly>

            <p class="confirmation">Are you sure you want to delete this record?</p>
            <input type="hidden" name="confirm_delete" value="true">
            <button type="submit">Delete</button>
        </form>
    </div>

    <div class="footer">
        <!-- Add your footer content here -->
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
