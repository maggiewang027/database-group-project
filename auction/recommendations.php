<?php include_once("database.php")?>
<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).

  // TODO: Perform a query to pull up auctions they might be interested in.
  $buyer_id = $_SESSION['userid'];

  $query = "SELECT *
  FROM Item
  WHERE itemID in (
  SELECT itemID
  FROM BidItem
  WHERE buyerID IN
      (SELECT buyerID
       FROM BidItem
       WHERE itemID IN
           (SELECT itemID
            FROM BidItem
            WHERE buyerID = '$buyer_id')
         AND buyerID <> '$buyer_id')
  ) AND endDate > now()
  ORDER BY endDate 
  ";
  $result = mysqli_query($connection, $query);

  // TODO: Loop through results and print them out as list items.
  while ($row = mysqli_fetch_assoc($result)) {
    $item_id = $row['itemID'];
    $title = $row['itemName'];
    $description = $row['description'];
    $current_price = $row['latestPrice'];
    $num_bids = $row['bid_cnt'];
    $end_date = new DateTime($row['endDate']);
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  }


  mysqli_close($connection);
?>
