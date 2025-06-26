<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

$error = '';  // nog niks te melden


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES);
    $lastname = htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES);
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES);
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES);
    $password = htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES);
    $role = 'Client'; // standaard rol omdat alleen klanten zich kunnen registreren

    if (!empty($firstname) && !empty($lastname) && !empty($username) && !empty($password)) {
        //wachtwoord checken
        $passwordValid = preg_match('/[a-z]/', $password) &&    // ten minste één kleine letter
            preg_match('/[A-Z]/', $password) &&    // ten minste één hoofdletter
            preg_match('/[0-9]/', $password) &&    // ten minste één cijfer
            preg_match('/[\W_]/', $password) &&    // ten minste één speciaal teken
            strlen($password) >= 8;               // minimale lengte van 8 tekens

        if (!$passwordValid) {
            $error = "Wachtwoord moet ten minste 8 tekens lang zijn, met ten minste één kleine letter, één hoofdletter, één cijfer en één speciaal teken.";
        } elseif (usernameExists($conn, $username)) {
            $error = "Gebruikersnaam bestaat al. Kies een andere gebruikersnaam.";
        } else {
            if (registerUser($conn, $username, $password, $firstname, $lastname, $address, $role)) {
                $_SESSION['username'] = $username;
                header("Location: login.php");
            } else {
                $error = "Registratie mislukt. Probeer het opnieuw.";
            }
        }
    } else {
        $error = "Vul alle verplichte velden in.";
    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreer pagina</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Registreren</h1>
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
        <h2>Maak een account aan</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
        <?php endif; ?>
        <div class="login-container">
            <div class="login-form">
                <h3>Registreren als klant</h3>
                <form action="" method="POST">
                    <label for="first_name">Voornaam:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Achternaam:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="username">Gebruikersnaam:</label>
                    <input type="text" id="username" name="username" required>
                    <label for="address">Adres (optioneel):</label>
                    <input type="text" id="address" name="address">
                    <label for="password">Wachtwoord:</label>
                    <input type="password" id="password" name="password"
                        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}"
                        title="Minstens 12 tekens, met minstens 1 hoofdletter, 1 kleine letter, 1 cijfer en 1 speciaal teken"
                        required>
                    <button type="submit">Register</button>
                </form>
                <p>Al een account? <a href="login.php">Log hier in.</a></p>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>

</html>