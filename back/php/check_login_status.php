<?php

require_once ('varable_session.php');

if (!isset($_SESSION['user'])) {  
   
    header("Location: index.php",true,200);
    exit(); // Make sure to exit after redirection
}

$message = array('status' => true, 'message' => " wellcome back " . $_SESSION['user']['name']);