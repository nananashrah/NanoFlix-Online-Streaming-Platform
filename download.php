<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mediaID = intval($_GET['id'] ?? 0);
if (!$mediaID) {
    die("Invalid media ID.");
}

$stmt = $conn->prepare("SELECT Title, Thumbnail FROM MediaLibrary WHERE MediaID = ? LIMIT 1");
$stmt->bind_param("i", $mediaID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Media not found.");
}

$stmt->bind_result($title, $filename);
$stmt->fetch();
$stmt->close();

$filepath = "uploads/$filename";

if (!file_exists($filepath)) {
    die("File does not exist.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

ob_clean();
flush();

readfile($filepath);
exit;
?>
