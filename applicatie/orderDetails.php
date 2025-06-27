<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

// Controle of de gebruiker is ingelogd
if (!isset($_SESSION['username']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit;
}

$orderId = (int) $_GET['order_id'];
$username = $_SESSION['username'];
$items = getOrderDetails($conn, $orderId, $username);

if (!$items) {
    die("Bestelling niet gevonden of je hebt geen toegang.");
}

$order = $items[0]; // Basisgegevens
$address = htmlspecialchars($order['address']);

// Tabel opbouwen als string
$html_table = '
<table>
    <tr>
    <thead>
        <th>Product</th>
        <th>Prijs</th>
        <th>Aantal</th>
        <th>Subtotaal</th>
        </thead>
    </tr>';

$total = 0;
foreach ($items as $item) {
    $product = htmlspecialchars($item['product_name']);
    $price = number_format($item['price'], 2);
    $quantity = $item['quantity'];
    $subTotal = $item['price'] * $quantity;
    $total += $subTotal;
    $subTotalFormatted = number_format($subTotal, 2);

    $html_table .= "
    <tr>
        <td>$product</td>
        <td>€$price</td>
        <td>$quantity</td>
        <td>€$subTotalFormatted</td>
    </tr>";
}

$html_table .= '
    <tr>
        <td colspan="3"><strong>Totaal</strong></td>
        <td><strong>€' . number_format($total, 2) . '</strong></td>
    </tr>
</table>';
?>


<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Bestelling #<?= $orderId ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Bestelling #<?= $orderId ?></h1>
    </header>
    <main>
        <button onclick="window.history.back()">Terug</button>
        <?= $html_table ?>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage</p>
    </footer>
</body>

</html>