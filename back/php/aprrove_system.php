<?php

require_once('./back/php/record.php');
require_once('./back/php/connect.php');
require_once('./back/php/varable_session.php');

// Handle approval and disapproval requests
if (isset($_POST['approve'])) {
    $reviewId = $_POST['review_id'];  // Assuming review ID is sent in the POST request
    $reviewTime = date('Y-m-d H:i:s', strtotime($_POST['date'])); 
    // Approve the review
    $message = approveReview($reviewId, $reviewTime);
}

if (isset($_POST['disapprove'])) {
    $reviewId = $_POST['review_id'];  // Assuming review ID is sent in the POST request
    $reviewTime = date('Y-m-d H:i:s', strtotime($_POST['date'])); 
    // Disapprove the review
    $message = disapproveReview($reviewId,$reviewTime);
}

// Function to handle review approval
function approveReview($reviewId, $reviewTime) {
    $connect = connect();
    $connect->begin_transaction();

    try {
        // Fetch the finance review details
        $stmt = $connect->prepare("SELECT id_code, code, amount FROM finance_review WHERE code = ? AND review_status = 'Pending' AND review_time = ?");
        $stmt->bind_param("ss", $reviewId, $reviewTime); // Use 'ss' for string parameters
        $stmt->execute();
        $stmt->bind_result($userId, $itemCode, $amount);
        $stmt->fetch();
        $stmt->close();

        if (!$itemCode || !$amount) {
            throw new Exception("Review not found or already processed.");
        }

        // Fetch the current budget data from the propsal table
        $stmt = $connect->prepare("SELECT data FROM propsal WHERE status = 1 AND code = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($dataJson);
        $stmt->fetch();
        $stmt->close();

        if ($dataJson) {
            // Decode the JSON data from the propsal table
            $data = json_decode($dataJson, true);

            // Update the budget in the propsal data
            $req_ibx_response = req_ibx($data, ['code' => $itemCode, 'amount' => $amount]);

            if ($req_ibx_response['status'] === 'success') {
                // Update the propsal table with the new budget data
                $updatedDataJson = json_encode($req_ibx_response['updatedData']);
                $stmt = $connect->prepare("UPDATE propsal SET data = ? WHERE code = ?");
                $stmt->bind_param("si", $updatedDataJson, $userId);

                if ($stmt->execute()) {
                    // Update the finance review status to "Approved"
                    $stmt = $connect->prepare("UPDATE finance_review SET review_status = 'Approved', review_time = NOW() WHERE code = ? AND review_time = ?");
                    $stmt->bind_param("ss", $reviewId, $reviewTime);

                    if ($stmt->execute()) {
                        // Update the records table with the new budget values
                        updateRecordsTable($itemCode, $amount);

                        $connect->commit();
                        return array("status" => "success", "message" => "Review approved successfully.");
                    } else {
                        throw new Exception("Failed to update finance review status.");
                    }
                } else {
                    throw new Exception("Failed to update propsal data.");
                }
            } else {
                throw new Exception($req_ibx_response['message']);
            }
        } else {
            throw new Exception("Budget data not found.");
        }
    } catch (Exception $e) {
        $connect->rollback();
        return array("status" => "error", "message" => $e->getMessage());
    }
}

// Function to handle review disapproval
function disapproveReview($reviewId, $reviewTime) {
    $connect = connect();
    $connect->begin_transaction();

    try {
        // Update the finance review status to "Disapproved"
        $stmt = $connect->prepare("UPDATE finance_review SET review_status = 'Rejected' WHERE review_time = ? and code = ?");

        $stmt->bind_param("ss",  $reviewId,$reviewTime);

        // Commit the transaction if everything went well
        $connect->commit();

        // Return success message
        return array("status" => "success", "message" => "Review disapproved successfully.");
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $connect->rollback();
        return array("status" => "error", "message" => $e->getMessage());
    } finally {
        // Close the prepared statement and connection
        if ($stmt) {
            $stmt->close();
        }
        $connect->close();
    }
}

// Function to update the records table after approval
function updateRecordsTable($itemCode, $amount) {
    $connect = connect();

    // Fetch the current records data
    $stmt = $connect->prepare("SELECT data FROM records WHERE status = 1");
    $stmt->execute();
    $stmt->bind_result($recordsJson);
    $stmt->fetch();
    $stmt->close();

    if ($recordsJson) {
        // Decode the JSON data
        $records = json_decode($recordsJson, true);

        // Find the index of "Item-code" and "buget" in the head array
        $itemCodeIndex = array_search("Item-code", $records['head']);
        $budgetIndex = array_search("buget", $records['head']);

        if ($itemCodeIndex !== false && $budgetIndex !== false) {
            // Update the budget in the records data
            foreach ($records['body'] as &$row) {
                $rowData = explode(": ", $row[$itemCodeIndex]);
                if ($rowData[0] == $itemCode) {
                    $budgetData = explode(": ", $row[$budgetIndex]);
                    $budgetData[0] -= $amount; // Update the budget
                    $row[$budgetIndex] = implode(": ", $budgetData);
                    break;
                }
            }

            // Encode the updated records data
            $updatedRecordsJson = json_encode($records);

            // Update the records table with the new data
            $stmt = $connect->prepare("UPDATE records SET data = ? WHERE status = 1");
            $stmt->bind_param("s", $updatedRecordsJson);
            $stmt->execute();
        } else {
            // Handle the case where "Item-code" or "buget" are not found
            return array("status" => "error", "message" => "Item-code or buget column not found."); 
        }
    }
}

?>