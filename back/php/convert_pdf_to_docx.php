<?php
require 'vendor/autoload.php';


use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use setasign\Fpdi\Fpdi;
use PhpOffice\PhpWord\Style\Table;

function convertPdfToDocx($pdfFilePath, $outputDir) {
    // Ensure the output directory exists
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Prepare the output file path
    $docxFilePath = $outputDir . '/' . basename($pdfFilePath, '.pdf') . '.docx';

    // Parse the PDF file for text
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFilePath);
    $pages = $pdf->getPages();

    // Create a new Word document
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    foreach ($pages as $page) {
        $lines = explode("\n", $page->getText());
        
        foreach ($lines as $line) {
            // Check for bold formatting indicator and apply formatting
            $bold = strpos($line, '[BOLD]') !== false;
            if ($bold) {
                $section->addText($line, array('bold' => true, 'name' => 'Arial', 'size' => 12));
            } else {
                $section->addText($line, array('name' => 'Arial', 'size' => 12));
            }

            // Add a new line (paragraph break) after each line to preserve spacing
            $section->addTextBreak();
        }
    }

    // // Extract images using FPDI
    // $pdf = new FPDI();
    // $pageCount = $pdf->setSourceFile($pdfFilePath);

    // // Iterate through each page to detect images
    // for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
    //     $pdf->importPage($pageNumber);
    //     $pdf->useTemplate($pdf->importPage($pageNumber));

    //     $annotations = $pdf->getAnnotations();
    //     foreach ($annotations as $annotation) {
    //         // Only process image annotations
    //         if ($annotation['Subtype'] == 'Stamp' || $annotation['Subtype'] == 'Image') {
    //             $imageContent = $pdf->getImageData($annotation['Image']);
    //             $tempImagePath = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.jpg';
    //             file_put_contents($tempImagePath, $imageContent);
    //             $section->addImage($tempImagePath, array('width' => 600, 'height' => 400, 'align' => 'center'));
    //             // Add space after image
    //             $section->addTextBreak(1);
    //         }
    //     }
    // }

    // Save the Word document
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($docxFilePath);

    return $docxFilePath;
}

function saveFilePathToDatabase($topic, $filepath, $description) {
    $conn = connect();
    $stmt = $conn->prepare("INSERT INTO proposals (employee_id, topic, description, dir_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user']['id'], $topic, $description, $filepath);
    $stmt->execute();
    $stmt->close();
    return true;
}

function generateAndDownloadDocxTable($data, $totalBudget) {
    // Extract headers and rows from the data
    $headers = $data['head'];
    $rows = $data['body'];

    // Create a new PHPWord object
    $phpWord = new PhpWord();

    // Add a new section to the document
    $section = $phpWord->addSection();

    // Add a table to the section with proper width
    $table = $section->addTable(['width' => 100 * 50]);

    // Add table headers
    $table->addRow();
    foreach ($headers as $header) {
        $table->addCell(2000)->addText($header);  // Adjust cell width if needed
    }

    // Add table rows
    foreach ($rows as $row) {
        $table->addRow();
        foreach ($row as $cell) {
            $table->addCell(2000)->addText($cell);  // Adjust cell width if needed
        }
    }

    // Add two empty lines
    $section->addTextBreak(2);

    // Add the total budget text
    $section->addText($totalBudget);

    // Save the document as a temporary DOCX file
    $filename = 'table.docx';
    $temp_file = tempnam(sys_get_temp_dir(), 'phpword');
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($temp_file);

    // Set headers to download the file
    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Expires: 0');
    readfile($temp_file);
    unlink($temp_file); // Remove the temporary file
    exit;
}


?>