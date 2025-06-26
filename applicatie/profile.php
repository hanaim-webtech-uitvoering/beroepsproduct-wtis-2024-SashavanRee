<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$orders = getOrderData($conn, $username);


// Groepeer per order_id (zonder de producten te tonen)
$grouped_orders = [];
foreach ($orders as $order) {
    $orderId = $order['order_id'];
    if (!isset($grouped_orders[$orderId])) {
        $grouped_orders[$orderId] = [
            'datetime' => $order['datetime'],
            'address' => $order['address'],
            'status' => $order['status']
        ];
    }
}

// Opbouw van de HTML-tabel
$html_tables = '';
if (empty($grouped_orders)) {
    $html_tables = '<p>U heeft nog geen bestellingen geplaatst.</p>';
} else {
    $html_tables .= "<table>
                        <tr>
                        <thead>
                            <th>Bestelnummer</th>
                            <th>Datum</th>
                            <th>Adres</th>
                            <th>Status</th>
                            <th>Actie</th>
                            </thead>
                        </tr>";
    foreach ($grouped_orders as $orderId => $data) {
        $statusText = getOrderStatusText((int) $order['status']);
        $html_tables .= "<tr>
                            <td>#{$orderId}</td>
                            <td>{$data['datetime']}</td>
                            <td>{$data['address']}</td>
                            <td>{$statusText}</td>
                            <td><a href='orderDetails.php?order_id={$orderId}'>Bekijk details</a></td>
                        </tr>";
    }
    $html_tables .= "</table>";
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
        <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
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
        <h2>Mijn bestellingen</h2>
        <?= $html_tables; ?>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>

</html>