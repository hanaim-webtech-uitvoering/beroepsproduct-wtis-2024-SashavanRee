<?php
require_once 'db_connectie.php';

$notification = '';  // nog niks te melden

// check voor de knop
if(isset($_POST['registeren'])) {
    $errors = [];
    // 1. inlezen gegevens uit form
    $username      = $_POST['naam'];
    $password = $_POST['wachtwoord'];

    // 2. controleren van de gegevens
    if(strlen($username) < 4) {
        $errors[] = 'Gebruikersnaam minstens 4 karakters.';
    }

    if(strlen($password) < 8) {
        $errors[] = 'Wachtwoord minstens 8 karakters.';
    }

    // 3. opslaan van de gegevens
    if(count($errors) > 0) {
        $notification = "Er waren fouten in de invoer.<ul>";
        foreach($errors as $error) {
            $notification .= "<li>$error</li>";
        }
        $notification .= "</ul>";

    } else {
        // Hash the password
        $passwordhash = password_hash($password, PASSWORD_DEFAULT);
        
        // database
        $db = maakVerbinding();
        // Insert query (prepared statement)
        $sql = 'INSERT INTO Gebruikers(naam, passwordhash)
                values (:naam, :passwordhash)';
        $query = $db->prepare($sql);

        // Send data to database
        $data_array = [
            'naam' => $username,
            'passwordhash' => $passwordhash
        ];
        $succes = $query->execute($data_array);

        // Check results
        if($succes)
        {
            $notification = 'Gebruiker is geregistreerd.';
        }
        else
        {
            $notification = 'Registratie is mislukt.';
        }
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
        <h1>Registreer pagina</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">inloggem</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="privacy.php">Privacy</a></li>
        </ul>
    </nav>
    <main>
        <h2>Create an Account</h2>
        <div class="login-container">
            <div class="login-form">
                <h3>Customer Registration</h3>
                <form action="#" method="POST">
                    <label for="register-name">Naam:</label>
                    <input type="text" id="register-name" name="register-name" required>
                    <label for="register-firstname">Firstname:</label>
                    <input type="email" id="register-email" name="register-email" required>
                    <label for="register-password">Wachtwoord:</label>
                    <input type="password" id="register-password" name="register-password" required>
                    <label for="register-confirm-password">Herhaal wachtwoord:</label>
                    <input type="password" id="register-confirm-password" name="register-confirm-password" required>
                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="login.php">Log hier in.</a></p>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>
</html>
