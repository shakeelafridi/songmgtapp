<?php
// Check if user is authenticated, redirect to login page if not
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

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

// Check if song ID is provided in the URL
if (!isset($_GET["id"])) {
    echo "Invalid song ID";
    exit();
}

$songId = $_GET["id"];
$songId = $mysqli->real_escape_string($songId);

// Construct the SQL query to delete the song from the database
$query = "DELETE FROM songs WHERE id='$songId'";

// Execute the query
if ($mysqli->query($query)) {
    // Redirect to the dashboard after successful song deletion
    header("Location: index.php");
    exit();
} else {
    echo "Error: " . $mysqli->error;
}

// Close the database connection
$mysqli->close();
?>
