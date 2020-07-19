<?php

class XmlProcessor
{
    private $mavenBase = "../maven/content/";
    private $filePath;

    private $templatePath;
    private $sectionPath;
    private $sitePath;

    private $projectService;
    private $language;

    public function __construct($projectService, $language)
    {
        $this->projectService = $projectService;
        $this->language = $language;

        $this->filePath = $this->mavenBase . "xdoc/index.xml.vm";

        $this->templatePath = "../docs/" . $language . "/IndexXmlTemplate.xml";
        $this->sectionPath = "../docs/" . $language . "/sectionTemplate.xml";
        $this->sitePath = "../docs/" . $language . "/site.xml";
    }

    public function generate()
    {
        try {
            copy($this->sitePath, $this->mavenBase . "site.xml");
            copy($this->templatePath, $this->filePath);

            if (file_exists($this->filePath)) {
                $studentsFields = ["firstName", "lastName", "courseName", "courseYear", "facultyNumber", "projectTasks", "manHours"];

                $prjs = $this->projectService->fetchProjects();

                $newdoc = new DOMDocument;
                $newdoc->loadXML(file_get_contents($this->sectionPath)); // used to copy the <section> elememt as a template for all rows(projects) of the CSV. Easier than creating all <section> elements here, and better than copying the existing section because information is copied as well
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

                    for ($studentIndex = 1; $studentIndex < count($students); $studentIndex++) {
                        $tableRow = $doctosave->getElementsByTagName("tr")->item(1);
                        $tableBody->appendChild($tableRow->cloneNode(true));
                    }
                    $doctosave->save($this->filePath);

                    for ($studentIndex = 0; $studentIndex < count($students); $studentIndex++) {
                        $currentRow = $tableBody->getElementsByTagName("tr")[$studentIndex];
                        $studentRowName = $students[$studentIndex];

                        for ($i = 0; $i < 7; $i++) {
                            $currentRow->getElementsByTagName("td")[$i]->nodeValue = $studentRowName[$studentsFields[$i]];
                        }
                        $doctosave->save($this->filePath);
                    }

                    //used to remove redundant rows in the table, which should not exist
                    $currentTableRows = $tableBody->getElementsByTagName("tr");
                    if($studentIndex != count($currentTableRows))
                    {
                        for($i = count($currentTableRows); --$i >= count($students); ){
                            $toRemove = $currentTableRows->item($i);
                            $toRemove->parentNode->removeChild($toRemove);
                        }
                    }

                    $index++;
                    $doctosave->save($this->filePath);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}