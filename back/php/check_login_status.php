<?php
require_once('varable_session.php'); // Ensure this file starts the session and includes session variables
require_once('record.php');
function check_user() {
    if (!isset($_SESSION['user'])) {  
        header("location: login.php");
        exit();
    }
    return true;
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST['logout'])){
            // Destroy session
    session_destroy();

    // Redirect to the login page
    header("Location: ../../login.php");
    exit();
    }
}
function check_url(){
    if($GLOBALS['url'] != $_SESSION['user']['role']){
        header("location: ".$_SESSION['user']['role'] . ".php");
        exit();
    }
    return true;
}

// Call the check function to ensure the user is logged in
if(check_user()){   
    if(check_url()){
        if(!isset($message)){
            $message = array('status' => "success", 'message' => "Welcome back " . $_SESSION['user']['username']);
        }
        
    } 
    $buget = get_buget();
}

?>