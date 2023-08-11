<?php
if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    // Replace the port number below with the actual port number you want to use for Telnet
    $port = 23;

    // Check if the Telnet port is open
    $connection = @fsockopen($ip, $port, $errno, $errstr, 5);

    if ($connection) {
        fclose($connection);
        // Telnet is available, provide a link to initiate Telnet
        $telnetLink = "telnet://$ip:$port";
        $response = [
            'status' => 'Telnet available',
            'telnet_link' => $telnetLink,
        ];
    } else {
        $response = [
            'status' => 'Telnet unavailable',
        ];
    }
} else {
    $response = [
        'status' => 'Invalid IP',
    ];
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
