<?php

class XmlProcessor
{
    private $filePath;
    private $projectService;

    public function __construct($filePath, $projectService)
    {
        $this->filePath = $filePath;
        $this->projectService = $projectService;
    }

    public function generate()
    {
        try {
            copy('../docs/IndexXmlTemplate.xml', '../maven/content/xdoc/index.xml.vm');

            if (file_exists($this->filePath)) {
                $studentsFields = ["firstName", "lastName", "courseName", "courseYear", "facultyNumber", "projectTasks", "manHours"];
                $prjs = $this->projectService->fetchProjects();

                $newdoc = new DOMDocument;
                $newdoc->loadXML(file_get_contents("../docs/sectionTemplate.xml")); // used to copy the <section> elememt as a template for all rows(projects) of the CSV. Easier than creating all <section> elements here, and better than copying the existing section because information is copied as well
                $sectionNode = $newdoc->getElementsByTagName("section")->item(0);

                $index = 0;
                foreach ($prjs as $row) {
                    $doctosave = new DOMDocument;

                    $doctosave->formatOutput = true;
                    $doctosave->loadXML(file_get_contents($this->filePath));

                    if ($index >= 1) {
                        $sectionNode = $doctosave->importNode($sectionNode, true);
                        $doctosave->getElementsByTagName("body")[0]->appendChild($sectionNode);
                        $doctosave->save($this->filePath);
                    }

                    $currentSection = $doctosave->getElementsByTagName("section")[$index];
                    
                    $projectID = $row['projectID'];
                    $sectionName = $projectID . ". " . $row['projectName'];
                    $currentSection->setAttribute("name", $sectionName);
                    $subsections = $currentSection->getElementsByTagName("subsection");

                    $subsections[0]->getElementsByTagName("p")[1]->nodeValue = $row['projectDescription'];;
                    $subsections[1]->getElementsByTagName("p")[2]->nodeValue = $row['exampleResources'];
                    $subsections[1]->getElementsByTagName("p")[4]->nodeValue = $row['usedResources'];
                    $subsections[3]->getElementsByTagName("p")[1]->nodeValue = $row['githubLink'];
                    $subsections[4]->getElementsByTagName("p")[1]->nodeValue = $row['presentationDate'];
                    $subsections[4]->getElementsByTagName("p")[3]->nodeValue = $row['presentationTime'];
                    $subsections[4]->getElementsByTagName("p")[5]->nodeValue = $row['presentationLink'];
                    $doctosave->save($this->filePath);

                    $students = $this->projectService->fetchStudentsByProjectId($projectID);

                    $tableBody = $doctosave->getElementsByTagName("tbody")[$index];
                    $studentCount = min(count($students), 3);
                    
                    for ($studentIndex = 0; $studentIndex < $studentCount; $studentIndex++) {
                        $currentRow = $tableBody->getElementsByTagName("tr")[$studentIndex];
                        $student_row = $students[$studentIndex];

                        for ($i = 0; $i < 7; $i++) {
                            $currentRow->getElementsByTagName("td")[$i]->nodeValue = $student_row[$studentsFields[$i]];
                        }
                        $doctosave->save($this->filePath);
                    }
                    $index++;
                    $doctosave->save($this->filePath);
                }
            }
        } catch (Exception $e) {
            throw new Exception("Site processing failed");
        }
    }
}
