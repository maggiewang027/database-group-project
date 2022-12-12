<?php include_once("header.php")?>

<div class="container my-5">


<?php
    // Connect to the database
    include_once("header.php");
    include('database.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Check if all the information has been entered
        if (!empty($_POST['name']) && !empty($_POST['accountType']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST["passwordConfirmation"])) {
            if(strpos($_POST['email'], '@') == false) {
                echo '<div class="text-center">Please enter a valid email. You will be redirected shortly.</div>';
                // Redirect to index after 3 seconds
                header("refresh:3;url=register.php");
            } else {
                // Set the variables
                $name = mysqli_real_escape_string($connection, $_POST['name']);
                $type = mysqli_real_escape_string($connection, $_POST['accountType']);
                $email = mysqli_real_escape_string($connection, $_POST['email']);
                $password = mysqli_real_escape_string($connection, $_POST['password']);
                $pass_confirm = mysqli_real_escape_string($connection, $_POST["passwordConfirmation"]);
                // Query 1: check if the user already has an account
                $query = "SELECT * FROM User WHERE email = '$email'";
                $check = mysqli_query($connection, $query);

                // Check if the password confirmation is same as the original one
                if (mysqli_num_rows($check) == 0) {
                    
                    if ($password == $pass_confirm) {
                        // Hash the password
                        $hash_pass = sha1($password);
                        // Query 2: insert the registration information to the database
                        $register = "INSERT INTO User (userID, displayName, email, password, userType) VALUES (NULL, '$name', '$email', '$hash_pass', '$type')";
                        $result = mysqli_query($connection, $register);
                        // Set session variables
                        $_SESSION['logged_in'] = true;
                        $query2 = "SELECT * FROM User WHERE email = '$email'";
                        $check2 = mysqli_query($connection, $query2);
                        while ($row = mysqli_fetch_assoc($check2)) {
                            $_SESSION['userid'] = $row['userID'];
                        }
                        $_SESSION['username'] = $name;
                        $_SESSION['account_type'] = $type;
                        echo '<div class="text-center">You are now registered and logged in as "'.$_SESSION['username'].'"! You will be redirected shortly.</div>';
                        // Redirect to index after 3 seconds
                        header("refresh:3;url=index.php");
                    } else {
                        echo '<div class="text-center">The two passwords do not match. Please try again. You will be redirected shortly.</div>';
                        // Redirect to index after 3 seconds
                        header("refresh:3;url=index.php");
                    }

                } else {
                    echo '<div class="text-center">The user already existed with the email. Please log in or check your details entered. You will be redirected shortly.</div>';
                    // Redirect to index after 3 seconds
                    header("refresh:3;url=register.php");
                }
            }
        } else {
            echo '<div class="text-center">Please enter all the required information. You will be redirected shortly.</div>';
            // Redirect to index after 3 seconds
            header("refresh:3;url=register.php");
        }
        mysqli_close($connection);
    }


// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

?>

<?php include_once('footer.php')?>
