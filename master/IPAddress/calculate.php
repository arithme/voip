<!DOCTYPE html>
<html>
<head>
    <title>IP Address Calculator</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>IP Address Calculator</h1>
        <?php
        function calculateNetworkDetails($ip, $subnet) {
            // Convert the IP address and subnet mask to binary
            $ipBinary = ip2long($ip);
            $subnetBinary = ip2long($subnet);

            // Calculate network address and broadcast address
            $networkAddress = long2ip($ipBinary & $subnetBinary);
            $broadcastAddress = long2ip($ipBinary | (~$subnetBinary));

            // Calculate total number of usable IP addresses in the network
            $totalIPs = pow(2, (32 - substr_count(decbin($subnetBinary), '1'))) - 2;

            return [
                'network' => $networkAddress,
                'broadcast' => $broadcastAddress,
                'total_ips' => $totalIPs
            ];
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $ipAddress = $_POST['ip'];
            $subnetMask = $_POST['subnet'];

            $networkDetails = calculateNetworkDetails($ipAddress, $subnetMask);

            echo '<p><strong>Network Address:</strong> ' . $networkDetails['network'] . '</p>';
            echo '<p><strong>Broadcast Address:</strong> ' . $networkDetails['broadcast'] . '</p>';
            echo '<p><strong>Total IP Addresses Available:</strong> ' . $networkDetails['total_ips'] . '</p>';
        }
        ?>
    </div>
</body>
</html>
