<?php
function  setbuget(){
    require_once('./back/php/connect.php');
    
    $connect = connect();
    $ststus = 1;
    $stmt = $connect->prepare("INSERT INTO records (id,code, time,status,buget_limit) VALUES (?, ?,?,?,?)");

    $stmt->bind_param("iisii",$_SESSION['user']['id'],$_SESSION['user']['code'] ,$_POST['budgetDuration'] , $ststus,$_POST['buget_limit']);
  
    if($stmt->execute()){
        $message = array("status" => "success", "message" => "buget has been set.",'navigateToSlide' => "viewStatus");
    }else{
        $message = array("status" => "error", "message" => "failed to set the buget",'navigateToSlide' => "viewStatus");
    }
    return $message;
}

function get_buget() {
    require_once('./back/php/connect.php');
    $connect = connect();
    
    $stmt = $connect->prepare("SELECT time, status, buget_limit,data FROM records WHERE id = ? AND code = ?");
    $stmt->bind_param("is", $_SESSION['user']['id'], $_SESSION['user']['code']);
    
    if ($stmt->execute()) {
        $stmt->bind_result($time, $status, $buget_limit,$data);
        if ($stmt->fetch()) {
            $buget_data = array(
                "status" => "success",
                "time" => $time,
                "status_code" => $status,
                "buget_limit" => $buget_limit
            );
        } else {
            $buget_data = array("status" => "error", "message" => "No budget found.");
        }
    } else {
        $buget_data = array("status" => "error", "message" => "Failed to retrieve the budget.");
    }

    return $buget_data;
}


function update_ibx($data) {
    require_once('./back/php/connect.php');
    $connect = connect();
    
    // Encode data to JSON format
    $data = json_encode($data);

    // Prepare the SQL statement
    $stmt = $connect->prepare("UPDATE records SET data = ? WHERE  status = 1");


    // Bind parameters
    $stmt->bind_param("s", $data);

    // Execute the statement
    if ($stmt->execute()) {
        $message = array(
            "status" => "success",
            "message" => "Budget has been updated.",
            'navigateToSlide' => "viewStatus"
        );
    } else {
        $message = array(
            "status" => "error",
            "message" => "Failed to update the budget: " . $stmt->error,
            'navigateToSlide' => "viewStatus"
        );
    }

    // Close statement and connection
    $stmt->close();
    $connect->close();

    return $message;
}

function req_ibx($data, $budget_request) {
    $transformedBody = [];
    $budgetUpdated = false;

    foreach ($data['body'] as $row) {
        $pairedRow = [];
        
        $itemCode = $row[1];
        $currentAmount = $row[2];
        $setAmount = $currentAmount;

        if ($itemCode == $budget_request['code']) {
            $currentAmount -= $budget_request['amount'];
            $budgetUpdated = true;
        }

        $pairedRow[] = "{$row[0]}: {$row[0]}"; // r.no
        $pairedRow[] = "{$itemCode}: {$itemCode}"; // item code
        $pairedRow[] = "{$currentAmount}: {$setAmount}"; // buget in the "current amount: set amount" format

        $transformedBody[] = $pairedRow;
    }

    $data['body'] = $transformedBody;

    // Generate the appropriate message
    if ($budgetUpdated) {
        $message = array(
            "status" => "success", 
            "message" => "Budget has been updated.", 
            'navigateToSlide' => "viewStatus"
        );
    } else {
        $message = array(
            "status" => "error", 
            "message" => "Failed to update the budget. Item code not found.", 
            'navigateToSlide' => "viewStatus"
        );
    }

    return $message;
}

function submitForFinanceReview($budget_request) {
    require_once('./back/php/connect.php');
    $connect = connect();

    // Fetch the current budget data from the proposal table
    $stmt = $connect->prepare("SELECT data FROM propsal WHERE code = ? AND id = ? AND status = 1");
    $stmt->bind_param("ii", $_SESSION['user']['code'], $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->bind_result($dataJson);
    $stmt->fetch();
    $stmt->close();

    if ($dataJson) {
        // Decode the JSON data
        $data = json_decode($dataJson, true);

        // Check if the budget is sufficient
        $sufficientBudget = false;
        if (isset($data['body'])) {
            foreach ($data['body'] as &$row) {
                if ($row[1] == $budget_request['code']) { // Assuming $row[1] is the Item-code
                    $currentBudget = floatval($row[2]); // Assuming $row[2] is the budget value
                    if ($currentBudget >= $budget_request['amount']) {
                        $sufficientBudget = true;
                        break;
                    }
                }
            }
        }

        if ($sufficientBudget) {
            // Insert the budget request into the finance_review table
            $stmt = $connect->prepare("INSERT INTO finance_review (id_code, code, amount, review_status, review_time) VALUES (?, ?, ?, 'Pending', NOW())");
            $stmt->bind_param("isi", $_SESSION['user']['code'], $budget_request['code'], $budget_request['amount']);
          
            if ($stmt->execute()) {
                $message = array("status" => "success", "message" => "Budget request submitted for finance review.", 'navigateToSlide' => "viewStatus");
            } else {
                $message = array("status" => "error", "message" => "Failed to submit budget request for finance review.", 'navigateToSlide' => "viewStatus");
            }
        } else {
            $message = array("status" => "error", "message" => "Insufficient budget or Budget data not found for the specified code.", 'navigateToSlide' => "viewStatus");
        }
    } else {
        $message = array("status" => "error", "message" => "Budget data not found for the specified code.", 'navigateToSlide' => "viewStatus");
    }

    return $message;
}


function checkAndProcessCode($code, $amount) {
    require_once('./back/php/connect.php');
    $connect = connect();
    $message = array();

    // Check if the code exists in the records table
    $stmt = $connect->prepare("SELECT id FROM records WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Code exists, proceed with transaction and finance review

        // Insert into transactions table
        $stmt = $connect->prepare("INSERT INTO transactions (id_code,code, amount, transaction_date) VALUES (?,?, ?, NOW())");
        $stmt->bind_param("isd", $_SESSION['user']['code'],$code, $amount);

        if ($stmt->execute()) {
            // Insert into finance_review table
            $stmt = $connect->prepare("INSERT INTO finance_review (id_code,code, amount, review_status) VALUES (?, ?, 'Pending')");
            $stmt->bind_param("isd", $_SESSION['user']['code'],$code, $amount);

            if ($stmt->execute()) {
                $message = array(
                    "status" => "success",
                    "message" => "Transaction recorded and finance review initiated.",
                    'navigateToSlide' => "viewStatus"
                );
            } else {
                $message = array(
                    "status" => "error",
                    "message" => "Failed to initiate finance review.",
                    'navigateToSlide' => "viewStatus"
                );
            }
        } else {
            $message = array(
                "status" => "error",
                "message" => "Failed to record transaction.",
                'navigateToSlide' => "viewStatus"
            );
        }
    } else {
        // if  Code does not exist
        $message = array(
            "status" => "error",
            "message" => "Code does not exist.",
            'navigateToSlide' => "viewStatus"
        );
    }

    return $message;
}



?>