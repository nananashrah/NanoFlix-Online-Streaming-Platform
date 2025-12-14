<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

if (isset($_POST['toggle_renewal'])) {
    $newStatus = ($_POST['current_status'] == 1) ? 0 : 1;
    $stmt = $conn->prepare("UPDATE Subscriber SET RenewalStatus=? WHERE UserID=?");
    $stmt->bind_param("ii", $newStatus, $userID);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM Subscriber WHERE UserID=? LIMIT 1");
$stmt->bind_param("i", $userID);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows == 0) die("User not found");
$user = $res->fetch_assoc();
$renewalStatus = $user['RenewalStatus'] ?? 1;

$favCount = $conn->query("SELECT COUNT(*) AS cnt FROM Favorites WHERE UserID=$userID")->fetch_assoc()['cnt'] ?? 0;
$wlCount = $conn->query("SELECT COUNT(*) AS cnt FROM WatchLater WHERE UserID=$userID")->fetch_assoc()['cnt'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($user['Name']) ?>'s Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: url('uploads/body.png') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        overflow-y: auto;
        color: #333;
    }

    .card {
        background: #ffffffcc;
        width: 100%;
        max-width: 360px;
        padding: 30px 25px;
        border-radius: 12px;
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.15);
        text-align: center;
    }

    h2 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }

    .info {
        font-size: 14px;
        color: #444;
        margin-bottom: 15px;
        text-align: left;
    }
    .info strong { font-weight: 600; }

    .renewal-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .renewal-section span {
        font-weight: bold;
        color: <?= $renewalStatus ? '#28a745' : '#dc3545' ?>;
    }

    .renewal-section button {
        background-color: #6366f1;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }
    .renewal-section button:hover { background-color: #4f46e5; }

    .stats {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
    }

    .stats a {
        flex: 1;
        text-decoration: none;
        background: #6366f1;
        color: white;
        padding: 15px 0;
        margin: 0 5px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.2s;
        display: inline-block;
    }
    .stats a:hover { background: #4f46e5; }

    .logout-btn {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border-radius: 6px;
        background-color: #e50914;
        border: none;
        color: white;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .logout-btn:hover { background-color: #cc0000; }
</style>
</head>
<body>

<div class="card">
    <h2><?= htmlspecialchars($user['Name']) ?>'s Profile</h2>

    <div class="info"><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></div>
    <div class="info"><strong>Registered:</strong> <?= htmlspecialchars($user['RegDate']) ?></div>

    <div class="info renewal-section">
        <div>
            <strong>Renewal Status:</strong> 
            <span><?= $renewalStatus ? "ON" : "OFF" ?></span>
        </div>
        <form method="POST">
            <input type="hidden" name="current_status" value="<?= $renewalStatus ?>">
            <button type="submit" name="toggle_renewal"><?= $renewalStatus ? "Turn OFF" : "Turn ON" ?></button>
        </form>
    </div>

    <div class="stats">
        <a href="favorites.php">Favorites: <?= $favCount ?></a>
        <a href="watch_later.php">Watch Later: <?= $wlCount ?></a>
    </div>

    <form action="logout.php" method="POST">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

</body>
</html>
