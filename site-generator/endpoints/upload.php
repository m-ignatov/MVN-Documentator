<?php
require_once "../services/CsvValidator.php";
require_once "../services/ProjectService.php";
require_once "../models/Project.php";

$fileInputName = 'dataFile';

if (!$_SERVER['REQUEST_METHOD'] === 'POST' || !isset($_FILES[$fileInputName])) {
    sendResponse('No file uploaded', false);
    return;
}

$file = $_FILES[$fileInputName];

try {
    CsvValidator::validate($file);
} catch (Exception $e) {
    sendResponse($e->getMessage(), false);
    return;
}

// Load data to DB
$filePath = str_replace('\\', '/', $file['tmp_name']);

try {
    $projectService = new ProjectService();

    $projectService->persist($filePath);
    $result = $projectService->fetchAll();
} catch (Exception $e) {
    sendResponse($e->getMessage(), false);
    return;
}

// TODO: Process data with SimpleXML


// Generate site
exec('cd ../maven & mvn clean site:site', $output);

$outputString = implode("\n", $output);
$success = strpos($outputString, 'BUILD SUCCESS') ? true : false;

sendResponse($outputString, $success);

function sendResponse($message, $success)
{
    echo json_encode([
        'message' => $message,
        'success' => $success,
    ]);
}
