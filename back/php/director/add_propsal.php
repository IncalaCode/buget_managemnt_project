<?php 

require_once('./back/php/varable_session.php');
require_once('./back/php/connect.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        $connect = connect();
        $data = $_POST;

        // Convert data to JSON
        $jsonData = json_encode($data);

        $time = date('Y-m-d H:i:s');

        if (checkProposalExists($connect, $_SESSION['user']['id'])) {
            // Update existing proposal
            $updateResult = updateProposal($connect, $_SESSION['user']['id'], $jsonData, $time);
            if (!$updateResult) {
                throw new Exception('Error updating proposal');
            }
            $message = array("status" => "success", "message" => "Proposal updated successfully", 'navigateToSlide' => "uploadProposal");
        } else {
            $status = true;
            // Insert new proposal
            $sql = "INSERT INTO proposal (id, code, data, time, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("issii", $_SESSION['user']['id'], $_SESSION['user']['code'], $jsonData, $time, $status); // Assuming status is 1 for new proposal
            $stmt->execute();

            if ($stmt->error) {
                throw new Exception('Error inserting proposal');
            } else {
                $message = array("status" => "success", "message" => "Proposal sent successfully", 'navigateToSlide' => "uploadProposal");
            }
        }
    } catch (Exception $e) {
        $message = array("status" => "error", "message" => "Error: " . $e->getMessage(), 'navigateToSlide' => "User_list");
    } finally {
        $stmt->close();
        $connect->close();
    }
}

function checkProposalExists($connect, $id) {
    $sql = "SELECT COUNT(*) AS count FROM proposal WHERE id = ? and status = true";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function updateProposal($connect, $id, $jsonData, $time) {
    $sql = "UPDATE proposal SET data = ?, time = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssi", $jsonData, $time, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

?>