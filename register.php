<?php 
include 'db.php';
session_start();

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date = date('Y-m-d');

    $sql = "INSERT INTO Subscriber (Name, Password, Email, RegDate)
            VALUES ('$name', '$password', '$email','$date')";
    
    if ($conn->query($sql)) {
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: payment.php");
        exit();
    } else {
        echo "<p style='text-align:center; color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NaNoFlix</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('uploads/registration.png') no-repeat center center fixed;
            background-size: cover; /* fills entire screen with no blank space */
        }

        .card {
            background: #ffffffcc;
            width: 100%;
            max-width: 320px;
            padding: 15px 20px;   /* keep vertical padding smaller */
            border-radius: 12px;
            backdrop-filter: blur(4px);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.15);
            text-align: center;
            transform: translateY(2cm); /* shift 5cm down from center */
        }




        h2 {
            font-size: 14px;
            font-weight: 400;
            color: #444;
            margin-bottom: 20px;
        }

        .input-group { margin-bottom: 12px; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border-radius: 6px;
            background-color: #6366f1;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4f46e5;
        }

        .login-section {
            margin-top: 15px;
            font-size: 12px;
            color: #555;
        }

        .login-link {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Create Your Account</h2>

    <form method="POST">
        <div class="input-group">
            <label>Name</label>
            <input type="text" name="name" required placeholder="Your Name">
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="bob@acme.com">
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <input type="submit" name="register" value="Register">
    </form>

    <div class="login-section">
        <span>Already have an account?</span>
        <a href="login.php" class="login-link">Login</a>
    </div>
</div>

</body>
</html>
