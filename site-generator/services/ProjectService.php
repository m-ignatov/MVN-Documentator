<?php
require_once "../models/Database.php";

class ProjectService
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function persistProjects($file): void
    {
        $this->executeQuery("LOAD DATA LOCAL INFILE '" . $file .
            "' INTO TABLE projects FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES (projectID, projectName, projectDescription, exampleResources, usedResources, githubLink, presentationDate, presentationTime, presentationLink)");
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
                    $this->executeQuery('INSERT INTO students (projectID, firstName, lastName, courseName, courseYear, facultyNumber, projectTasks, manHours) VALUES ("' . $row[0] . '","' . $row[$studentIndex] . '","' . $row[$studentIndex + 1] . '","' . $row[$studentIndex + 2] . '","' . $row[$studentIndex + 3] . '","' . $row[$studentIndex + 4] . '","' . $row[$studentIndex + 5] . '","' . $row[$studentIndex + 6] . '")');
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
        $result = $this->executeQuery("SELECT * FROM projects");
        return $result->fetchAll();
    }

    public function fetchStudentsByProjectId($projectId): array
    {
        $params = [':value', $projectId, PDO::PARAM_INT];
        $result = $this->executeQuery("SELECT * FROM students WHERE projectID = :value", $params);

        return $result->fetchAll();
    }

    public function fetchStudents(): array
    {
        $result = $this->executeQuery("SELECT * FROM students");
        return $result->fetchAll();
    }

    private function executeQuery($query, $params = NULL)
    {
        $connection = $this->getDbConnection();
        $statement = $connection->prepare($query);

        if ($params) {
            $statement->bindParam($params[0], $params[1], $params[2]);
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
