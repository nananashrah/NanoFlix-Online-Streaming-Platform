<?php
session_start();
include 'db.php';
if (!isset($_SESSION['is_admin'])||$_SESSION['is_admin']!==true) header("Location: admin_login.php");

$id = intval($_GET['id'] ?? 0);
if (!$id) header("Location: admin_dashboard.php");

$res = $conn->query("SELECT Thumbnail FROM MediaLibrary WHERE mediaID=$id");
if ($res && $res->num_rows) {
    $thumb = $res->fetch_assoc()['Thumbnail'];
    if (!empty($thumb) && file_exists(__DIR__.'/uploads/'.$thumb)) unlink(__DIR__.'/uploads/'.$thumb);
}
$conn->query("DELETE FROM MediaLibrary WHERE mediaID=$id");
header("Location: admin_dashboard.php");
exit();
