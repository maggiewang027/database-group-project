<?php include_once("header.php")?>

<div class="container my-5">

<?php
    // Connect to the database
    include('database.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtain the login information
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        // Query 1: check if the user has an account
        $query1 = "SELECT * FROM User WHERE email = '$email'";
        $check1 = mysqli_query($connection, $query1);

        if (mysqli_num_rows($check1) >= 1) {
            // Hash the password
            $hash_pass = sha1($password);
            // Query 2: check if the password match the email
            $query2 = "SELECT * FROM User WHERE email = '$email' AND password = '$hash_pass'";
            $check2 = mysqli_query($connection, $query2);

            if (mysqli_num_rows($check2) == 1) {
                while ($row = mysqli_fetch_assoc($check2)) {
                    $id = $row['userID'];
                    $name = $row['displayName'];
                    $type = $row['userType'];
                }
                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['userid'] = $id;
                $_SESSION['username'] = $name;
                $_SESSION['account_type'] = $type;
                echo '<div class="text-center">You are now logged in as "'.$_SESSION['username'].'"! You will be redirected shortly.</div>';
                // Redirect to index after 3 seconds
                header("refresh:3;url=index.php");
            } else {
                echo '<div class="text-center">You have entered a wrong password. Please try again. You will be redirected shortly.</div>';
                $_SESSION['logged_in'] = false;
                // Redirect to index after 3 seconds
                header("refresh:3;url=index.php");
            }

        } else {
            echo '<div class="text-center">User not found. Please register a new account. You will be redirected shortly.</div>';
            $_SESSION['logged_in'] = false;
            // Redirect to index after 3 seconds
            header("refresh:3;url=index.php");
        }
        mysqli_close($connection);
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
<?php include_once('footer.php')?>
