<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Toegang geweigerd</title>
    <link rel="stylesheet" href="styleAuthorization.css">
</head>

<body>
    <div class="container">
        <h1>403 - Toegang geweigerd</h1>
        <p>Je hebt geen toestemming om deze pagina te bekijken.</p>
        <?php if (isset($_SESSION['username'])): ?>
            <p><a href="logout.php">Uitloggen</a> of <a href="index.php">Ga terug naar de startpagina</a>.</p>
        <?php else: ?>
            <p><a href="login.php">Log in</a> met een account dat toegang heeft.</p>
        <?php endif; ?>
    </div>
</body>

</html>