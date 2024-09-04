<?php 

require_once('./back/php/connect.php');
require_once('./back/php/convert_pdf_to_docx.php');
require_once('./back/php/record.php');

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

        if(isset($_POST['approve'])){
            return;
        }

            if(isset($_POST['request'])){
                $message = submitForFinanceReview($_POST);
                return;
            }

                        if(isset($_POST['update'])){
                return;
            }
            
            

if(isset($_POST['submit'])){
    if($_POST['submit'] == "dowlode_dox" or $_POST['submit'] == "insert_ibx"){
        $message =  make_ibx();
        return;
     }

}

if(!isset($_POST['propsal']) or isset($_POST['set_buget'])  ){
            return;
}
if(isset($_SESSION['buget']['data']) ){
    $message = array("status" => "error", "message" => " Proposal was set , Unable to update ", 'navigateToSlide' => "uploadProposal");
    return;
}

        // Convert data to JSON
        $tableData = checktable();
        
        $code = $_POST['code'];
        $jsonData = $tableData ? json_encode($tableData) : json_encode([]);

        $time = date('Y-m-d H:i:s');
        $code = (!$code) ? $_SESSION['user']['id'] : $code;

        if(isset($_SESSION['buget']['data'])){
            $message = array("status" => "error", "message" => "Unable to update Proposal successfully", 'navigateToSlide' => "uploadProposal");
        }

        if (checkProposalExists($connect,$code)) {
            // Update existing proposal
            $updateResult = updateProposal($connect, $code, $jsonData, $time);
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

    }
}

function checkProposalExists($connect, $id) {
    $sql = "SELECT COUNT(*) AS count FROM propsal WHERE id = ? and status = 1";
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

function make_ibx() {

    $message = array("status" => "error", "message" => "oniy the total propsal will be send", 'navigateToSlide' => "uploadProposal");
    
    if ( is_array($_POST['headers'])) {
        array_pop($_POST['headers']);
    }
    if ($_POST['submit'] === "dowlode_dox") {
        $data = checktable();
        generateAndDownloadDocxTable($data,$_POST['total']);
    }
    if($_POST['code'] == "total"){

    if(date('Y-m-d', strtotime( $_SESSION['buget']['time'])) < date("Y-m-d") or !$_SESSION['buget']['data'] ){

    
        $intal  = intval(mb_split(":",$_POST['total'])[1]);
        $buget = intval($_SESSION['buget']['buget_limit']);
        if($buget >  $intal){
        $data = checktable();
        $data = ibx($data);
       $message =  update_ibx($data);
          store_buget_limit_now();
        }else{
            $message = array("status" => "error", "message" => "limited buget", 'navigateToSlide' => "uploadProposal");
        }
    }else{
        $message = array("status" => "error", "message" => "this year buget alrady set up to " . $_SESSION['buget']['time'], 'navigateToSlide' => "uploadProposal");
    }

}
    
    return $message;
}

function ibx($data) {
    $transformedBody = [];

    foreach ($data['body'] as $row) {
        $pairedRow = [];

        foreach ($row as $item) {
            // Assuming $item is a single value, we'll double it to create a "total: change" pair
            $pairedRow[] = "{$item} : {$item}";
        }

        $transformedBody[] = $pairedRow;
    }

    $data['body'] = $transformedBody;

    return $data;
}

function store_buget_limit_now(){
    $connect = connect();
    $resualt = $connect->query("SELECT * FROM records where status = 1");
    $resualt = $resualt->fetch_assoc();
    $_SESSION['buget'] = $resualt;
}

?>