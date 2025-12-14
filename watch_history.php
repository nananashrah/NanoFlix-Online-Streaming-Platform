<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$UserID = $_SESSION['user_id'];

$sql = "
    SELECT 
        wl.MediaID,
        ml.Title,
        ml.MediaType,
        ml.Genre,
        ml.Thumbnail,
        wl.MinutesWatched,
        wl.LastWatched
    FROM WatchHistory wl
    INNER JOIN MediaLibrary ml ON wl.MediaID = ml.MediaID
    WHERE wl.UserID = ?
    ORDER BY wl.LastWatched DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

$filter_title = ''; $filter_genre = ''; $filter_type = ''; $filter_rating = '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Watch History</title>
    <link href="https://fonts.cdnfonts.com/css/netflix-sans" rel="stylesheet">
    <style>
        body {
            background: #000000;
            color: white;
            font-family: 'Netflix Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            padding-top: 30px;
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #fff;
        }

        .history-item {
            display: flex;
            background: #141414; 
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #333;
            transition: 0.2s;
        }

        .history-item:hover {
            border-color: #007bff;
        }

        .thumb img {
            width: 180px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .info {
            padding-left: 20px;
            flex-grow: 1;
        }

        .info h2 {
            margin: 0 0 8px 0;
            color: #fff;
            font-size: 22px;
        }

        .meta {
            color: #aaa;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .meta strong {
            color: #fff;
        }

        .watch-btn {
            margin-top: 15px;
            display: inline-block;
            color: #fff;
            background: #007bff; 
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .watch-btn:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Your Watch History</h1>

    <?php if ($result->num_rows === 0): ?>
        <p>You have not watched anything yet.</p>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="history-item">

                <div class="thumb">
                    <?php 
                        $tPath = "uploads/thumbnails/" . $row['Thumbnail'];
                        if(!file_exists($tPath) || empty($row['Thumbnail'])) $tPath = "uploads/thumbnails/no_image.jpg";
                    ?>
                    <img src="<?php echo htmlspecialchars($tPath); ?>" alt="thumbnail">
                </div>

                <div class="info">
                    <h2><?php echo htmlspecialchars($row['Title']); ?></h2>

                    <p class="meta">
                        Type: <strong><?php echo htmlspecialchars($row['MediaType']); ?></strong> |
                        Genre: <strong><?php echo htmlspecialchars($row['Genre']); ?></strong> <br>
                        Minutes Watched: <strong><?php echo $row['MinutesWatched']; ?> min</strong> <br>
                        Last Watched: <strong><?php echo date("M j, Y - g:i a", strtotime($row['LastWatched'])); ?></strong>
                    </p>

                    <a class="watch-btn" href="view_media.php?id=<?php echo $row['MediaID']; ?>">
                        Continue Watching
                    </a>
                </div>

            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>