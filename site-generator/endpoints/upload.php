<?php
require_once "../services/CsvValidator.php";
require_once "../services/ProjectService.php";
require_once "../services/XmlProcessor.php";

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

$projectService = new ProjectService();

//Set custom folder
// TODO insert foldername in projects table
$targetFolder = $_POST['folderName'];
$targetPath = "../maven/target/".$targetFolder;
$message = file_exists($targetPath)? 'This folder already exists. Choose another folder name':'Placeholder';
$success =  file_exists($targetPath)? false:true;

if(!$success)
 {
    sendResponse($message, $success);
    exit;
 }

try {
    $projectService->persistProjects($filePath);
    $projectService->persistStudents($filePath);
} catch (Exception $e) {
    sendResponse($e->getMessage(), false);
    return;
}

// Process data
$language = $_POST['language'];
$xmlProcessor = new XmlProcessor($projectService, $language);

try {
    $xmlProcessor->generate();
} catch (Exception $e) {
    sendResponse($e->getMessage(), false);
    return;
}

// Add style template
$chosenTheme = $_POST['chosenTheme'];
$themesCssStyles = ['brisk', 'compote', 'condiments', 'coral', 'green', 'harbour', 'harvest', 'marsala', 'pebble', 'scholar', 'sky', 'uncorked'];
//css stylesheets names are xdoc-style-brisk, xdoc-style-compote etc. will use below for setting theme style according to the chosen option

$cssPath = '../style/themes/xdoc-style-' . $themesCssStyles[intval($chosenTheme)] . '.css';
copy($cssPath, '../maven/content/resources/css/xdoc-style.css');


// Generate site
exec('cd ../maven & mvn site', $output);

rename("../maven/target/site", $targetPath);

$outputString = implode("\n", $output);

$message = strpos($outputString, 'BUILD SUCCESS') ? 'Site generation success' : 'Site generation failed';
$success = strpos($outputString, 'BUILD SUCCESS') ? true : false;

sendResponse($message, $success);

function sendResponse($message, $success)
{
    echo json_encode([
        'message' => $message,
        'success' => $success,
    ]);
}
