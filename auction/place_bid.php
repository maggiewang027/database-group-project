<?php include_once("header.php")?>
<div class="container my-5">
<?php include("database.php")?>
<?php require("utilities.php")?>
<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;
  require 'vendor/autoload.php';
?>


<?php
$item_id = $_SESSION['itemid'];
$buyer_id = $_SESSION['userid'];

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
        echo '<div class="text-center">Bid successful! <a href="mybids.php">View your bid listing.</a></div>';

        // Query 1: obtain the item id
        $query1 = "SELECT itemName FROM Item WHERE itemID = '$item_id'";
        $result1 = mysqli_query($connection, $query1);
        while($row1=mysqli_fetch_assoc($result1))
        {
            $item_name=$row1['itemName'];
        }
        
        // Query 2: obtain the buyer's information with the highest price
        $query2 = "SELECT nowBuyerName, nowBuyerEmail, MAX(bidPrice) AS maxPrice
                   FROM BidItem bi
                   JOIN
                    (SELECT displayName AS nowBuyerName,
                            email AS nowBuyerEmail,
                            userID
                     FROM
                       (SELECT displayName,
                               email,
                               userID
                        FROM User
                        WHERE userID = '$buyer_id') AS u
                     GROUP BY userID) AS us
                   WHERE itemID = '$item_id' AND buyerID = '$buyer_id' AND us.userID = bi.buyerID
                   GROUP BY nowBuyerName
                   ORDER BY maxPrice
                   ";
        $result2 = mysqli_query($connection, $query2);
        // Send an email to the buyer
        while($row2=mysqli_fetch_assoc($result2))
        {
            $nowbuyer_name=$row2['nowBuyerName'];
            $nowbuyer_email=$row2['nowBuyerEmail'];
            $highest_bid=$row2['maxPrice'];

            try {
              $mail = new PHPMailer();
              $mail->isSMTP();
              $mail->Host = 'smtp.mailtrap.io';
              $mail->SMTPAuth = true;
              $mail->Port = 2525;
              $mail->Username = '55fa1691ecde2c';
              $mail->Password = 'dc1930f984410a';

              // Set the mail sender
              $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
              // Add a recipient
              $mail->addAddress($nowbuyer_email, $nowbuyer_name);
              // Set the subject
              $mail->Subject = 'Bid Successful!';
              // Set the mail message body
              $mail->Body = "Dear ".$nowbuyer_name.",\n\nYour bid of the item '".$item_name."' is successful for £".$highest_bid."!\n\nBest Wishes,\nGroup 6 Auction Team";
              // Finally send the mail
              $mail->send();
            }
            catch (Exception $e)
            {
               // PHPMailer exception
               echo $e->errorMessage();
            }
            catch (\Exception $e)
            {
               // PHP exception (note the backslash to select the global namespace Exception class)
               echo $e->getMessage();
            }
        }

        // Query 3: obtain the buyers' information with the previous price
        $query3 = "SELECT prevBuyerName, prevBuyerEmail, MAX(bidPrice) AS prevPrice
                   FROM BidItem bi
                   JOIN
                    (SELECT displayName AS prevBuyerName,
                            email AS prevBuyerEmail,
                            userID
                     FROM
                       (SELECT displayName,
                               email,
                               userID
                        FROM User
                        WHERE userID != '$buyer_id') AS u
                     GROUP BY userID) AS us
                   WHERE itemID = '$item_id' AND buyerID != '$buyer_id' AND us.userID = bi.buyerID
                   GROUP BY prevBuyerName
                   ORDER BY prevPrice
                  ";
        $result3 = mysqli_query($connection, $query3);
        // Send an email to the buyers
        while($row3=mysqli_fetch_assoc($result3))
        {
            $prevbuyer_name=$row3['prevBuyerName'];
            $prevbuyer_email=$row3['prevBuyerEmail'];
            $prev_bid=$row3['prevPrice'];

            try {
              $mail = new PHPMailer();
              $mail->isSMTP();
              $mail->Host = 'smtp.mailtrap.io';
              $mail->SMTPAuth = true;
              $mail->Port = 2525;
              $mail->Username = '55fa1691ecde2c';
              $mail->Password = 'dc1930f984410a';

              // Set the mail sender
              $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
              // Add a recipient
              $mail->addAddress($prevbuyer_email, $prevbuyer_name);
              // Set the subject
              $mail->Subject = 'Outbid Notification';
              // Set the mail message body
              $mail->Body = "Dear ".$prevbuyer_name.",\n\nYour previous bid of the item '".$item_name."' has been auctioned by others for £".$highest_bid." and your previous bid price is £".$prev_bid.".\nIf you want to place a higher price, please log back to the system.\n\nBest Wishes,\nGroup 6 Auction Team";
              // Finally send the mail
              $mail->send();
            }
            catch (Exception $e)
            {
               // PHPMailer exception
               echo $e->errorMessage();
            }
            catch (\Exception $e)
            {
               // PHP exception (note the backslash to select the global namespace Exception class)
               echo $e->getMessage();
            }
        }

        //header("refresh:2;url=browse.php");
        }
        else
        {
        printf("wrong masseage: %s\n", mysqli_error($connection));

        header("refresh:2;url=browse.php");
        }

    }
    else
    {
        echo '<div class="text-center">You cannot enter a value lower than Current bid. You will be redirected shortly.</div>';
        header("refresh:3;url=listing.php?item_id=$item_id");
    }

    mysqli_close($connection);

} else {
	echo '<div class="text-center">You cannot place a bid. Please login as a buyer.</div>';
        header("refresh:3;url=browse.php");
}

?>

<?php include_once('footer.php')?>
