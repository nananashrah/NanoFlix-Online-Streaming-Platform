<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mediaID = intval($_GET['id'] ?? 0);
if (!$mediaID) die("Invalid Media ID");

$result = $conn->query("SELECT * FROM MediaLibrary WHERE MediaID = $mediaID");
if (!$result || $result->num_rows == 0) die("Media not found");

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST['Title'] ?? '';
    $genre = $_POST['Genre'] ?? '';
    $type = $_POST['MediaType'] ?? '';
    $rating = $_POST['Rating'] ?? '';
    $duration = $_POST['Duration'] ?? '';

    $sql = "UPDATE MediaLibrary SET 
            Title = ?, 
            Genre = ?, 
            MediaType = ?, 
            Rating = ?, 
            Duration = ?";

    $params = [$title, $genre, $type, $rating, $duration];
    $types = "ssssd";

    if (!empty($_FILES['VideoFile']['name'])) {

        $videoName = time() . "_" . basename($_FILES['VideoFile']['name']);
        $videoPath = "uploads/videos/" . $videoName;

        if (move_uploaded_file($_FILES['VideoFile']['tmp_name'], $videoPath)) {

            $sql .= ", VideoFile = ?";
            $params[] = $videoPath;
            $types .= "s";
        }
    }

    if (!empty($_FILES['Thumbnail']['name'])) {

        $thumbName = time() . "_" . basename($_FILES['Thumbnail']['name']);
        $thumbPath = "uploads/thumbnails/" . $thumbName;

        if (move_uploaded_file($_FILES['Thumbnail']['tmp_name'], $thumbPath)) {

            $sql .= ", Thumbnail = ?";
            $params[] = $thumbPath;
            $types .= "s";
        }
    }

    $sql .= " WHERE MediaID = ?";
    $params[] = $mediaID;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?updated=1");
        exit();
    } else {
        echo "Update failed: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Media</title>
</head>
<body>

<h2>Edit Media</h2>

<form method="POST" enctype="multipart/form-data">

    Title: <br>
    <input type="text" name="Title" value="<?= htmlspecialchars($row['Title']) ?>" required><br><br>

    Genre: <br>
    <input type="text" name="Genre" value="<?= htmlspecialchars($row['Genre']) ?>" required><br><br>

    Type: <br>
    <input type="text" name="MediaType" value="<?= htmlspecialchars($row['MediaType']) ?>" required><br><br>

    Rating: <br>
    <input type="number" step="0.1" name="Rating" value="<?= htmlspecialchars($row['Rating']) ?>" required><br><br>

    Duration (mins): <br>
    <input type="number" name="Duration" value="<?= htmlspecialchars($row['Duration']) ?>" required><br><br>

    Thumbnail (upload only if replacing):<br>
    <input type="file" name="Thumbnail"><br><br>

    Video File (upload only if replacing):<br>
    <input type="file" name="VideoFile"><br><br>

    <button type="submit">Update Media</button>
</form>

</body>
</html>
