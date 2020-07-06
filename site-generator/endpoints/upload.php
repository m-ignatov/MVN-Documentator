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
$chosenTheme = $_POST['chosenTheme'];
$themesCssStyles = ['brisk', 'compote', 'condiments', 'coral', 'green', 'harbour', 'harvest', 'marsala', 'pebble', 'scholar', 'sky', 'uncorked'];
//css stylesheets names are xdoc-style-brisk, xdoc-style-compote etc. will use below for setting theme style according to the chosen option

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

    $projectService->persistProjects($filePath);
    $projectService->persistStudents($filePath);
    //  $result = $projectService->fetchAll();
} catch (Exception $e) {
    sendResponse($e->getMessage(), false);
    return;
}

// TODO: Process data with DOM
$xdocpath = "../maven/content/xdoc/index.xml.vm";
if (file_exists($xdocpath)) {
    $xml = new SimpleXMLElement($xdocpath, 0, TRUE);
    $xmlsubsections = $xml->body->section->subsection[0]->p;

    $attributeName = 'name'; //attribute to be edited of the 'section' element
    $query_projects = "SELECT * FROM projects";
    $studentsFields = ["firstName", "lastName", "courseName", "courseYear", "facultyNumber", "projectTasks", "manHours"];
    $projectService = new ProjectService();
    $connection = $projectService->getDbConnection();
    $result_projects = $projectService->executeQuery($query_projects);
    $parentElement = $xml->body->section;

    if ($result_projects->execute()) {
        $index = 0;
        $newdoc = new DOMDocument;
        $newdoc->loadXML(file_get_contents("../docs/sectionTemplate.xml")); // used to copy the <section> elememt as a template for all rows(projects) of the CSV. Easier than creating all <section> elements here, and better than copying the existing section because information is copied as well
        $sectionNode = $newdoc->getElementsByTagName("section")->item(0); 

        while ($row = $result_projects->fetch(PDO::FETCH_ASSOC)) {
            $doctosave = new DOMDocument;
            $doctosave->formatOutput = true;
            $doctosave->loadXML(file_get_contents($xdocpath));

            if ($index >= 1) {
                $sectionNode = $doctosave->importNode($sectionNode, true);
                $doctosave->getElementsByTagName("body")[0]->appendChild($sectionNode);
                $doctosave->save($xdocpath);
            }

            $currentSection = $doctosave->getElementsByTagName("section")[$index];
            $sectionName = $row['projectID'] . ". " . $row['projectName'];
            $currentSection->setAttribute("name", $sectionName);
            $subsections = $currentSection->getElementsByTagName("subsection");
            $subsections[0]->getElementsByTagName("p")[1]->nodeValue = $row['projectDescription'];;
            $subsections[1]->getElementsByTagName("p")[2]->nodeValue = $row['exampleResources'];
            $subsections[1]->getElementsByTagName("p")[4]->nodeValue = $row['usedResources'];
            $subsections[3]->getElementsByTagName("p")[1]->nodeValue = $row['githubLink'];
            $subsections[4]->getElementsByTagName("p")[1]->nodeValue = $row['presentationDate'];
            $subsections[4]->getElementsByTagName("p")[3]->nodeValue = $row['presentationTime'];
            $subsections[4]->getElementsByTagName("p")[5]->nodeValue = $row['presentationLink'];
            $doctosave->save($xdocpath);

            $projectID = $row['projectID'];
            $query_students = "SELECT * FROM students WHERE projectID = :value";
            $results_students = $connection->prepare($query_students);
            $results_students->bindParam(':value',$projectID, PDO::PARAM_INT);

            if ($results_students->execute()) { //removes empty rows from tbody which are 3 by default. Better than adding them one by one as this is slower and errors occur when trying to update the table content with DOM
                $studentIndex = 0;
                $tableBody = $doctosave->getElementsByTagName("tbody")[$index];//->getElementsByTagName("tr")[$studentIndex];
                while ($student_row = $results_students->fetch(PDO::FETCH_ASSOC)) {

                    $currentRow = $tableBody->getElementsByTagName("tr")[$studentIndex];
                    for ($i = 0; $i < 7; $i++) {
                       $currentRow->getElementsByTagName("td")[$i]->nodeValue = $student_row[$studentsFields[$i]];
                    }
                    $doctosave->save($xdocpath);
                    $studentIndex = $studentIndex + 1;
                }
                for($removeTrIndex = $studentIndex; $removeTrIndex < 3; $removeTrIndex++)
                {
                    $trToRemove = $tableBody->getElementsByTagName("tr")[$removeTrIndex];
                    $oldElement = $tableBody->removeChild($trToRemove);
                }
            }
            //   echo $doctosave->saveXML();
            
            $index = $index + 1;
            $doctosave->save($xdocpath);
        }
    }

    $pdo = null;
}


// Generate site
exec('cd ../maven & mvn clean site:site', $output);

$outputString = implode("\n", $output);
$success = strpos($outputString, 'BUILD SUCCESS') ? true : false;

if ($success) {
    $cssPath = '../style/themes/xdoc-style-'.$themesCssStyles[intval($chosenTheme)].'.css';
    copy($cssPath, '../maven/target/site/css/xdoc-style.css');
}

sendResponse($outputString, $success);

function sendResponse($message, $success)
{
    echo json_encode([
        'message' => $message,
        'success' => $success,
    ]);
}
