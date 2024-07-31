<?php

require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');

// Function to sanitize input data
function filter_input_data($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $role = filter_input_data($_POST['role']);
    $first_name = filter_input_data($_POST['first_name']);
    $last_name = filter_input_data($_POST['last_name']);
    $phone_number = filter_input_data($_POST['phone_number']);
    $password = filter_input_data($_POST['password']);
    $confirm_password = filter_input_data($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $message = array("status" => "error", "message" => "Passwords do not match.",'navigateToSlide' => "Manage_account");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $connect = connect();
        
        // Prepare the SQL statement
        $stmt = $connect->prepare("INSERT INTO employ (role, fname, lname, phonenum, password) VALUES (?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param('sssss', $role, $first_name, $last_name, $phone_number, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            $message = array("status" => "success", "message" => "Registration successful.",'navigateToSlide' => "Manage_account");
        } else {
            $message = array("status" => "error", "message" => "Registration failed.",'navigateToSlide' => "Manage_account");
        }

        $stmt->close();
        $connect->close();

    } catch (Exception $e) {
        $message = array("status" => "error", "message" => "Error: " . $e->getMessage(),'navigateToSlide' => "Manage_account");
    }
}
?>