<?php 

// require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');

// Initialize the database connection
$connect = connect();

// Construct the SQL query securely
$code = $connect->real_escape_string($_SESSION['user']['code']);
$statusCondition = ($GLOBALS['url'] == "b_manager") ? "  status = '1'" : "code = '$code' ";

$sql = "SELECT id,data,code,time FROM propsal WHERE $statusCondition";

// Execute the query
$result = $connect->query($sql);

// Fetch the result as an associative array
$data = $result->fetch_all(MYSQLI_ASSOC);

// Convert the data to JSON format
$jsonData = json_encode($data);
// Escape the JSON string for JavaScript
$escapedJsonData = addslashes($jsonData);

// Inject the escaped JSON data into JavaScript
echo "<script>
    window.data = JSON.parse('[$escapedJsonData]');
    console.log(window.data);
</script>";

?>