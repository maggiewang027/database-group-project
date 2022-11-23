<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("database.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>



<?php
session_start();
// TODO: Check user's credentials (cookie/session).
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }

// TODO: Perform a query to pull up their auctions.
$sellerID = $_SESSION['userid'];
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $query1 = "SELECT itemID, itemName, description, startingPrice, endDate FROM Item";
    $result = mysqli_query($connection, $query1)or die('Error making select users query' . mysql_error());
    
// TODO: Loop through results and print them out as list items.
    if (empty($result)) {
      echo 'You do not have any auctions.';
    } else {
      echo 'yes';
      while ($row = mysqli_fetch_assoc($result)) {
        $item_id = $row['itemID'];
        $title = $row['itemName'];
        $desc = $row['description'];
        $price = 2;
        $num_bids = 1;
        $end_time = $row['endDate'];
        print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time);
      }
    }
  } else {
    echo 'Please log in before checking your listings.';
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
  }

  ?>




</div>

<?php


?>
  

 <?php  
  
  
  
  
  
  
  
?>

<?php include_once("footer.php")?>
