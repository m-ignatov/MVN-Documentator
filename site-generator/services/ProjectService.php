<?php
require_once "../models/Database.php";

class ProjectService
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function persistProjects($file): void //TODO update to N arg function with specifiable columns
    {
        $this->executeQuery("LOAD DATA LOCAL INFILE '" . $file .
            "' INTO TABLE projects FIELDS TERMINATED BY ';' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES (projectID, projectName, projectDescription, exampleResources, usedResources, githubLink, presentationDate, presentationTime, presentationLink)");
    }
    public function persistStudents($file): void
    {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $index = 0;
            $studentIndexArray = [9, 16, 23];
            $flag = true;
            while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
                if ($flag) {
                    $flag = false;
                    continue;
                }
                $index = 0;
                while ($index < 3) {
                    $studentIndex = $studentIndexArray[$index];
                    if (!$this->isValidStudent(array_slice($row, $studentIndex, 7))) {
                        $index = $index + 1;
                        continue;
                    }

                    $this->executeQuery('INSERT INTO students (projectID, firstName, lastName, courseName, courseYear, facultyNumber, projectTasks, manHours) VALUES ("' . $row[0] . '","' . $row[$studentIndex] . '","' . $row[$studentIndex + 1] . '","' . $row[$studentIndex + 2] . '","' . $row[$studentIndex + 3] . '","' . $row[$studentIndex + 4] . '","' . $row[$studentIndex + 5] . '","' . $row[$studentIndex + 6] . '")');
                    $index = $index + 1;
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

    public function fetchAll(): array
    {
        $result = $this->executeQuery("SELECT * FROM projects; SELECT * FROM students");
        return $result->fetchAll();
    }

    public function executeQuery($query) // todo bind parameters
    {
        $connection = $this->getDbConnection();
        $statement = $connection->prepare($query);

        if (!$statement->execute()) {
            throw new Exception("Request failed, try again later");
        }
        return $statement;
    }

    public function getDbConnection()
    {
        return $this->database->getConnection();
    }
}
