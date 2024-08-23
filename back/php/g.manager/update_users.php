<?php

require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');

function filter_input_data_fitter($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}


    if (isset($_POST['update'])) {
        $connect = connect();
        // Get the user ID, username, and password from the POST request
        $user_id = $_SESSION['user']['id'];
        $username = filter_input_data_fitter($_POST['username']);
        $password = filter_input_data_fitter($_POST['password']);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Update the username and password for the given user ID
            $stmt = $connect->prepare("UPDATE employ SET username = ?, password = ? WHERE id = ?");
            $stmt->bind_param('ssi', $username, $hashed_password, $user_id);

            // Execute the statement
            if ($stmt->execute()) {
                $message = array("status" => "success", "message" => "Update successful.", 'navigateToSlide' => "User_list");
            } else {
                $message = array("status" => "error", "message" => "Update failed.", 'navigateToSlide' => "User_list");
            }

            $stmt->close();
            $connect->close();
        } catch (Exception $e) {
            $message = array("status" => "error", "message" => "Error: " . $e->getMessage(), 'navigateToSlide' => "User_list");
        }
    }

    
?>