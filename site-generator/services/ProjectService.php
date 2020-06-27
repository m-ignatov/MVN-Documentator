<?php
require_once "../models/Database.php";

class ProjectService
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function persist($file): void
    {
        $this->executeQuery("LOAD DATA LOCAL INFILE '" . $file .
            "' INTO TABLE projects FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    }

    public function fetchAll(): array
    {
        $result = $this->executeQuery("SELECT * FROM projects");
        return $result->fetchAll();
    }

    private function executeQuery($query)
    {
        $connection = $this->getDbConnection();
        $statement = $connection->prepare($query);

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
