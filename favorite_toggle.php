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

$table = "Favorites"; 
$userCol = "UserID";
$mediaCol = "MediaID";

if ($action === 'add') {
    $sql = "INSERT IGNORE INTO $table ($userCol, $mediaCol) VALUES ($userID, $mediaID)";
} else {
    $sql = "DELETE FROM $table WHERE $userCol=$userID AND $mediaCol=$mediaID";
}

if (!$conn->query($sql)) {
    die("Database error: " . $conn->error);
}

header("Location: view_media.php?id=$mediaID");
exit();