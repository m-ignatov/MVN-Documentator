<?php

class Database
{
    private $connection;

    public function __construct()
    {
        $dbhost = "localhost";
        $dbName = "maven_generator_db";
        $userName = "root";
        $userPassword = "";

        $this->initDbSchema($dbhost, $userName, $userPassword);

        try {
            $this->connection = new PDO(
                "mysql:host=$dbhost;dbname=$dbName",
                $userName,
                $userPassword,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $err) {
            $errorMessage = '';

            switch ($err->getCode()) {
                case 1045:
                    $errorMessage = "Invalid database credentials";
                    break;
                case 1049:
                    $errorMessage = "Database does not exist";
                    break;
                case 2002:
                    $errorMessage = "Cannot connect to database host";
                    break;
                default:
                    $errorMessage = "Connection to database failed";
            }
            throw new Exception($errorMessage);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function initDbSchema($dbhost, $userName, $userPassword)
    {
        $initalConnection = new PDO(
            "mysql:host=$dbhost",
            $userName,
            $userPassword
        );
        $query = file_get_contents('../db_schema_changelog.sql');
        $initalConnection->prepare($query)->execute();
    }
}
