<?php

require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');
require_once('./back/php/record.php');
// Function to sanitize input data
function filter_input_data($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    $connect = connect();
    
    
    if(isset($_POST['propsal'])){
        return;
    }

    if(isset($_POST['update'])){
           return;
       }
       if(isset($_POST['submit'])){
        return;
    }

    $role = filter_input_data($_POST['role']);
    $first_name = filter_input_data($_POST['first_name']);
    $last_name = filter_input_data($_POST['last_name']);
    $phone_number = filter_input_data($_POST['phone_number']);
    $password = filter_input_data($_POST['password']);
    $confirm_password = filter_input_data($_POST['confirm_password']);
    $username = filter_input_data($_POST['username']);
    $code = filter_input_data($_POST['code']);


    if ($password !== $confirm_password) {
        $message = array("status" => "error", "message" => "Passwords do not match.",'navigateToSlide' => "User_list");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        
        
        // Prepare the SQL statement
        $stmt = $connect->prepare("INSERT INTO employ (role, fname, lname, phonenum, password,username,code) VALUES (?, ?, ?, ?, ?,?,?)");

        // Bind parameters
        $stmt->bind_param('sssssss', $role, $first_name, $last_name, $phone_number, $hashed_password,$username,$code);

        // Execute the statement
        if ($stmt->execute()) {
            $message = array("status" => "success", "message" => "Registration successful.",'navigateToSlide' => "User_list");
        } else {
            $message = array("status" => "error", "message" => "Registration failed.",'navigateToSlide' => "User_list");
        }

        $stmt->close();
        $connect->close();

    } catch (Exception $e) {
        $message = array("status" => "error", "message" => "Error: " . $e->getMessage(),'navigateToSlide' => "User_list");
    }

}

?>