-- $dbName = "maven_generator_db"
-- $userName = "root"
-- $userPassword = ""

CREATE DATABASE IF NOT EXISTS maven_generator_db
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE maven_generator_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    courseYear INT NOT NULL,
    courseName VARCHAR(255) NOT NULL,
    facultyNumber VARCHAR(255) NOT NULL,
    groupNumber INT NOT NULL,
    birthday DATE NOT NULL,
    zodiac VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    photo VARCHAR(255) NOT NULL,
    motivation TEXT,
    signature VARCHAR(255)
);