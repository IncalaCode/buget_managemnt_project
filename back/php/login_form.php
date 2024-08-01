<?php
require_once ('./varable_session.php');
require_once ('./connect.php');


function filter_input_data($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}


if($_SERVER['REQUEST_METHOD'] == "post"){
    
    $phone_number = filter_input_data($_POST['phone_number']);
    $password = filter_input_data($_POST['password']);

    // 
}