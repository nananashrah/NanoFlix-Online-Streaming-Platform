<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: media_library.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT UserID, Password FROM Subscriber WHERE Email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userID, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userID;
            $_SESSION['email'] = $email;

            header("Location: media_library.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No account found with this email!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - NaNoFlix</title>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: url('uploads/registration.png') no-repeat center center fixed;
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
        max-width: 320px;
        padding: 20px 20px;
        border-radius: 12px;
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.15);
        text-align: center;
        transform: translateY(2cm); 

    }

    h1 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    h2 {
        font-size: 14px;
        font-weight: 400;
        color: #444;
        margin-bottom: 20px;
    }

    .input-group {
        margin-bottom: 12px;
        text-align: left;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 5px;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: #6366f1;
    }

    input[type="submit"] {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        cursor: pointer;
        border-radius: 6px;
        background-color: #6366f1;
        border: none;
        color: white;
        font-size: 15px;
        font-weight: 600;
        transition: background-color 0.2s;
    }

    input[type="submit"]:hover {
        background-color: #4f46e5;
    }

    .error {
        color: red;
        margin-bottom: 10px;
        text-align: center;
    }

    .register-section {
        margin-top: 15px;
        font-size: 13px;
        color: #666;
    }

    .register-link {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>
<div class="card">
    <h1>Welcome Back!</h1>
    <h2>Login to NaNoFlix</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <input type="submit" value="Login">
    </form>

    <div class="register-section">
        <span>Don't have an account? </span>
        <a href="register.php" class="register-link">Register</a>
    </div>
</div>
</body>
</html>
