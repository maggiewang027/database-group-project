<?php
    // Create connection with database configuration
    $connection = mysqli_connect('localhost','auctadmin','adminpassword','auction');
    // Check connection 
    if (mysqli_connect_errno()) {
        echo 'Failed to connect to the MySQL server: ' . mysqli_connect_error();
        exit();
    }
?>
