<?php
    // Create connection
    $connection = mysqli_connect("localhost:8888","auctadmin","adminpassword","auction"); // Database configuration
    // Check connection 
    if (mysqli_connect_errno()) {
        echo "Failed to connect to the MySQL server: " . mysqli_connect_error();
        exit();
    }

?>
