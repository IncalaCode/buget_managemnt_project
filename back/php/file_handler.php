<?php

require ('./convert_pdf_to_docx.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $targetDir = "upload/" . $_POST['dir'];
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                throw new Exception('Failed to create directories.');
            }
        }

        $fileType = strtolower(pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION));

        if ($fileType === 'pdf') {
            $convertedFilePath = convertPdfToDocx($_FILES["file"]["tmp_name"], $targetDir);
            if ($convertedFilePath) { 
                if (!saveFilePathToDatabase("",basename($convertedFilePath), "")) {
                    throw new Exception('Failed to save file path to database.');
                }
                $message = array('status' => 'success', 'message' => 'Proposal was uploaded successfully.');
            } else {
                throw new Exception('Conversion failed.');
            }
        } elseif ($fileType === 'docx') {
            $targetFile = $targetDir . '/' . basename($_FILES["file"]["name"]);
            if (!saveFilePathToDatabase("",$targetDir .basename($convertedFilePath), "")) {
                throw new Exception('Failed to save file path to database.');
            }
            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                throw new Exception('Failed to move uploaded file.');
            }
            $message = array('status' => 'success', 'message' => 'Proposal was uploaded successfully.');
        } else {
            throw new Exception('Invalid file type.');
        }
    } catch (Exception $e) {
        $message = array('status' => 'error', 'message' => $e->getMessage());
    }
}

?>