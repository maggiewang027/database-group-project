<?php include_once("header.php")?>

<div class="container my-5">

<?php
    // Connect to the database
    include_once('database.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['auctionTitle']) && !empty($_POST['auctionCategory']) && !empty($_POST['auctionStartPrice']) && !empty($_POST['auctionEndDate'])) {
            // Set the variables
            //$item_id = uniqid();
            $title = mysqli_real_escape_string($connection, $_POST['auctionTitle']);
            $details = mysqli_real_escape_string($connection, $_POST['auctionDetails']);
            $category = mysqli_real_escape_string($connection, $_POST['auctionCategory']);
            $start_price = $_POST['auctionStartPrice'];
            if (!empty($_POST["auctionReservePrice"])) {
                $reserve_price = $_POST["auctionReservePrice"];
            } else {
                $reserve_price = $start_price;
            }
            $end_date = $_POST["auctionEndDate"];
            $seller_id = settype($_SESSION['userid'],'int');
            // Query: insert the auction item to the database
            $create_auction = "INSERT INTO Item (itemID, itemName, description, category, startingPrice, reservePrice, endDate, sellerID) VALUES (NULL, '$title', '$details', '$category', '$start_price', '$reserve_price', '$end_date', '$seller_id')";
            $result = mysqli_query($connection, $create_auction);
            // If all is successful, let user know.
            echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
        } else {
            echo '<div class="text-center">Please enter all the required information. You will be redirected shortly.</div>';
            // Redirect to index after 3 seconds
            header("refresh:3;url=create_auction.php");
        }
        mysqli_close($connection);
    }

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */


/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
            

// If all is successful, let user know.
//echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>
