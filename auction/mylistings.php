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
    $query = "SELECT i.itemID as itemID, itemName, description, latestPrice, endDate, sellerID, bid_cnt
     FROM Item i
     JOIN (
     SELECT itemID,
            MAX(price) AS latestPrice,
            COUNT(*)-1 AS bid_cnt
     FROM
       (SELECT itemID,
               bidPrice AS price
        FROM BidItem
        UNION ALL SELECT itemID,
                         startingPrice AS price
        FROM Item) AS prices
     GROUP BY itemID   
      ) bi
     ON i.itemID = bi.itemID
     WHERE sellerID = '$sellerID'";
    $result = mysqli_query($connection, $query)or die('Error making select users query' . mysql_error());
    
// TODO: Loop through results and print them out as list items.
    if (empty($result)) {
      echo 'You do not have any auctions.';
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $item_id = $row['itemID'];
        $title = $row['itemName'];
        $desc = $row['description'];
        $price = $row['latestPrice'];
        $num_bids = $row['bid_cnt'];
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
