<?php
require_once('./connect.php');
require 'vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['code'])) {
    $code = $_POST['code'];

    // Generate the DOCX report
    $filename = generateReport($code);

    // Set headers to download the file
    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Expires: 0');
    readfile($filename);
    unlink($filename);
    exit;
}

// Function to generate the DOCX report
function generateReport($code) {
    // Retrieve the data from the `records` table based on the code
    $connect = connect();
    $stmt = $connect->prepare("SELECT data FROM records WHERE code = ?");
    $stmt->bind_param("i", $code);
    $stmt->execute();
    $stmt->bind_result($dataJson);
    $stmt->fetch();
    $stmt->close();

    // Decode the JSON data
    $data = json_decode($dataJson, true);

    // Use PHPWord or similar library to generate the DOCX
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    $table = $section->addTable(['width' => 200 * 80]);

    // Add table header with new columns for Initial Budget, Current Budget, and Percentage
    $table->addRow();
    foreach ($data['head'] as $header) {
        $table->addCell()->addText($header);
    }
    $table->addCell()->addText('Initial Budget');
    $table->addCell()->addText('Current Budget');
    $table->addCell()->addText('Percentage');

    // Add table rows with initial budget, current budget, and percentage calculations
    foreach ($data['body'] as $row) {
        $table->addRow();
        $initialBudget = 0;
        $currentBudget = 0;

        foreach ($row as $key => $cell) {
            list($current, $initial) = explode(':', $cell);
            $table->addCell()->addText($current);


            // Check if the column is 'buget' to calculate initial and current budgets
            if (strpos($data['head'][$key], 'buget') !== false) {
                // Split the value by colon
                list($current, $initial) = explode(':', $cell);
                $currentBudget = (float)$current;
                $initialBudget = (float)$initial;
            }
        }

        // Calculate percentage as (current / initial) * 100
        if ($initialBudget != 0) {
            $percentage = ($currentBudget / $initialBudget) * 100;
        } else {
            $percentage = 0;
        }

        // Add initial budget, current budget, and percentage to the row
        $table->addCell()->addText(number_format($initialBudget, 2));
        $table->addCell()->addText(number_format($currentBudget, 2));
        $table->addCell()->addText(number_format($percentage, 2) . '%');
    }

    // Save the document as a temporary DOCX file
    $filename = 'report_' . $code . '.docx';
    $temp_file = tempnam(sys_get_temp_dir(), 'phpword');
    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($temp_file);

    // Move the temp file to the final destination
    rename($temp_file, $filename);

    return $filename;
}
?>