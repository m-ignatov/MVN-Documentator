-- $dbName = "62113_momchil_ignatov"
-- $userName = "root"
-- $userPassword = ""

CREATE DATABASE IF NOT EXISTS 62113_momchil_ignatov
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE 62113_momchil_ignatov;

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