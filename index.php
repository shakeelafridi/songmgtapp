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

// Handle form submission to create a new song
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $mysqli->real_escape_string($_POST["title"]);
    $artist = $mysqli->real_escape_string($_POST["artist"]);
    $genre = $mysqli->real_escape_string($_POST["genre"]);

    // Handle file upload
    $songFile = $_FILES["song_file"]["name"];
    $songFileTmp = $_FILES["song_file"]["tmp_name"];
    $songFileExt = strtolower(pathinfo($songFile, PATHINFO_EXTENSION));
    $songFileName = uniqid() . "." . $songFileExt;
    $songFileDest = "uploads/" . $songFileName;

    // Valid audio file extensions
    $allowedExtensions = array("mp3", "wav", "ogg"); // Add more extensions if needed

    // Validate the file extension
    if (!in_array($songFileExt, $allowedExtensions)) {
        echo "Invalid file format. Only MP3, WAV, and OGG files are allowed.";
        exit();
    }

    if (move_uploaded_file($songFileTmp, $songFileDest)) {
        // File uploaded successfully, construct the SQL query to insert the new song into the database
        $query = "INSERT INTO songs (title, artist, genre, file_path) VALUES ('$title', '$artist', '$genre', '$songFileDest')";

        // Execute the query
        if ($mysqli->query($query)) {
            // Redirect to the dashboard after successful song creation
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $mysqli->error;
        }
    } else {
        echo "Error uploading the file.";
    }
}

// Retrieve the search query from the URL parameters
$searchQuery = $_GET["search"] ?? "";

if (!empty($searchQuery)) {
    // Construct the SQL query to search for songs matching the query
    $query = "SELECT * FROM songs WHERE title LIKE '%$searchQuery%' OR artist LIKE '%$searchQuery%' OR genre LIKE '%$searchQuery%'";
} else {
    // Construct the SQL query to retrieve all songs
    $query = "SELECT * FROM songs";
}

// Execute the query
$result = $mysqli->query($query);

// Fetch all songs from the result
$matchingSongs = array();
while ($row = $result->fetch_assoc()) {
    $matchingSongs[] = $row;
}

// Free the result set
$result->free();

// Close the database connection
$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Song Management Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f7f7f7;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            z-index: 9999;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        .popup h3 {
            margin-top: 0;
        }

        .popup input[type="text"],
        .popup input[type="file"],
        .popup input[type="submit"] {
            margin-bottom: 10px;
        }

        .add-song-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .add-song-button:hover {
            background-color: #45a049;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show the "Add Song" form on button click
            $("#addSongBtn").click(function() {
                $(".popup, .overlay").fadeIn();
            });

            // Hide the "Add Song" form and overlay on form submission
            $("#addSongForm").submit(function() {
                $(".popup, .overlay").fadeOut();
            });
        });
    </script>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search songs">
        <input type="submit" value="Search">
    </form>
        <!-- Add Song Button -->
    <button class="add-song-button" id="addSongBtn">Add Song</button>

    <h3>List of Songs</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Artist</th>
            <th>Genre</th>
            <th>File</th>
            <th>Action</th>
        </tr>
        <?php foreach ($matchingSongs as $song): ?>
            <tr>
                <td><?php echo $song["id"]; ?></td>
                <td><?php echo $song["title"]; ?></td>
                <td><?php echo $song["artist"]; ?></td>
                <td><?php echo $song["genre"]; ?></td>
                <td><?php echo $song["file_path"]; ?></td>
                <td>
                    <a href="edit_song.php?id=<?php echo $song["id"]; ?>">Edit</a>
                    <a href="delete_song.php?id=<?php echo $song["id"]; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Add Song Popup -->
    <div class="popup" id="addSongPopup">
        <h3>Create a New Song</h3>
        <form action="" method="POST" enctype="multipart/form-data" id="addSongForm">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="artist">Artist:</label>
            <input type="text" id="artist" name="artist" required><br>

            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" required><br>

            <label for="song_file">Song File:</label>
            <input type="file" id="song_file" name="song_file" required><br>

            <input type="submit" value="Add Song">
        </form>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
</body>
</html>
