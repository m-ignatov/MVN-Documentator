-- $dbName = "maven_generator_db"
-- $userName = "root"
-- $userPassword = ""

CREATE DATABASE IF NOT EXISTS maven_generator_db
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE maven_generator_db;

-- EXAMPLE DATA - will change later
CREATE TABLE IF NOT EXISTS projects (
    projectID INT NOT NULL PRIMARY KEY,
    projectName VARCHAR(255) NOT NULL,
    projectDescription TEXT NOT NULL,
    exampleResources TEXT,
    usedResources TEXT,
    githubLink VARCHAR(255),
    presentationDate DATE NOT NULL,
    presentationTime TIME NOT NULL,
    presentationLink VARCHAR(255) NOT NULL
    -- id INT AUTO_INCREMENT PRIMARY KEY,
    -- firstName VARCHAR(255) NOT NULL,
    -- lastName VARCHAR(255) NOT NULL,
    -- courseYear INT NOT NULL,
    -- courseName VARCHAR(255) NOT NULL,
    -- facultyNumber VARCHAR(255) NOT NULL,
    -- groupNumber INT NOT NULL,
    -- birthday DATE NOT NULL,
    -- zodiac VARCHAR(255) NOT NULL,
    -- link VARCHAR(255),
    -- photo VARCHAR(255) NOT NULL,
    -- motivation TEXT,
    -- signature VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS students (
    projectID INT NOT NULL,
    facultyNumber INT NOT NULL PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    courseName VARCHAR(255) NOT NULL,
    courseYear INT NOT NULL,
    projectTasks TEXT NOT NULL,
    manHours INT NOT NULL,
    CONSTRAINT foreign_key_constraint FOREIGN KEY (projectID)
    REFERENCES projects(projectID)
);
