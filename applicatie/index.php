<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Pizzeria Sole Machina 🍕</h1>
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
        <h2>Over ons</h2>
        <p>Bekijk onze nieuwste producten.</p>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>
</html>
