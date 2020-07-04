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
$xdocpath = "../maven/content/xdoc/index.xml.vm";
$response = "response"; 

$products = array();
if (file_exists($xdocpath)) {
   $xml = new SimpleXMLElement($xdocpath, 0, TRUE);
   $xmlsubsections = $xml->body->section->subsection[0]->p;

  /* 
   $projectID 
   $projectName
   $projectDescription = $xml->body->section->subsection[0]->p[1];
   $exampleResources = $xml->body->section->subsection[1]->p[2];
   $usedResources = $xml->body->section->subsection[1]->p[4];
   $githubLink = $xml->body->section->subsection[3]->p[1];
   $presentationDate = $xml->body->section->subsection[4]->p[1];
   $presentationTime = $xml->body->section->subsection[4]->p[3];
   $presentationLink = $xml->body->section->subsection[4]->p[5];
   */

    $attributeName = 'name'; //attribute to be edited of the 'section' element
    $query = "SELECT * FROM projects";
    $projectService = new ProjectService();
    $projectService->getDbConnection();
    $result = $projectService->executeQuery($query);
    if($result->execute())
      {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $xml->body->section->attributes()->$attributeName = $row['projectID'] . ". " . $row['projectName'];
            $xml->body->section->subsection[0]->p[1] = $row['projectDescription'];
            $xml->body->section->subsection[1]->p[2] = $row['exampleResources'];
            $xml->body->section->subsection[1]->p[4] = $row['usedResources'];
            $xml->body->section->subsection[3]->p[1] = $row['githubLink'];
            $xml->body->section->subsection[4]->p[1] = $row['presentationDate'];
            $xml->body->section->subsection[4]->p[3] = $row['presentationTime'];
            $xml->body->section->subsection[4]->p[5] = $row['presentationLink'];
            
            $xml->saveXML($xdocpath);
             }
      }

    $pdo = null;
}


// Generate site
exec('cd ../maven & mvn clean site:site', $output);

$outputString = implode("\n", $output);
$success = strpos($outputString, 'BUILD SUCCESS') ? true : false;

if($success)
{
    copy('../style/xdoc-style.css', '../maven/target/site/css/xdoc-style.css');
}

sendResponse($outputString, $success);

function sendResponse($message, $success)
{
    echo json_encode([
        'message' => $message,
        'success' => $success,
    ]);
}