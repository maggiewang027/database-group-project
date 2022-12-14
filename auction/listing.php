<?php include_once("header.php")?>
<?php include("database.php")?>
<?php require("utilities.php")?>

<?php
  // Get info from the URL:
  $item_id = $_GET['item_id'];
  $_SESSION['itemid'] = $item_id;
  $buyer_id = $_SESSION['userid'];
  $has_session = $_SESSION['logged_in'];

  // TODO: Use item_id to make a query to the database.
  // Select informations on a specific item
  $query="SELECT i.itemID as itemID, itemName, description, latestPrice, endDate, reservePrice
  FROM Item i
  JOIN
    (SELECT itemID,
             MAX(price) AS latestPrice
     FROM
       (SELECT itemID,
               bidPrice AS price
        FROM BidItem
        where itemID='$item_id'
        UNION ALL SELECT itemID,
                         startingPrice AS price
        FROM Item
        where itemID='$item_id') AS prices
     GROUP BY itemID ) bi
  WHERE i.itemID = bi.itemID 
  ";
  $result=mysqli_query($connection, $query);

  // DELETEME: For now, using placeholder data.
  //$title = "Placeholder title";
  //$description = "Description blah blah blah";
  //$current_price = 30.50;
  //$num_bids = 1;
  //$end_time = new DateTime('2020-11-02T00:00:00');

  while($row=mysqli_fetch_array($result))
  {
    $title=$row['itemName'];
    $description=$row['description'];
    $current_price=$row['latestPrice'];
    $reserve_price=$row['reservePrice'];
    $end_time=new DateTime($row['endDate']);

  } 

  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  
  // Calculate time to auction end:
  $now = new DateTime();
  
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }
  
  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  
  //$has_session = true;
  //$watching = false;

  $query = "SELECT buyerID from WatchList where buyerID = '$buyer_id' and itemID = '$item_id'";
  $result = mysqli_query($connection, $query);
  if(mysqli_num_rows($result) == 0){
    $watching = false;
  }
  else{
    $watching = true;
  }
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time && isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer'):
?>

    <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
    </div>

    <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
    </div>



<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<?php
//find out which buyer bids with highest price
$query = "SELECT buyerID FROM BidItem WHERE itemID='$item_id' and bidPrice = '$current_price'";
$result = mysqli_query($connection, $query);
while($row=mysqli_fetch_array($result))
{
  $buyerID_highestPrice = $row['buyerID']; 
} 
?>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
<?php if ($now > $end_time): ?>
     This auction ended <?php echo(date_format($end_time, 'j M H:i')) ?><br>
     <!-- TODO: Print the result of the auction here? -->
     Result: 
     
<?php
//check1 if current price higher than reserve price
//check2 if this account is a buyer account
//check2 if this buyer account is the buyer account with highest bid price

if($current_price >= $reserve_price){
  if(isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer'){
    if($buyerID_highestPrice == $buyer_id){
      echo('Bid successful');
    }else{
      echo('Outbid');
    }
  }else{
    echo('Auction finished');
  }
}else{
  echo('Auction failed, the highest price is lower than the reserve price');
}
      
?>

<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p >  
    <p class="lead">
      Current bid: ??<?php echo(number_format($current_price, 2)) ?>
      <?php 
      if($buyerID_highestPrice == $buyer_id){
        echo('(Your bid)');
      }else{
        echo('(Other\'s bid)');
      }
    ?>
    </p >

    <?php
    if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
     echo('
      <!-- Bidding form -->
     <form method="POST" action="place_bid.php">
     <div class="input-group">
     <div class="input-group-prepend">
     <span class="input-group-text">??</span>
     </div>
     <input type="number" class="form-control" id="bid" name="bid">
     </div>
     <button type="submit" class="btn btn-primary form-control">Place bid</button>
     </form>');
  }
?>
<?php endif ?>

  
  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->


<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func
</script>
