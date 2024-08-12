<?php 

require_once('./back/php/connect.php');

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function checktable() {
    $headers = null;
    $body = null;
    $footer = null;
    
    if (isset($_POST['headers']) && isset($_POST['rows'])) {
        // Sanitize headers
        foreach ($_POST['headers'] as $header) {
            $headers[] = sanitizeInput($header);
        }

        // Process table rows
        foreach ($_POST['rows'] as $index => $row) {
            $body[$index] = [];
            foreach ($row as $cell) {
                $body[$index][] = sanitizeInput($cell);
            }
        }

        // Process footer if it exists
        if (isset($_POST['footer'])) {
            foreach ($_POST['footer'] as $index => $row) {
                $footer[$index] = [];
                foreach ($row as $cell) {
                    $footer[$index][] = sanitizeInput($cell);
                }
            }
        }

        return array(
            'head' => $headers,
            'body' => $body,
            'footer' => $footer,
        );
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $stmt = null;
    try {
        $connect = connect();

        // Convert data to JSON
        $tableData = checktable();
        $jsonData = $tableData ? json_encode($tableData) : json_encode([]);

        $time = date('Y-m-d H:i:s');

        if (checkProposalExists($connect, $_SESSION['user']['id'])) {
            // Update existing proposal
            $updateResult = updateProposal($connect, $_SESSION['user']['id'], $jsonData, $time);
            if (!$updateResult) {
                $message = array("status" => "error", "message" => "Unable to update Proposal successfully", 'navigateToSlide' => "uploadProposal");
            } else {
                $message = array("status" => "success", "message" => "Proposal updated successfully", 'navigateToSlide' => "uploadProposal");
            }
        } else {
            $status = true;
            // Insert new proposal
            $sql = "INSERT INTO propsal (id, code, data, time, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("isssi", $_SESSION['user']['id'], $_SESSION['user']['code'], $jsonData, $time, $status);
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
        if ($stmt) {
            $stmt->close();
        }
        if ($connect) {
            $connect->close();
        }
    }
}

function checkProposalExists($connect, $id) {
    $sql = "SELECT COUNT(*) AS count FROM propsal WHERE id = ? and status = true";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function updateProposal($connect, $id, $jsonData, $time) {
    $sql = "UPDATE propsal SET data = ?, time = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ssi", $jsonData, $time, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

?>