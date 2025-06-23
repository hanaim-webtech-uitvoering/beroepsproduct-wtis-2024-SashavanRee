<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

if (isset($_GET['logout'])) {
    logoutUser();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profielpagina</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a href="images/profile.php" class="profile-icon">
            <img src="profile-icon.png" alt="Profielicoon">
            <span>Mijn Profiel</span>
        </a>
    </header>
    <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="privacy.php">Privacy</a></li>

        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="profile.php">Profiel</a></li>
            <li><a href="login.php">Uitloggen</a></li>
        <?php else: ?>
            <li><a href="login.php">Inloggen</a></li>
        <?php endif; ?>
    </ul>
</nav>
    <main>
        <h2>Mijn profielgegevens</h2>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>

</html>