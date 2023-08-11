<?php
include('db.conf');

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
$ssa = $row['SSA'] ?? '';
$district = $row['District'];
$user = $row['User'];
$location = $row['Location'];
$ipAddress = $row['IPAddress'];
$dirNumber = $row['DirNumber'];
$model = $row['Model'];
$serialNo = $row['SerialNo'];
$installationStatus = $row['InstallationStatus'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedSSA = sanitizeInput($_POST['ssa']);
    $updatedDistrict = sanitizeInput($_POST['district']);
    $updatedUser = sanitizeInput($_POST['user']);
    $updatedLocation = sanitizeInput($_POST['location']);
    $updatedIPAddress = sanitizeInput($_POST['ipAddress']);
    $updatedDirNumber = sanitizeInput($_POST['dirNumber']);
    $updatedModel = sanitizeInput($_POST['model']);
    $updatedSerialNo = sanitizeInput($_POST['serialNo']);
    $updatedInstallationStatus = sanitizeInput($_POST['installationStatus']);

    $updateQuery = "UPDATE ip_phones SET SSA = ?, District = ?, User = ?, Location = ?, IPAddress = ?, DirNumber = ?, Model = ?, SerialNo = ?, InstallationStatus = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssssssi", $updatedSSA, $updatedDistrict, $updatedUser, $updatedLocation, $updatedIPAddress, $updatedDirNumber, $updatedModel, $updatedSerialNo, $updatedInstallationStatus, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dial-100 Directory - Edit Record</title>
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

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
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

        /* Additional CSS for edit.php */
        form#edit-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Edit Record</h1>
    <a href="../../index.php">Back to List</a>

    <div class="container">
        <form action="" method="POST" id="edit-form">
            <label for="ssa">SSA:</label>
            <input type="text" name="ssa" id="ssa" value="<?php echo $ssa; ?>" required>

            <label for="district">District:</label>
            <input type="text" name="district" id="district" value="<?php echo $district; ?>" required>

            <label for="user">User:</label>
            <input type="text" name="user" id="user" value="<?php echo $user; ?>" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo $location; ?>" required>

            <label for="ipAddress">IP Address:</label>
            <input type="text" name="ipAddress" id="ipAddress" value="<?php echo $ipAddress; ?>" required>

            <label for="dirNumber">Directory Number:</label>
            <input type="text" name="dirNumber" id="dirNumber" value="<?php echo $dirNumber; ?>" required>

            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="<?php echo $model; ?>" required>

            <label for="serialNo">Serial Number:</label>
            <input type="text" name="serialNo" id="serialNo" value="<?php echo $serialNo; ?>" required>

            <label for="installationStatus">Installation Status:</label>
            <input type="text" name="installationStatus" id="installationStatus" value="<?php echo $installationStatus; ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>

    <div class="footer">
        <!-- Add your footer content here -->
    </div>

    <!-- Add your JavaScript code here -->
</body>
</html>
