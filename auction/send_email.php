<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;
  require 'vendor/autoload.php';
  include "database.php";

  $query1 = "SELECT i.itemID as itemID, maxPrice, itemName, sellerID, bid_cnt
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
             WHERE endDate<=now() AND now()-interval 5 second<=endDate AND bi.ItemId = i.ItemId
             ";
  $result1 = mysqli_query($connection, $query1);
  //$rowcount1 = mysqli_num_rows($result1);
  //printf("Result set has %d rows.\n",$rowcount1);

  if (mysqli_num_rows($result1) >= 1) {
    while ($row1 = mysqli_fetch_assoc($result1)) {
      $item_id = $row1['itemID'];
      $max_price = $row1['maxPrice'];
      $item_name = $row1['itemName'];
      $seller_id = $row1['sellerID'];
      $bid_cnt = $row1['bid_cnt'];
      //printf("Id: %d.\n",$item_id);
      //printf("Price: %d.\n",$max_price);
      //printf("Name: %s.\n",$item_name);
      //printf("Seller: %d.\n",$seller_id);
    }

    if ($bid_cnt > 0) {
      $query2 = "SELECT buyerID FROM BidItem WHERE itemID = '$item_id' AND bidPrice = '$max_price'";
      $result2 = mysqli_query($connection, $query2);
      //$rowcount2 = mysqli_num_rows($result2);
      //printf("Result set has %d rows.\n",$rowcount2);

      while ($row2 = mysqli_fetch_assoc($result2)) {
        $winner_id = $row2['buyerID'];
        //printf("Buyer id: %d.\n",$winner_id);
      }

      $query3 = "SELECT displayName, email FROM User WHERE userID = '$winner_id'";
      $result3 = mysqli_query($connection, $query3);
      //$rowcount3 = mysqli_num_rows($result3);
      //printf("Result set has %d rows.\n",$rowcount3);
      while ($row3 = mysqli_fetch_assoc($result3)) {
        $winner_name = $row3['displayName'];
        $winner_email = $row3['email'];
        //printf("Name: %s.\n",$winner_name);
        //printf("Email: %s.\n",$winner_email);
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
          $mail->addAddress($winner_email, $winner_name);
          // Set the subject
          $mail->Subject = 'Congrats for Your Auction!';
          // Set the mail message body
          $mail->Body = "Dear ".$winner_name.",\n\nCongratulations! You are the winner of the item '".$item_name."' for £".$max_price."!\n\nBest Wishes,\nGroup 6 Auction Team";
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

      $query4 = "SELECT displayName, email FROM User WHERE userID = '$seller_id'";
      $result4 = mysqli_query($connection, $query4);
      while ($row4 = mysqli_fetch_assoc($result4)) {
        $seller_name = $row4['displayName'];
        $seller_email = $row4['email'];
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
          $mail->addAddress($seller_email, $seller_name);
          // Set the subject
          $mail->Subject = 'Congrats for Your Item!';
          // Set the mail message body
          $mail->Body = "Dear ".$seller_name.",\n\nCongratulations! Your item '".$item_name."' has been auctioned by ".$winner_name." for £".$max_price."!\n\nBest Wishes,\nGroup 6 Auction Team";
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
    } else {
      $query5 = "SELECT displayName, email FROM User WHERE userID = '$seller_id'";
      $result5 = mysqli_query($connection, $query5);
      while ($row5 = mysqli_fetch_assoc($result5)) {
        $seller_name = $row5['displayName'];
        $seller_email = $row5['email'];
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
          $mail->addAddress($seller_email, $seller_name);
          // Set the subject
          $mail->Subject = 'No Bid Notification';
          // Set the mail message body
          $mail->Body = "Dear ".$seller_name.",\n\nYour item '".$item_name."' has no bider and the auction has been closed now.\n\nBest Wishes,\nGroup 6 Auction Team";
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

