<?php include_once("header.php")?>
<?php include_once("database.php")?>
<?php require("utilities.php")?>


<?php
$item_id = settype($_SESSION['itemid'],'int');//TODO
$buyer_id = settype($_SESSION['userid'],'int');

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.

//
if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
    $bid = $_POST['bid'];
    $query="SELECT latestPrice
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
      WHERE i.itemID = bi.itemID ";
      
    $result=mysqli_query($connection, $query);
    while($row=mysqli_fetch_array($result))
    {
        $latest_price=$row['latestPrice'];
    }

    if ($bid > $latest_price)
    {
        $query = "INSERT INTO BidItem (bidID, itemID, bidTime, bidPrice, buyerID) 
        VALUES (NULL, '$item_id', now(), '$bid', '$buyer_id')";          
        $result = mysqli_query($connection, $query);

        if($result)
        {
        echo '<div class="text-center">Bid successful</div>';
        header("refresh:2;url=browse.php");
        }
        else
        {
        printf("wrong masseage: %s\n", mysqli_error($connection));
        header("refresh:2;url=browse.php");
        }

    }
    else
    {
        echo '<div class="text-center">You cannot enter a value lower than Current bid</div>';
        header("refresh:3;url=listing.php?item_id=$item_id");
    }

    mysqli_close($connection);

} else {
	echo '<div class="text-center">You cannot place a bid. Please login as a buyer.</div>';
        header("refresh:3;url=browse.php");
}



?>
