<?php
session_start();
include 'db.php';

$whereClauses = [];
if (!empty($_GET['title'])) {
    $title = $conn->real_escape_string($_GET['title']);
    $whereClauses[] = "Title LIKE '%$title%'";
}

$sql = "SELECT * FROM MediaLibrary";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}
$sql .= " ORDER BY MediaID DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NANOFLIX Library</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: url('uploads/body.png') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        overflow-y: auto;
        color: #fff;
    }

    .page-container {
        padding: 20px 40px;
        backdrop-filter: blur(2px);
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 400;
        color: #00ffff;
        border-left: 4px solid #00c2ff;
        padding-left: 15px;
    }

    .filter-icon {
        font-size: 16px;
        color: #fff;
        cursor: pointer;
    }

    /* Media Grid */
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 30px;
    }

    .media-card {
        background: rgba(0,0,0,0.5);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
    }

    .media-card:hover {
        transform: translateY(-5px);
    }

    .poster-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 2/3;
        overflow: hidden;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .poster-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #4a90e2;
        margin-bottom: 5px;
        padding: 0 10px;
    }

    .meta-text { color: #ccc; font-weight: 500; }

    .media-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0 10px 12px 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #fff;
    }

    .watch-btn {
        background-color: #6366f1;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        margin: 0 10px 10px 10px;
        text-align: center;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }

    .watch-btn:hover { background-color: #4f46e5; }

    .play-icon { font-size: 10px; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-container">
    <div class="section-header">
        <div class="section-title">Movies and TV Shows</div>
        <i class="fa-solid fa-filter filter-icon"></i>
    </div>

    <div class="media-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['MediaID'];
                $title = $row['Title'];
                $rating = $row['Rating'] ?? '0.0';
                $year = '2023';
                
                $thumbFile = $row['Thumbnail'];
                $thumbPath = "uploads/thumbnails/no_image.jpg";
                if (!empty($thumbFile) && file_exists("uploads/thumbnails/" . $thumbFile)) {
                    $thumbPath = "uploads/thumbnails/" . $thumbFile;
                }
        ?>
            <div class="media-card">
                <div class="poster-wrapper">
                    <img src="<?= htmlspecialchars($thumbPath) ?>" alt="<?= htmlspecialchars($title) ?>" class="poster-img">
                </div>

                <div class="meta-row">
                    <i class="fa-solid fa-star"></i>
                    <span><?= htmlspecialchars($rating) ?></span>
                    <span class="meta-text">HD</span>
                    <span class="meta-text"><?= $year ?></span>
                </div>

                <h3 class="media-title"><?= htmlspecialchars($title) ?></h3>

                <a href="view_media.php?id=<?= $id ?>" class="watch-btn">
                    <i class="fa-solid fa-play play-icon"></i> Watch now
                </a>
            </div>
        <?php
            }
        } else {
            echo "<p style='color:#ccc;'>No media found matching your search.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
