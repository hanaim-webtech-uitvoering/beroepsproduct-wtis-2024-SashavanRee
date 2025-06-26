<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (loginUser($conn, $username, $password)) {
        $role = $_SESSION['role'];

        if ($role === 'Personnel') {
            header("Location: orderViewPersonnel.php");
        } else if ($role === 'Client') {
            header("Location: profile.php");
        }
        exit;
    } else {
        $error = "Ongeldige gebruikersnaam of wachtwoord.";
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
        <li><a href="menu.php">Menu</a></li>
        <li><a href="privacy.php">Privacy</a></li>

        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="profile.php">Profiel</a></li>
        <?php else: ?>
            <li><a href="login.php">Inloggen</a></li>
        <?php endif; ?>
    </ul>
</nav>
    <main>
        <h2>Login</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="login-container">
            <div class="login-form">
                <h3>Klanten Login</h3>
                <form action="" method="POST">
                    <label for="client-username">Gebruikersnaam:</label>
                    <input type="text" id="username" name="username" required>
                    <label for="customer-password">Wachtwoord:</label>
                    <input type="password" id="client-password" name="password" required>
                    <button type="submit">Inloggen</button>
                </form>
                <p>Nog geen account? <a href="register.php">Registreer hier</a></p>
            </div>
            <div class="login-form">
                <h3>Medewerkers Login</h3>
                <form action="" method="POST">
                    <label for="staff-username">Gebruikersnaam:</label>
                    <input type="text" id="staff-username" name="username" required>
                    <label for="staff-password">Wachtwoord:</label>
                    <input type="password" id="staff-password" name="password" required>
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