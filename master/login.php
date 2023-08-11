<?php
session_start();

// Check if the username and password are valid (you need to implement this logic)
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform the necessary validation and database check here

    // Assuming the login is successful
    $_SESSION['username'] = $username;

    // Redirect to index.php
    header('Location: index.php');
    exit();
}
?>

<!-- Rest of the login.html code -->
