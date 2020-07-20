<?php
require_once "../models/Database.php";

class ProjectService
{
    private $database;
    private $folder;

    public function __construct($folder)
    {
        $this->folder = $folder;
        $this->database = new Database();
    }

    public function persistProjects($file): void
    {
        $this->executeQuery("LOAD DATA LOCAL INFILE '" . $file .
            "' INTO TABLE projects FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES (projectID, projectName, projectDescription, exampleResources, usedResources, githubLink, presentationDate, presentationTime, presentationLink) SET folderName = '" . $this->folder . "'");
    }

    public function persistStudents($file): void
    {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $skipHeaderRow = true;

            while (($row = fgetcsv($handle, ",")) !== FALSE) {
                if ($skipHeaderRow) {
                    $skipHeaderRow = false;
                    continue;
                }
                $studentIndex = 9;
                $step = 7;

                while (isset($row[$studentIndex])) {
                    if (!$this->isValidStudent(array_slice($row, $studentIndex, $step))) {
                        $studentIndex += $step;
                        continue;
                    }
                    $this->executeQuery('INSERT INTO students (projectID, firstName, lastName, courseName, courseYear, facultyNumber, projectTasks, manHours, folderName) VALUES ("' . $row[0] . '","' . $row[$studentIndex] . '","' . $row[$studentIndex + 1] . '","' . $row[$studentIndex + 2] . '","' . $row[$studentIndex + 3] . '","' . $row[$studentIndex + 4] . '","' . $row[$studentIndex + 5] . '","' . $row[$studentIndex + 6] . '","' . $this->folder . '")');
                    $studentIndex += $step;
                }
            }
            fclose($handle);
        }
    }

    private function isValidStudent($array): bool
    {
        foreach ($array as $value) {
            if (!$value) {
                return false;
            }
        }
        return true;
    }

    public function fetchProjects(): array
    {
        $result = $this->executeQuery('SELECT * FROM projects WHERE folderName = "' . $this->folder . '"');
        return $result->fetchAll();
    }

    public function fetchStudentsByProjectId($projectId): array
    {
        $params = [':value', $projectId, PDO::PARAM_INT, ':folder', $this->folder, PDO::PARAM_STR_CHAR];
        $result = $this->executeQuery("SELECT * FROM students WHERE projectID = :value AND folderName = :folder", $params);

        return $result->fetchAll();
    }

    public function fetchStudents(): array
    {
        $result = $this->executeQuery('SELECT * FROM students WHERE folderName="' . $this->folder . '"');
        return $result->fetchAll();
    }

    private function executeQuery($query, $params = NULL)
    {
        $connection = $this->getDbConnection();
        $statement = $connection->prepare($query);

        if ($params) {
            for ($i = 0; $i < count($params); $i += 3) {
                $statement->bindParam($params[$i], $params[$i + 1], $params[$i + 2]);
            }
        }
        if (!$statement->execute()) {
            throw new Exception("Request failed, try again later");
        }
        return $statement;
    }

    private function getDbConnection()
    {
        return $this->database->getConnection();
    }
}
