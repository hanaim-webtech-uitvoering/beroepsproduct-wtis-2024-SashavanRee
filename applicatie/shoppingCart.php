<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

if (!isset($_SESSION['is_logged_in'])) {
    $_SESSION['is_logged_in'] = false;
}

$html_table = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    //adres ophalen uit formulier
    $isGuest = !$_SESSION['username'] ?? false; //check of de gebruiker een gast is
    $clientUsername = $isGuest ? 'gast' : $_SESSION['username'];
    $address = $_POST['address'] ?? '';
    $personnelUsername = 'ayildiz'; // Of kies een medewerker
    $orderDate = date('Y-m-d H:i:s');
    $status = 1;

    if (!empty($_SESSION['cart']) && !empty($address)) {
        //plaatsen van een bestelling
        $completed = placeOrder($conn, $personnelUsername, $clientUsername, $orderDate, $status, $address);
        if ($completed) {
            foreach ($_SESSION['cart'] as $product) {
                addToCart($conn, $product['naam'], $product['aantal']);
            }

            $error = "<p style='color:green'>Bestelling succesvol geplaatst!</p>";
            $_SESSION['cart'] = []; //maakt winkelwagen na bestelling plaatsen leeg
        } else {
            $error = "<p style='color:red'>Er ging iets mis bij het plaatsen van je bestelling.</p>";
        }
    } else {
        $error = "<p style='color:red'>Je winkelwagen is leeg of het adres is niet ingevuld.</p>";
    }
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
        <h1>Winkelwagen</h1>
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
    <h2>Mijn Winkelwagen</h2>
    <?= $error ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Aantal</th>
                <th>Prijs per stuk</th>
                <th>Totaal</th>
            </tr>
            <?php
            $totaal = 0;
            foreach ($_SESSION['cart'] as $product):
                $subtotaal = $product['prijs'] * $product['aantal'];
                $totaal += $subtotaal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($product['naam']) ?></td>
                    <td><?= $product['aantal'] ?></td>
                    <td>€<?= number_format($product['prijs'], 2) ?></td>
                    <td>€<?= number_format($subtotaal, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Totaal:</strong></td>
                <td><strong>€<?= number_format($totaal, 2) ?></strong></td>
            </tr>
        </table>

        <form method="post">
            <label for="address">Bezorgadres:</label><br>
            <input type="text" name="address" id="address" required style="width: 100%;"><br><br>
            <input type="submit" name="place_order" value="Bestelling plaatsen">
        </form>
    <?php else: ?>
        <p>Je winkelwagen is leeg.</p>
    <?php endif; ?>
</main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>

</html>