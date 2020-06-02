<?php
require_once "../models/CsvValidator.php";
require_once "../models/Project.php";

$fileInputName = 'dataFile';

if (!$_SERVER['REQUEST_METHOD'] === 'POST' || !isset($_FILES[$fileInputName])) {
    sendResponse('No file uploaded', false);
    return;
}

$file = $_FILES[$fileInputName];
$errors = CsvValidator::validate($file);

if ($errors) {
    sendResponse($errors, false);
    return;
}

// Load data to DB


// Process data with SimpleXML


// Generate site
exec('cd ../maven & mvn clean site:site', $output);

$outputString = implode("\n", $output);
$success = strpos($outputString, 'BUILD SUCCESS') ? true : false;

sendResponse($outputString, $success);

// $user = new User(
//     $phpInput['firstName'],
//     $phpInput['lastName'],
//     $phpInput['courseYear'],
//     $phpInput['courseName'],
//     $phpInput['facultyNumber'],
//     $phpInput['groupNumber'],
//     $phpInput['birthday'],
//     $phpInput['zodiac'],
//     $phpInput['link'],
//     $phpInput['photo'],
//     $phpInput['motivation'],
//     $phpInput['signature']
// );

// try {
//     $user->validate();
//     $user->storeInDb();

//     echo json_encode(['success' => true]);
// } catch (Exception $e) {
//     echo json_encode([
//         'success' => false,
//         'message' => $e->getMessage(),
//     ]);
// }

function sendResponse($message, $success)
{
    echo json_encode([
        'message' => $message,
        'success' => $success,
    ]);
}
