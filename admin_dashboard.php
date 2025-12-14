<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "OnlineSubscriptionManagement";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM MediaLibrary WHERE MediaID=$del_id");
    header("Location: admin_dashboard.php");
    exit;
}

if (isset($_POST['add_media'])) {
    $Title     = $conn->real_escape_string($_POST['Title']);
    $Genre     = $conn->real_escape_string($_POST['Genre']);
    $MediaType = $conn->real_escape_string($_POST['MediaType']); 
    $Rating    = (float)$_POST['Rating'];
    $Duration  = $conn->real_escape_string($_POST['Duration']);

    $VideoFile = '';
    if (!empty($_FILES['VideoFile']['name'])) {
        $targetDir = "uploads/videos/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['VideoFile']['name']);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES['VideoFile']['tmp_name'], $targetFile)) {
            $VideoFile = $filename;
        }
    }

    $Thumbnail = "no_image.jpg";
    if (!empty($_FILES['Thumbnail']['name'])) {
        $targetDir = "uploads/thumbnails/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['Thumbnail']['name']);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES['Thumbnail']['tmp_name'], $targetFile)) {
            $Thumbnail = $filename;
        }
    }

    $sql = "INSERT INTO MediaLibrary (Title, MediaType, Genre, Duration, Rating, VideoFile, Thumbnail) 
            VALUES ('$Title', '$MediaType', '$Genre', '$Duration', '$Rating', '$VideoFile', '$Thumbnail')";

    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error adding media: ".$conn->error."</p>";
    }
}

$result = $conn->query("SELECT * FROM MediaLibrary ORDER BY MediaID DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Media Library</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #2c7be5; color: white; }
        img { width: 80px; height: 60px; object-fit: cover; }
        form { margin-bottom: 30px; background: #eee; padding: 15px; border-radius: 5px; }
        input, select { padding: 5px; margin: 5px; width: 100%; }
        button { padding: 6px 12px; background: #2c7be5; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1a5bb8; }
        a { text-decoration: none; color: #2c7be5; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Admin Dashboard</h2>

<h3>Add New Media</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="Title" placeholder="Title" required>
    
    <select name="Genre" required>
        <option value="">Genre</option>
        <option value="Action">Action</option>
        <option value="Drama">Drama</option>
        <option value="Comedy">Comedy</option>
        <option value="Horror">Horror</option>
        <option value="Sci-Fi">Sci-Fi</option>
    </select>

    <select name="MediaType" required>
        <option value="">Type</option>
        <option value="Movie">Movie</option>
        <option value="Series">Series</option>
        <option value="Documentary">Documentary</option>
    </select>

    <input type="number" name="Rating" step="0.1" min="0" max="5" placeholder="Rating" required>
    <input type="text" name="Duration" placeholder="Duration in minutes" required>
    
    <input type="file" name="Thumbnail">
    <input type="file" name="VideoFile">
    
    <button type="submit" name="add_media">Add Media</button>
</form>

<h3>All Media</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Thumbnail</th>
        <th>Title</th>
        <th>Genre</th>
        <th>Type</th>
        <th>Rating</th>
        <th>Duration</th>
        <th>Video</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $thumb = file_exists("uploads/thumbnails/".$row['Thumbnail']) ? "uploads/thumbnails/".$row['Thumbnail'] : "uploads/no_image.jpg";
            $video = !empty($row['VideoFile']) && file_exists("uploads/videos/".$row['VideoFile']) ? "uploads/videos/".$row['VideoFile'] : '';
            echo "<tr>
                <td>{$row['MediaID']}</td>
                <td><img src='$thumb'></td>
                <td>{$row['Title']}</td>
                <td>{$row['Genre']}</td>
                <td>{$row['MediaType']}</td>
                <td>{$row['Rating']}</td>
                <td>{$row['Duration']}</td>
                <td>";
            if ($video) echo "<a href='$video' target='_blank'>Watch</a>";
            else echo "N/A";
            echo "</td>
                <td>
                    <a href='admin_edit_media.php?id={$row['MediaID']}'>Edit</a> | 
                    <a href='admin_dashboard.php?delete={$row['MediaID']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No media found.</td></tr>";
    }
    ?>
</table>

<p><a href="media_library.php">← View Media Library</a></p>

</body>
</html>
