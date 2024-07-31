<?php

require_once ('varable_session.php');

if (!isset($_SESSION['user'])) {  
    // header("Location: index.php"); make sure that it has to go the login ok 
    return;
}

$message = array('status' => true, 'message' => " wellcome back " . $_SESSION['user']['name']);