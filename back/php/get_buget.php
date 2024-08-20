<?php
require_once('./back/php/connect.php');
require_once('./back/php/director/add_propsal.php');

$connect = connect();
$escapedJsonData = '';

if ($_SESSION['user']['role'] == "g_manager" || $_SESSION['user']['role'] == "b_manager" || $_SESSION['user']['role'] == "finance") {
    $sql = "SELECT time, buget_limit, data FROM records WHERE status = '1'";

    // Execute the query
    $result = $connect->query($sql);

    // Fetch the result as an associative array
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        if (!empty($data)) {
            // Convert the processed data to JSON format
            $jsonData = json_encode($data);
            // Escape the JSON string for JavaScript
            $escapedJsonData = addslashes($jsonData);
        }
    }
} else {
    $code = intval($_SESSION['user']['code']); // Cast to int to prevent SQL injection
    $sql = "SELECT id, data, code, time FROM propsal WHERE code = $code AND status = 1";

    // Execute the query
    $result = $connect->query($sql);

    // Fetch the result as an associative array
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        if (!empty($data)) {
            // Process the last record's data
            $data = json_decode($data[0]['data'], true);
             array_pop($data['head']);
            $dataProcessed = ibx($data); // Decode JSON and process with ibx

            // Convert the processed data to JSON format
            $jsonData = json_encode($dataProcessed);
            // Escape the JSON string for JavaScript
            $escapedJsonData = addslashes($jsonData);
        }
    }
}

// Inject the escaped JSON data into JavaScript
echo "<script>
    window.buget = JSON.parse('$escapedJsonData');
    console.log(window.buget);
</script>";
?>