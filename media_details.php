<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$id = intval($_GET['id'] ?? 0);
if (!$id) die("Invalid id");

$res = $conn->query("SELECT * FROM MediaLibrary WHERE mediaID=$id LIMIT 1");
if (!$res || $res->num_rows==0) die("Not found");
$row = $res->fetch_assoc();

$userID = $_SESSION['user_id'];
$fav = $conn->query("SELECT 1 FROM Favorites WHERE UserID=$userID AND MediaID=$id")->num_rows;
$wl = $conn->query("SELECT 1 FROM WatchLater WHERE UserID=$userID AND MediaID=$id")->num_rows;
?>
<!DOCTYPE html><html><head><title>Details</title></head><body>
<h2>Media Details</h2>
<?php if(!empty($row['Thumbnail']) && file_exists('uploads/'.$row['Thumbnail'])): ?>
  <img src="uploads/<?=htmlspecialchars($row['Thumbnail'])?>" width="320"><br>
<?php endif; ?>
<p><strong>Type:</strong> <?=htmlspecialchars($row['mediaType'])?></p>
<p><strong>Genre:</strong> <?=htmlspecialchars($row['Genre'])?></p>
<p><strong>Duration:</strong> <?=htmlspecialchars($row['Duration'])?> mins</p>
<p><strong>Rating:</strong> <?=htmlspecialchars($row['Rating'])?></p>

<?php if($fav): ?>
  <form method="POST" action="favorite_toggle.php"><input type="hidden" name="mediaID" value="<?=$id?>"><input type="hidden" name="action" value="remove"><button>Unfavorite</button></form>
<?php else: ?>
  <form method="POST" action="favorite_toggle.php"><input type="hidden" name="mediaID" value="<?=$id?>"><input type="hidden" name="action" value="add"><button>Favorite</button></form>
<?php endif; ?>

<?php if($wl): ?>
  <form method="POST" action="watchlater_toggle.php"><input type="hidden" name="mediaID" value="<?=$id?>"><input type="hidden" name="action" value="remove"><button>Remove Watch Later</button></form>
<?php else: ?>
  <form method="POST" action="watchlater_toggle.php"><input type="hidden" name="mediaID" value="<?=$id?>"><input type="hidden" name="action" value="add"><button>Add to Watch Later</button></form>
<?php endif; ?>

<p><a href="media_library.php">Back</a></p>
</body></html>