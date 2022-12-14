<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;
  require 'vendor/autoload.php';
  include "database.php";

  // Query 1: check if the bid item expired and obtain the information we need
  $query1 = "SELECT i.itemID as itemID, maxPrice, itemName, sellerID, bid_cnt, reservePrice
             FROM Item i
             JOIN (
             SELECT itemID,
                    MAX(price) AS maxPrice,
                    COUNT(*)-1 AS bid_cnt
             FROM
              (SELECT itemID,
                      bidPrice AS price
               FROM BidItem
               UNION ALL SELECT itemID,
                                startingPrice AS price
               FROM Item
               ) AS prices
             GROUP BY itemID
             ) AS bi
             WHERE endDate=now() AND bi.ItemId = i.ItemId
             ";
  $result1 = mysqli_query($connection, $query1);

  if (mysqli_num_rows($result1) >= 1) {
    while ($row1 = mysqli_fetch_assoc($result1)) {
      // Set the variables
      $item_id = $row1['itemID'];
      $max_price = $row1['maxPrice'];
      $item_name = $row1['itemName'];
      $seller_id = $row1['sellerID'];
      $bid_cnt = $row1['bid_cnt'];
      $reserve_price = $row1['reservePrice'];
    }

    // Check if the bid number equals to 0, if so, then only notify the seller
    if ($bid_cnt > 0) {

      // Query 2: obtain the buyer id who bids with the highest price
      $query2 = "SELECT buyerID FROM BidItem WHERE itemID = '$item_id' AND bidPrice = '$max_price'";
      $result2 = mysqli_query($connection, $query2);
      while ($row2 = mysqli_fetch_assoc($result2)) {
        // Set the variable
        $winner_id = $row2['buyerID'];
      }

      // Query 3: obtain the buyer's information
      $query3 = "SELECT displayName, email FROM User WHERE userID = '$winner_id'";
      $result3 = mysqli_query($connection, $query3);
      while ($row3 = mysqli_fetch_assoc($result3)) {
        // Set the variables
        $winner_name = $row3['displayName'];
        $winner_email = $row3['email'];
        try {

          // Check if the reserve price is lower than the max price
          if ($reserve_price<=$max_price) {
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '55fa1691ecde2c';
            $mail->Password = 'dc1930f984410a';

            // Set the mail sender
            $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
            // Add a recipient
            $mail->addAddress($winner_email, $winner_name);
            // Set the subject
            $mail->Subject = 'Congrats for Your Auction!';
            // Set the mail message body
            $mail->Body = "Dear ".$winner_name.",\n\nCongratulations! You are the winner of the item '".$item_name."' for £".$max_price."!\n\nBest Wishes,\nGroup 6 Auction Team";
            // Finally send the mail
            $mail->send();
          } else {
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '55fa1691ecde2c';
            $mail->Password = 'dc1930f984410a';

            // Set the mail sender
            $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
            // Add a recipient
            $mail->addAddress($winner_email, $winner_name);
            // Set the subject
            $mail->Subject = 'Auction Failed Notification';
            // Set the mail message body
            $mail->Body = "Dear ".$winner_name.",\n\nYour bid price of the item '".$item_name."' is £".$max_price." which is lower than the reserve price of £".$reserve_price.". So unfortunately your auction is unsuccessful.\n\nBest Wishes,\nGroup 6 Auction Team";
            // Finally send the mail
            $mail->send();
          }
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

      // Query 4: obtain the seller's information
      $query4 = "SELECT displayName, email FROM User WHERE userID = '$seller_id'";
      $result4 = mysqli_query($connection, $query4);
      while ($row4 = mysqli_fetch_assoc($result4)) {
        // Set the variables
        $seller_name = $row4['displayName'];
        $seller_email = $row4['email'];
        try {

          // Check if the reserve price is lower than the max price
          if ($reserve_price<=$max_price) {
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '55fa1691ecde2c';
            $mail->Password = 'dc1930f984410a';

            // Set the mail sender
            $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
            // Add a recipient
            $mail->addAddress($seller_email, $seller_name);
            // Set the subject
            $mail->Subject = 'Congrats for Your Item!';
            // Set the mail message body
            $mail->Body = "Dear ".$seller_name.",\n\nCongratulations! Your item '".$item_name."' has been auctioned by ".$winner_name." for £".$max_price."!\n\nBest Wishes,\nGroup 6 Auction Team";
            // Finally send the mail
            $mail->send();
          } else {
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '55fa1691ecde2c';
            $mail->Password = 'dc1930f984410a';

            // Set the mail sender
            $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
            // Add a recipient
            $mail->addAddress($seller_email, $seller_name);
            // Set the subject
            $mail->Subject = 'Auction Failed Notification';
            // Set the mail message body
            $mail->Body = "Dear ".$seller_name.",\n\nYour item '".$item_name."' has the highest bid price of £".$max_price." which is lower than the reserve price of £".$reserve_price.". So unfortunately your auction is unsuccessful and has been closed now.\n\nBest Wishes,\nGroup 6 Auction Team";
            // Finally send the mail
            $mail->send();
          }
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
    } else {

      // Query 5: Send an email to seller if there is no bidder
      $query5 = "SELECT displayName, email FROM User WHERE userID = '$seller_id'";
      $result5 = mysqli_query($connection, $query5);
      while ($row5 = mysqli_fetch_assoc($result5)) {
        // Set the variables
        $seller_name = $row5['displayName'];
        $seller_email = $row5['email'];
        try {
          $mail = new PHPMailer();
          $mail->CharSet = "UTF-8";
          $mail->isSMTP();
          $mail->Host = 'smtp.mailtrap.io';
          $mail->SMTPAuth = true;
          $mail->Port = 2525;
          $mail->Username = '55fa1691ecde2c';
          $mail->Password = 'dc1930f984410a';

          // Set the mail sender
          $mail->setFrom('group6auction@mailtrap.io', 'Group6Auction');
          // Add a recipient
          $mail->addAddress($seller_email, $seller_name);
          // Set the subject
          $mail->Subject = 'No Bid Notification';
          // Set the mail message body
          $mail->Body = "Dear ".$seller_name.",\n\nYour item '".$item_name."' has no bidder and the auction has been closed now.\n\nBest Wishes,\nGroup 6 Auction Team";
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
    }
  }

?>

<!-- <script type="text/javascript">  
   setTimeout(function(){  
       location.reload();  
   },60000);  
</script> -->

