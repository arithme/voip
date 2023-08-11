<?php
if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];

    // Execute the ping command based on the operating system
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows OS
        exec("ping -n 1 $ip", $output, $returnVal);
    } else {
        // Linux/Mac OS
        exec("ping -c 1 $ip 2>&1", $output, $returnVal);
    }

    if ($returnVal === 0 && !strpos(implode(' ', $output), 'Destination net unreachable')) {
        // Ping successful
        $response = [
            'status' => 'Success',
        ];
    } else {
        // Ping failed
        $response = [
            'status' => 'Failed',
        ];
    }
} else {
    // Invalid IP parameter
    $response = [
        'status' => 'Invalid IP',
    ];
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
