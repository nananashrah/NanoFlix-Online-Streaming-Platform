<?php
session_start();
include 'admin_config.php';
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else $err = "Wrong password";
}
?>
<!DOCTYPE html><html><head><title>Admin Login</title></head><body>
<h2>Admin Login</h2>
<?php if(!empty($err)) echo "<p style='color:red;'>$err</p>"; ?>
<form method="POST"><input type="password" name="pass" required><button type="submit">Login</button></form>
</body></html>
