<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if (!$id) die("Invalid media ID");

$res = $conn->query("SELECT * FROM MediaLibrary WHERE MediaID=$id LIMIT 1");
if (!$res || $res->num_rows == 0) die("Media not found");

$row = $res->fetch_assoc();

$videoFile = $row['VideoFile'] ?? '';
$thumb = $row['Thumbnail'] ?? 'no_image.jpg';

$thumbPath = !empty($thumb) && file_exists("uploads/thumbnails/" . $thumb) ? "uploads/thumbnails/" . $thumb : "uploads/thumbnails/no_image.jpg";
$videoPath = "uploads/videos/" . $videoFile;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($row['Title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/netflix-sans" rel="stylesheet">
    <style>
        body {
            font-family: 'Netflix Sans', sans-serif;
            background: url('uploads/body.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            gap: 20px;
            padding: 40px;
            align-items: flex-start;
        }

        .left {
            width: 350px;
            background: rgba(0,0,0,0.6);
            padding: 20px;
            border-radius: 10px;
        }

        .right {
            flex: 1;
            max-width: 100%;
        }

        img {
            width: 100%;
            border-radius: 5px;
        }

        video {
            width: 100%;
            max-height: 500px;
            border-radius: 5px;
            object-fit: contain;
            display: block;
        }

        .media-info p { margin: 5px 0; }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .buttons form, .buttons a {
            width: 100%;
        }

        .buttons button, .buttons a.download-btn {
            width: 100%;
            padding: 10px;
            background-color: #6366f1;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            transition: background 0.2s;
        }

        .buttons button:hover, .buttons a.download-btn:hover {
            background-color: #4f46e5;
        }

        h2 {
            margin-top: 15px;
            font-size: 24px;
        }

        a.back-link {
            display: inline-block;
            margin: 20px 40px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <div class="left">
        <img src="<?= htmlspecialchars($thumbPath) ?>" alt="Thumbnail">

        <div class="media-info">
            <h2><?= htmlspecialchars($row['Title']) ?></h2>
            <p><b>Genre:</b> <?= htmlspecialchars($row['Genre']) ?></p>
            <p><b>Type:</b> <?= htmlspecialchars($row['MediaType']) ?></p>
            <p><b>Duration:</b> <?= htmlspecialchars($row['Duration']) ?> mins</p>
            <p><b>Rating:</b> <?= htmlspecialchars($row['Rating']) ?></p>
        </div>

        <div class="buttons">
            <!-- Favorites -->
            <form method="POST" action="favorite_toggle.php">
                <input type="hidden" name="MediaID" value="<?= $id ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit">☆ Add to Favorites</button>
            </form>

            <!-- Watch Later -->
            <form method="POST" action="watchlater_toggle.php">
                <input type="hidden" name="MediaID" value="<?= $id ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit">+ Watch Later</button>
            </form>

            <!-- Download -->
            <a href="download.php?id=<?= $id ?>" class="download-btn">⬇ Download</a>
        </div>
    </div>

    <div class="right">
        <?php if(!empty($videoFile) && file_exists($videoPath)): ?>
            <video controls poster="<?= htmlspecialchars($thumbPath) ?>">
                <source src="<?= htmlspecialchars($videoPath) ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <p>Video not available.</p>
        <?php endif; ?>
    </div>
</div>

<a href="media_library.php" class="back-link">← Back to Media Library</a>

</body>
</html>
