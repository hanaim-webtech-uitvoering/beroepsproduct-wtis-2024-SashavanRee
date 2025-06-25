<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';


$isGuest = !isset($_SESSION['username']); // check of gebruiker een gast is
$clientUsername = $isGuest ? 'gast' : $_SESSION['username'];
$existingAddress = getUserAddress($conn, $clientUsername);

$html_table = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    //adres ophalen uit formulier
    $submittedAddress = $_POST['address'] ?? '';
    $addressToUse = !empty($submittedAddress) ? $submittedAddress : $existingAddress;
    $personnelUsername = 'ayildiz'; // Of kies een medewerker
    $orderDate = date('Y-m-d H:i:s');
    $status = 1;

    if (!empty($_SESSION['cart']) && !empty($addressToUse)) {
        //plaatsen van een bestelling
        $completed = placeOrder($conn, $personnelUsername, $clientUsername, $orderDate, $status, $addressToUse);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        removeFromCart($_POST['remove_item']);
    }

    if (isset($_POST['update_quantity'], $_POST['product_name'])) {
        $productName = $_POST['product_name'];
        $newQuantity = (int) $_POST['update_quantity'];
        updateCartQuantity($productName, $newQuantity);
    }
}


//Winkelwakentabel opbouw
if (!empty($_SESSION['cart'])) {
    $total = 0;
    $html_table .= "<table>
    <tr>
        <th>Product</th>
        <th>Aantal</th>
        <th>Prijs per stuk</th>
        <th>Totaal</th>
        <th>Actie</th>
    </tr>";
    foreach ($_SESSION['cart'] as $product) {
        $subtotal = $product['prijs'] * $product['aantal'];
        $total += $subtotal;

        $html_table .= "<tr>
        <td>" . htmlspecialchars($product['naam']) . "</td>
        <td>
            <form method='post' style='display:inline-flex; gap:5px; align-items:center;'>
                <input type='number' name='update_quantity' value='{$product['aantal']}' min='1' style='width: 50px;'>
                <input type='hidden' name='product_name' value='" . htmlspecialchars($product['naam']) . "'>
                <button type='submit'>Update</button>
            </form>
        </td>
        <td>€" . number_format($product['prijs'], 2) . "</td>
        <td>€" . number_format($subtotal, 2) . "</td>
        <td>
            <form method='post'>
                <input type='hidden' name='remove_item' value='" . htmlspecialchars($product['naam']) . "'>
                <button type='submit'>Verwijder</button>
            </form>
        </td>";
    }

        $html_table .= "<tr>
        <td colspan='3'><strong>Totaal</strong></td>
        <td colspan='2'><strong>€" . number_format($total, 2) . "</strong></td>
    </tr>
</table>";
    
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
            <?= $html_table ?>
            <form method="post">
                <label for="address">Adres:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($existingAddress) ?>" required>
                <br>
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