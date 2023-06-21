-- Create the database
CREATE DATABASE song_management;

-- Use the created database
USE song_management;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert a sample user
INSERT INTO users (username, password) VALUES ('admin', 'admin123');

-- Create the songs table
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL
);
