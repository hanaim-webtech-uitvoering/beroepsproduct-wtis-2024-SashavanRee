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
        <h1>Welcome to My Homepage</h1>
    </header>
   <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="privacy.php">Privacy</a></li>

        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="profile.php">Profiel</a></li>
            <li><a href="logout.php">Uitloggen</a></li>
        <?php else: ?>
            <li><a href="login.php">Inloggen</a></li>
        <?php endif; ?>
    </ul>
</nav>
     <main>
        <div class="privacy-policy">
            <h2>Privacyverklaring</h2>
            <p>Wij hechten veel waarde aan uw privacy en willen uw persoonlijke gegevens met respect behandelen.<br>
               Hieronder informeren wij u over ons privacybeleid:</p>
            <h3>1. Verzameling van gegevens</h3>
            <p>We verzamelen alleen de noodzakelijke persoonlijke gegevens die nodig zijn voor het leveren van onze diensten.</p>
            <h3>2. Gebruik van gegevens</h3>
            <p>Uw gegevens worden uitsluitend gebruikt voor het uitvoeren van de overeengekomen diensten<br>
               en worden niet gedeeld met derden zonder uw toestemming.</p>
            <h3>3. Beveiliging</h3>
            <p>Wij nemen alle mogelijke maatregelen om uw gegevens te beschermen tegen<br>
               ongeautoriseerde toegang, verlies, misbruik of openbaarmaking.</p>
            <h3>4. Bewaartermijn</h3>
            <p>Uw persoonlijke gegevens worden niet langer bewaard dan nodig is voor het doel waarvoor ze zijn verzameld,<br>
               tenzij anders is overeengekomen of vereist wegens wetgeving.</p>
            <h3>5. Rechten van betrokkenen</h3>
            <p>U heeft het recht om uw persoonlijke gegevens in te zien, te corrigeren of te verwijderen.<br>
               Neem contact met ons op voor vragen over uw gegevens.</p>
            <h3>6. Cookies</h3>
            <p>Onze website maakt gebruik van cookies om de gebruikerservaring te verbeteren.<br>
               U kunt uw browserinstellingen aanpassen om het gebruik van cookies te beheren.</p>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>
</html>
