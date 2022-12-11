<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("database.php")?>

<div class="container">

<h2 class="my-3">My Watchlist</h2>

</div>

<?php
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }
?>

<div class="container mt-5">

<ul class="list-group">

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

  // TODO: Perform a query to pull up the auctions within watchlist.
  // Select auctions buyer are watching
  // only display items in active auctions
  // display by expired order (the soonest expired one shows first)
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
     WHERE i.itemID IN (SELECT itemID FROM Watchlist WHERE buyerID = '$buyerID') 
        AND i.itemID = bi.itemID
        AND endDate > now()";
    $result = mysqli_query($connection, $query)or die('Error making select users query' . mysql_error());
      /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
    $num_results = mysqli_num_rows($result); // TODO: Calculate me for real
    $results_per_page = 5;
    $max_page = ceil($num_results / $results_per_page);
    $page_start = ($curr_page-1) * $results_per_page;  
    $query .= " LIMIT " . $page_start . ',' . $results_per_page;
    $result = mysqli_query($connection, $query);
    
  
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
        $end_time = new DateTime($row['endDate']);
        print_listing_li($item_id, $title, $desc, $currentprice, $num_bids, $end_time, '');
      }
    }
  } else {
    echo 'Please log in before checking your bids.';
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
  }
?>

</ul>
<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mywatchlist.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="mywatchlist.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mywatchlist.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }

mysqli_close($connection);
?>

  </ul>
</nav>
</div>

<?php include_once("footer.php")?>
