<?php
require_once 'db_connectie.php';

$melding = '';  // nog niks te melden

// check voor de knop
if(isset($_POST['registeren'])) {
    $fouten = [];
    // 1. inlezen gegevens uit form
    $naam       = $_POST['naam'];
    $wachtwoord = $_POST['wachtwoord'];

    // 2. controleren van de gegevens
    if(strlen($naam) < 4) {
        $fouten[] = 'Gebruikersnaam minstens 4 karakters.';
    }

    if(strlen($wachtwoord) < 8) {
        $fouten[] = 'Wachtwoord minstens 8 karakters.';
    }

    // 3. opslaan van de gegevens
    if(count($fouten) > 0) {
        $melding = "Er waren fouten in de invoer.<ul>";
        foreach($fouten as $fout) {
            $melding .= "<li>$fout</li>";
        }
        $melding .= "</ul>";

    } else {
        // Hash the password
        $passwordhash = password_hash($wachtwoord, PASSWORD_DEFAULT);
        
        // database
        $db = maakVerbinding();
        // Insert query (prepared statement)
        $sql = 'INSERT INTO Gebruikers(naam, passwordhash)
                values (:naam, :passwordhash)';
        $query = $db->prepare($sql);

        // Send data to database
        $data_array = [
            'naam' => $naam,
            'passwordhash' => $passwordhash
        ];
        $succes = $query->execute($data_array);

        // Check results
        if($succes)
        {
            $melding = 'Gebruiker is geregistreerd.';
        }
        else
        {
            $melding = 'Registratie is mislukt.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Login pagina</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">inloggen</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="privacy.php">Privacy</a></li>
        </ul>
    </nav>
    <main>
        <h2>Login</h2>
        <div class="login-container">
            <div class="login-form">
                <h3>Klanten Login</h3>
                <form action="" method="POST">
                    <label for="client-username">Gebruikersnaam:</label>
                    <input type="text" id="client-username" name="client-username" required>
                    <label for="customer-password">Wachtwoord:</label>
                    <input type="password" id="client-password" name="client-password" required>
                    <button type="submit">Inloggen</button>
                </form>
                <?=$melding?>
                <p>Nog geen account? <a href="register.php">Registreer hier</a></p>
            </div>
            <div class="login-form">
                <h3>Medewerkers Login</h3>
                <form action="" method="POST">
                    <label for="staff-username">Gebruikersnaam:</label>
                    <input type="text" id="staff-username" name="staff-username" required>
                    <label for="staff-password">Wachtwoord:</label>
                    <input type="password" id="staff-password" name="staff-password" required>
                    <button type="submit">Inloggen</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>
</html>
