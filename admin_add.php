<?php
session_start();
include 'db.php';
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin']!==true) header("Location: admin_login.php");

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $mediaType = $conn->real_escape_string($_POST['mediaType']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $duration = intval($_POST['duration']);
    $rating = $conn->real_escape_string($_POST['rating']);

    $thumbName = '';
    if (!empty($_FILES['thumbnail']['name'])) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext),$allowed)) { $err = "Invalid image type."; }
        else {
            $thumbName = uniqid('t_').'.'.$ext;
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], __DIR__.'/uploads/'.$thumbName)) $err="Upload failed";
        }
    }

    if (empty($err)) {
        $stmt = $conn->prepare("INSERT INTO MediaLibrary (mediaType, Genre, Duration, Rating, Thumbnail) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $mediaType, $genre, $duration, $rating, $thumbName);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html><html><head><title>Add Media</title></head><body>
<h2>Add Media</h2>
<?php if(!empty($err)) echo "<p style='color:red;'>$err</p>"; ?>
<form method="POST" enctype="multipart/form-data">
  Type: <input name="mediaType" required><br>
  Genre: <input name="genre" required><br>
  Duration (mins): <input name="duration" type="number" required><br>
  Rating: <input name="rating" required><br>
  Thumbnail: <input type="file" name="thumbnail" accept="image/*"><br><br>
  <button type="submit">Add</button>
</form>
<a href="admin_dashboard.php">Back</a>
</body></html>
