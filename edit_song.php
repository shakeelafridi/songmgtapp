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

// Handle form submission to update the song details
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $mysqli->real_escape_string($_POST["title"]);
    $artist = $mysqli->real_escape_string($_POST["artist"]);
    $genre = $mysqli->real_escape_string($_POST["genre"]);

    // Construct the SQL query to update the song details in the database
    $query = "UPDATE songs SET title='$title', artist='$artist', genre='$genre' WHERE id='$songId'";

    // Execute the query
    if ($mysqli->query($query)) {
        // Redirect to the dashboard after successful song update
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $mysqli->error;
    }
}

// Retrieve the song details from the database
$query = "SELECT * FROM songs WHERE id='$songId'";

// Execute the query
$result = $mysqli->query($query);

// Fetch the song details
if ($result->num_rows === 1) {
    $song = $result->fetch_assoc();
} else {
    echo "Song not found";
    exit();
}

// Free the result set
$result->free();

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Song</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
            border: none;
            background-color: #4caf50;
            color: #ffffff;
            font-weight: bold;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Song</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $song["title"]; ?>" required>
            </div>

            <div class="form-group">
                <label for="artist">Artist:</label>
                <input type="text" id="artist" name="artist" value="<?php echo $song["artist"]; ?>" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" value="<?php echo $song["genre"]; ?>" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Update">
            </div>
        </form>
    </div>
</body>
</html>

