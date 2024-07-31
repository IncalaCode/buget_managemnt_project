<?php

require_once ('varable_session.php');

// Function to sanitize input data
function filter_input_data($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['submit'])) {
        $message = array("status" => false, "message" => "No input was found !!");
        exit;
    }

    $role = filter_input_data($_POST['role']);
    $first_name = filter_input_data($_POST['fname']);
    $last_name = filter_input_data($_POST['lname']);
    $phone_number = filter_input_data($_POST['phonenum']);
    $password = filter_input_data($_POST['password']);
    $confirm_password = filter_input_data($_POST['confirm_password']);

    
    if ($password !== $confirm_password) {
        $message = array("status" => false, "message" => "Passwords do not match.");
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO users (role, fname, lname, phonenum, password) VALUES (:role, :fname, :lname, :phonenum, :password)");

        // Bind parameters
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':fname', $first_name);
        $stmt->bindParam(':lname', $last_name);
        $stmt->bindParam(':phonenum', $phone_number);
        $stmt->bindParam(':password', $hashed_password);

        // Execute the statement
        $stmt->execute();

        $message = array("status" => true, "message" => "Registration successful.");
    } catch (Exception $e) {
        $message = array("status" => false, "message" => "Error: " . $e->getMessage());
    }
}