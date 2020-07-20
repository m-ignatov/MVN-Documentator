CREATE DATABASE IF NOT EXISTS maven_generator_db
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE maven_generator_db;

CREATE TABLE IF NOT EXISTS projects (
    folderName VARCHAR(255) NOT NULL,
    projectID INT NOT NULL,
    projectName VARCHAR(255) NOT NULL,
    projectDescription TEXT NOT NULL,
    exampleResources TEXT,
    usedResources TEXT,
    githubLink VARCHAR(255),
    presentationDate DATE NOT NULL,
    presentationTime TIME NOT NULL,
    presentationLink VARCHAR(255) NOT NULL,
    PRIMARY KEY (folderName, projectID)
);

CREATE TABLE IF NOT EXISTS students (
    folderName VARCHAR(255) NOT NULL,
    projectID INT NOT NULL,
    facultyNumber INT NOT NULL,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    courseName VARCHAR(255) NOT NULL,
    courseYear INT NOT NULL,
    projectTasks TEXT NOT NULL,
    manHours INT NOT NULL,
    CONSTRAINT foreign_key_constraint FOREIGN KEY (folderName, projectID)
    REFERENCES projects(folderName, projectID)
);
