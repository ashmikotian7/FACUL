-- Create the combined database
CREATE DATABASE facultrack;

-- Use the combined database
USE facultrack;

-- Create the 'admin' table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Unique identifier for each admin
    adminID VARCHAR(50) NOT NULL UNIQUE,     -- Unique admin ID
    name VARCHAR(100) NOT NULL,              -- Admin's full name
    email VARCHAR(100) NOT NULL UNIQUE,      -- Admin's email address
    password VARCHAR(255) NOT NULL,          -- Hashed password
    birthdate DATE NOT NULL                  -- Admin's birthdate (used for verification)
);

-- Create the 'faculty' table
CREATE TABLE faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facultyID VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL,     -- Faculty birthdate (used for verification)
    department VARCHAR(100) NOT NULL,
    grade VARCHAR(50) NOT NULL,
    allowance INT NOT NULL,      -- Whole numbers (e.g., 2000 or 5000)
    total_score INT NOT NULL,    -- Total score field
    drive_link VARCHAR(255) NOT NULL,
    year INT NOT NULL            -- Year field added to store the year
);

CREATE TABLE faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facultyID VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL,
    department VARCHAR(100) NOT NULL,
    grade VARCHAR(50) NOT NULL,
    allowance INT NOT NULL,
    total_score INT NOT NULL,
    drive_link VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    UNIQUE (facultyID, year)
);
