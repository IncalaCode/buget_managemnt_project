<?php 

// require_once('./back/php/varable_session.php');
// require_once('./back/php/connect.php');

$connect = connect();

$sql = "SELECT * FROM proposal where code=" . $_SESSION['user']['code'] . $GLOBALS['url'] == "b_manager" ? " and status = true":""; // Select all columns from proposal table
$result = $connect->query($sql)->fetch_assoc();
$result = json_encode(array($result));

echo "<script> window.data = JSON.prase('$result') </script>"

?>