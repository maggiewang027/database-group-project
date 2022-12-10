<?php include_once("database.php")?>

 <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
$buyer_id = $_SESSION['userid'];

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['arguments'];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  //$query = "INSERT INTO WatchList (watchListID, itemID, buyerID) VALUES (NULL, 1 ,1)";
  $query = "INSERT INTO WatchList (watchListID, itemID, buyerID) VALUES (NULL, '$item_id', '$buyer_id')";
  $result = mysqli_query($connection, $query);
  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  $query = "DELETE FROM WatchList WHERE itemID ='1' AND buyerID='1'";
  $result = mysqli_query($connection, $query);
  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;
}
//mysqli_close($connection);
?>
