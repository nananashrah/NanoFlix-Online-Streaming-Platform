<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = intval($_SESSION['user_id']);

$sql = "SELECT m.* FROM MediaLibrary m
        INNER JOIN WatchLater w ON m.MediaID = w.MediaID
        WHERE w.UserID = ?"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Watch Later</title>
    <link href="https://fonts.cdnfonts.com/css/netflix-sans" rel="stylesheet">
    <style>
        body {
            font-family: 'Netflix Sans', sans-serif;
            background: #000000;
            color: #ffffff;
            margin: 0; padding: 0;
        }
        h2 {
            font-size: 36px;
            font-weight: 700;
            padding: 0 40px;
            margin: 30px 0 20px 0;
            color: white;
        }
        .media-grid { display: flex; flex-wrap: wrap; gap: 20px; padding: 10px 40px 40px 40px; }
        
        .card {
            width: 260px;
            background: #141414;
            border: 1px solid #333;
            border-radius: 4px;
            padding: 10px;
            transition: 0.2s;
        }
        .card:hover { transform: translateY(-5px); border-color: #007bff; }
        
        .card img { width: 100%; height: 180px; object-fit: cover; border-radius: 4px; }
        .card h3 { font-size: 18px; margin: 10px 0 5px 0; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        .card p { margin: 2px 0; font-size: 13px; color: #ccc;}
        .card p b { color: #fff; }
        
        .buttons a, .buttons button {
            display: block; width: 100%; padding: 8px; margin-top: 5px;
            text-align: center; border-radius: 4px; border: none;
            cursor: pointer; transition: 0.2s; font-weight: 600;
            text-decoration: none; box-sizing: border-box; font-family: inherit; font-size: 13px;
        }
        
        .btn-view { background: #007bff; color: white; }
        .btn-view:hover { background: #0056b3; }
        
        .btn-remove { background: #333; color: white; border: 1px solid #555; }
        .btn-remove:hover { background: #cc0000; border-color: #cc0000; }

        .back-link { padding: 0 40px 20px; display: block; color: #007bff; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<h2>Watch Later</h2>

<div class="media-grid">
<?php 
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $media_id = $row['MediaID'];
        $media_title = $row['Title'];
        
        $thumbName = $row['Thumbnail'];
        $media_thumbnail = !empty($thumbName) && file_exists("uploads/thumbnails/$thumbName") ? "uploads/thumbnails/$thumbName" : "uploads/thumbnails/no_image.jpg";
        
        $media_type = isset($row['MediaType']) ? $row['MediaType'] : (isset($row['Type']) ? $row['Type'] : 'Unknown');
        $media_genre = $row['Genre'];
        $media_rating = $row['Rating'];

        echo "
        <div class='card'>
            <img src='$media_thumbnail' alt='thumbnail'>
            <h3>" . htmlspecialchars($media_title) . "</h3>
            <p><b>Type:</b> $media_type</p>
            <p><b>Genre:</b> $media_genre</p>
            <div class='buttons'>
                <a href='view_media.php?id=$media_id' class='btn-view'>Play</a>
                
                <form method='POST' action='watchlater_toggle.php'>
                    <input type='hidden' name='MediaID' value='$media_id'>
                    <input type='hidden' name='action' value='remove'>
                    <button type='submit' class='btn-remove'>Remove</button>
                </form>
            </div>
        </div>";
    }
} else {
    echo "<p style='padding: 20px 40px;'>You have no media in Watch Later.</p>";
}
?>
</div>

<a href="media_library.php" class="back-link">← Back to Media Library</a>

</body>
</html>
