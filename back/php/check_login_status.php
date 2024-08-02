<?php
require_once('varable_session.php'); // Ensure this file starts the session and includes session variables

function check() {
    if (!isset($_SESSION['user'])) {  
        // Redirection to login page in case the user is not logged in
        header("location: login.php");
        exit();
    }
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

// Call the check function to ensure the user is logged in
check();
$message = array('status' => "success", 'message' => "Welcome back " . $_SESSION['user']['username']);
?>