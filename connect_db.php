<?php
// Database connection parameters
$hostname = "localhost";
$username = "root";
$password = "admin";
$database = "song_management";

// Create a new MySQLi object
$mysqli = new mysqli($hostname, $username, $password, $database);

// Check if connection was successful
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Read the SQL file
$sqlFile = "song_management.sql";
$sql = file_get_contents($sqlFile);

// Execute the SQL statements
if ($mysqli->multi_query($sql)) {
    echo "Database and tables created successfully!";
} else {
    echo "Error creating database and tables: " . $mysqli->error;
}

// Close the database connection
$mysqli->close();
?>
