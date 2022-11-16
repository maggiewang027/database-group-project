<?php
    // Connect to the database
    include_once('database.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtain the login information
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        // Query 1: check if the user has an account
        $query1 = "SELECT userID FROM User WHERE email = '$email'";
        $check1 = mysqli_query($connection, $query1);
        #$rowcount=mysqli_num_rows($check1);
        #printf("Result set has %d rows.\n",$rowcount);
        if (mysqli_num_rows($check1) >= 1) {
            // Hash the password
            $hash_pass = md5($password);
            // Query 2: check if the password match the email
            $query2 = "SELECT userID FROM User WHERE email = '$email' AND password = '$hash_pass'";
            $check2 = mysqli_query($connection, $query2);
            #$rowcount2=mysqli_num_rows($check2);
            #printf("Result set has %d rows.\n",$rowcount2);
            if (mysqli_num_rows($check2) == 1) {
                // Set session variables
				session_start();
                $_SESSION['logged_in'] = true;
		// 等待fix
                #$user_id = current($connection -> query("SELECT userID FROM User WHERE email = '$email'") -> fetch_assoc());
                #$_SESSION['user_id'] = $user_id;
                #$type = urrent($connection -> query("SELECT userType FROM User WHERE email = '$email'") -> fetch_assoc());
                #$_SESSION['account_type'] = $type;
                echo '<div class="text-center">You are now logged in! You will be redirected shortly.</div>';
                // Redirect to index after 5 seconds
                header("refresh:5;url=index.php");
            } else {
                echo '<div class="text-center">You have entered a wrong password. Please try again. You will be redirected shortly.</div>';
                $_SESSION['logged_in'] = false;
                // Redirect to index after 5 seconds
                header("refresh:5;url=index.php");
            }

        } else {
            echo '<div class="text-center">User not found. Please register a new account. You will be redirected shortly.</div>';
            $_SESSION['logged_in'] = false;
            // Redirect to index after 5 seconds
            header("refresh:5;url=index.php");
        }
        $connection -> close();
    }


// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

//session_start();
//$_SESSION['logged_in'] = true;
//$_SESSION['username'] = "test";
//$_SESSION['account_type'] = "buyer";

//echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');

// Redirect to index after 5 seconds
//header("refresh:5;url=index.php");

?>
