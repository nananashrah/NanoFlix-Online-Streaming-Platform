<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://fonts.cdnfonts.com/css/minecrafter" rel="stylesheet"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --bg-dark: #131722;
        --header-bg: #131722;
        --input-bg: #ffffff;
        --text-main: #ffffff;
        --text-muted: #a0a0a0;
        --accent: #2c7be5;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Netflix Sans', sans-serif;
        background-color: var(--bg-dark);
        color: var(--text-main);
    }

    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 40px;
        background-color: var(--header-bg);
        border-bottom: 1px solid #1f2330;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .brand-logo {
        font-family: 'Minecrafter', sans-serif; 
        font-size: 40px;
        font-weight: 900;
        color: #007BFF; /* Azure Blue */
        letter-spacing: 1px;
        text-decoration: none;
        text-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
    }

    .nav-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-container {
        position: relative;
        width: 100%;
        max-width: 500px;
    }

    .search-container input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border-radius: 4px;
        border: none;
        background-color: var(--input-bg);
        color: #333;
        font-size: 14px;
        font-family: 'Netflix Sans', sans-serif;
        outline: none;
    }

    .search-container .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 16px;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .nav-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
        cursor: pointer;
    }

    .nav-btn:hover {
        color: var(--text-main);
    }

    .profile-icon-circle {
        width: 32px;
        height: 32px;
        background-color: #e50914;
        color: white;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .logout-link {
        font-size: 18px;
        color: #a0a0a0;
        transition: color 0.2s;
    }

    .logout-link:hover {
        color: #e50914;
    }
</style>

<div class="navbar">
    <div class="nav-left">
        <a href="media_library.php" class="brand-logo">NANOFLIX</a>
    </div>

    <div class="nav-center">
        <form action="media_library.php" method="GET" class="search-container">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" name="title" placeholder="Enter keywords…" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">
        </form>
    </div>

    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="nav-btn" title="My Profile">
                <div class="profile-icon-circle">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span>Profile</span>
            </a>
            <a href="logout.php" class="nav-btn logout-link" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        <?php else: ?>
            <a href="login.php" class="nav-btn">
                <div class="profile-icon-circle" style="background: #2c7be5;">
                    <i class="fa-solid fa-user"></i>
                </div>
                Login
            </a>
        <?php endif; ?>
    </div>
</div>
