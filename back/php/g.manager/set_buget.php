<?php
require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');
require_once('./back/php/record.php');


if ($_SERVER['REQUEST_METHOD'] == "POST") {

}

function store_buget_now(){
    $connect = connect();
    $resualt = $connect->query("SELECT * FROM records where status = 1");
    $resualt = $resualt->fetch_assoc();
    $_SESSION['buget'] = $resualt;
}

if(isset($_POST['set_buget'])){
    $message = setbuget();
    $buget = get_buget();
    store_buget_now();
       return;
   }
?>