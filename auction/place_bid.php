<?php include_once("header.php")?>
<?php include_once("database.php")?>
<?php require("utilities.php")?>


<?php
$item_id = $_SESSION['itemid'];//TODO

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
        $latest_price=$price['latest_price'];
    }

    if ($bid > $latest_price)
    {
        $query = "INSERT INTO BidItem (bidID, itemID, bidTime, bidPrice, buyerID, status) 
        VALUES (NULL, '$item_id', now(), '$bid', 1, 'InAuction')";          
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
        echo '<div class="text-center">You can not enter a value lower than Current bid</div>';
        header("refresh:3;url=browse.php");
    }

    mysqli_close($link);

} else {
	echo '<div class="text-center">You can not place a bid. Please login as a buyer.</div>';
        header("refresh:3;url=browse.php");
}



?>
