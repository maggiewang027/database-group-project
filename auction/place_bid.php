<?php include_once("header.php")?>
<?php include_once("database.php")?>
<?php require("utilities.php")?>


<?php
$item_id = $_GET['item_id'];
// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.

$bid = $_POST['bid'];
$pre_bid="SELECT bidPrice FROM Item WHERE itemID =1";
$latest_bid=mysqli_query($connection, $pre_bid);
while($price=mysqli_fetch_array($latest_bid))
{
    $latest_bid_price=$price['bidPrice'];
}

if ($bid > $latest_bid_price)
{
    $query = "INSERT INTO BidItem (itemID, bidTime, bidPrice, buyerID, status) 
    VALUES (1, now(), '$bid', 1, 'auction')";          
    $result = mysqli_query($connection, $query);

    $sql_biditem="UPDATE BidItem set bidPrice='$bid' where itemID =1";
    $list_biditem=mysqli_query($connection, $sql_biditem);

    $sql="UPDATE Item set bidPrice='$bid' where itemID =1";
    $list=mysqli_query($connection, $sql);

    if($list_biditem)
    {
    echo '<div class="text-center">Bid successful</div>';
    header("refresh:2;url=listing.php");
    }
    else
    {
    printf("wrong masseage: %s\n", mysqli_error($connection));
    header("refresh:2;url=listing.php");
    }

}
else
{
    echo '<div class="text-center">You can not enter a value lower than Current bid</div>';
    header("refresh:2;url=listing.php");
}








 
mysqli_close($link);



?>
