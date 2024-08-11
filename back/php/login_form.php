<?php
require_once './back/php/connect.php';

function filter_input_data($data) {
    return filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = filter_input_data($_POST['username']);
    $password = filter_input_data($_POST['password']);


    try {
        $connect = connect();

        // Prepare the SQL statement to get the user's data
        $stmt = $connect->prepare("SELECT * FROM employ WHERE username = ?");
        $stmt->bind_param('s', $username);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Fetch the user data
                $user = $result->fetch_assoc();
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    $message = array("status" => "success", "message" => "Login successful.");
                    //redirect it to the given role
                    $_SESSION['user'] = $user;
                      header("location: ".$user['role'] . ".php");
                    //header("location: ../../g_manager.php");
                } else {
                    $message = array("status" => "error", "message" => "Invalid username or password.");
                }
            } else {
                $message = array("status" => "error", "message" => "User not found.");
            }
        } else {
            $message = array("status" => "error", "message" => ".");
        }

        $stmt->close();
        $connect->close();

    } catch (Exception $e) {
        $message = array("status" => "error", "message" => "Error: " . $e->getMessage());
    }
}