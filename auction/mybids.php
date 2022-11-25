<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("database.php")?>

<div class="container">

<h2 class="my-3">My bids</h2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
session_start();

if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'buyer') {
    header('Location: browse.php');
  }

  // TODO: Perform a query to pull up the auctions they've bidded on.
$buyerID = $_SESSION['userid'];
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $query = "SELECT i.itemID as itemID, itemName, description, latestPrice, endDate, bid_cnt
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
     WHERE i.itemID IN (SELECT itemID FROM BidItem WHERE buyerID = '$buyerID') 
        AND i.itemID = bi.itemID";
    $result = mysqli_query($connection, $query)or die('Error making select users query' . mysql_error());
    
  
  // TODO: Loop through results and print them out as list items.
    if (empty($result)) {
      echo 'You do not have any bids.';
    } else {
      while ($row = mysqli_fetch_assoc($result)) {
        $item_id = $row['itemID'];
        $title = $row['itemName'];
        $desc = $row['description'];
        $currentprice = $row['latestPrice'];
        $num_bids = $row['bid_cnt'];
        $end_time = $row['endDate'];
        print_listing_li($item_id, $title, $desc, $currentprice, $num_bids, $end_time);
      }
    }
  } else {
    echo 'Please log in before checking your bids.';
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
  }
?>

<?php include_once("footer.php")?>
