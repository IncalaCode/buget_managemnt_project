<?php

require ('./back/php/convert_pdf_to_docx.php');

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
                saveFilePathToDatabase("", $convertedFilePath, "");
                $message = array('status' => 'success', 'message' => 'Proposal was uploaded successfully.');
            } else {
                $message = array('status' => 'success', 'message' => 'Proposal was uploaded successfully.');
            }
        } elseif ($fileType === 'docx') {
            $targetFile = $targetDir . '/' . basename($_FILES["file"]["name"]);

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                saveFilePathToDatabase("", $targetFile, "");
                $message = array('status' => 'success', 'message' => 'Proposal was uploaded successfully.');
            } else {
                $message = array('status' => 'error', 'message' => 'Proposal was not uploaded successfully.');
            }
        }
    } catch (Exception $e) {
        $message = array('status' => 'error', 'message' => $e->getMessage());
    }
}

?>