<?php

class Db
{
    private $connection;

    public function __construct()
    {
        $dbhost = "localhost";
        $dbName = "62113_momchil_ignatov";
        $userName = "root";
        $userPassword = "";

        try {
            $this->connection = new PDO(
                "mysql:host=$dbhost;dbname=$dbName",
                $userName,
                $userPassword,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
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
}
