<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = intval($_SESSION['user_id']);
$mediaID = intval($_POST['MediaID'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$mediaID || !in_array($action, ['add', 'remove'])) {
    die("Invalid request");
}

if ($action === 'add') {
    $conn->query("INSERT IGNORE INTO WatchLater (UserID, MediaID) VALUES ($userID, $mediaID)");
} else {
    $conn->query("DELETE FROM WatchLater WHERE UserID=$userID AND MediaID=$mediaID");
}

header("Location: view_media.php?id=$mediaID");
exit();