<?php
session_start();
require_once 'db_connectie.php';
require_once 'functions/functions.php';

//Controle of de gebruiker de juiste rol heeft
// Dit is een beveiligingsmaatregel om ervoor te zorgen dat alleen personeel toegang heeft tot deze pagina
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Personnel') {
    header("Location: unauthorized.php");
    exit;
}

//checken of de gebruiker is ingelogd
// Dit voorkomt dat niet-ingelogde gebruikers deze pagina kunnen bekijken
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$personnelUsername = $_SESSION['username'];
$error = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = (int) $_POST['order_id'];
    $newStatus = (int) $_POST['new_status'];

    try {
        updateStatusOrder($conn, $orderId, $newStatus);
        $error = " Status succesvol bijgewerkt voor bestelling #{$orderId}.";
    } catch (Exception $e) {
        $error = "Er is een fout opgetreden bij het bijwerken van de status.";
    }
}

$orders = getAllActiveOrders($conn);

// Functie om statusopties als <option> op te bouwen
function buildStatusDropdown($selectedStatus)
{
    $statuses = [
        1 => 'In behandeling',
        2 => 'In de oven',
        3 => 'Onderweg',
        4 => 'Afgeleverd'
    ];

    $html = '';
    foreach ($statuses as $value => $label) {
        $selected = ($value == $selectedStatus) ? 'selected' : '';
        $html .= "<option value=\"$value\" $selected>$label</option>";
    }
    return $html;
}

// Tabelopbouw
$html_table = '';

if (empty($orders)) {
    $html_table = '<p>Er zijn momenteel geen actieve bestellingen.</p>';
} else {
    $html_table .= '<table>
                        <thead>
                            <tr>
                                <th>Bestelnummer</th>
                                <th>Klant</th>
                                <th>Personeelslid</th>
                                <th>Datum</th>
                                <th>Adres</th>
                                <th>Status</th>
                                <th>Actie</th>
                            </tr>
                        </thead>
                        <tbody>';
    foreach ($orders as $order) {
        $html_table .= '<tr>
            <form method="POST">
                <td>#' . htmlspecialchars($order['order_id']) . '</td>
                <td>' . htmlspecialchars($order['client_name']) . '</td>
                <td>' . htmlspecialchars($order['personnel_username']) . '</td>
                <td>' . htmlspecialchars($order['datetime']) . '</td>
                <td>' . htmlspecialchars($order['address']) . '</td>
                <td>
                    <select name="new_status">
                        ' . buildStatusDropdown((int) $order['status']) . '
                    </select>
                </td>
                <td>
                    <input type="hidden" name="order_id" value="' . (int) $order['order_id'] . '">
                    <button type="submit">Opslaan</button>
                    <a href="orderDetailsPersonnel.php?order_id=' . urlencode($order['order_id']) . '">Details</a>
                </td>
            </form>
        </tr>';
    }
    $html_table .= '</tbody></table>';
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

            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="profile.php">Profiel</a></li>
                <li><a href="logout.php">Uitloggen</a></li>
            <?php else: ?>
                <li><a href="login.php">Inloggen</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <h2>Bestellingsoverzicht</h2>
        <?php if ($error): ?>
        <p style="color: <?= str_starts_with($error, 'Status succesvol') ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

        <?= $html_table; ?>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>

</html>