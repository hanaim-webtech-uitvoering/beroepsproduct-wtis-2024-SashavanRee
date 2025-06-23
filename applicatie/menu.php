<?php
session_start();
require_once 'Functions/functions.php'; // Zorg dat de functie hier beschikbaar is

$products = getMenuData($conn);
if (empty($products)) {
    return "<p>Geen producten gevonden.</p>";
}

$grouped_products = [];
foreach ($products as $row) {
    $categorie = $row['categorie'];
    $grouped_products[$categorie][] = $row;
}

//per categorie een tabel aanmalen
$html_tables = '';
foreach ($grouped_products as $categorie => $items) {
    $html_tables .= "<h3>{$categorie}</h3>";
    $html_tables .= '<table>';
    $html_tables .= '<tr><th>Naam</th><th>Prijs</th><th>Ingrediënten</th></tr>';

    foreach ($items as $item) {
        $html_tables .= "<tr>
            <td>{$item['naam']}</td>
            <td>{$item['prijs']}</td>
            <td>{$item['ingrediënten']}</td>
        </tr>";
    }

$html_tables .= '</table>';
}

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
        <h1>Pizzeria Sole Machina 🍕</h1>
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
    <h2>Ons Menu</h2>
        <p>Hieronder zijn onze producten te zien.</p>
        <?php
        echo $html_tables
        ?>
    </main>
    <footer>
        <p>&copy; 2025 My Homepage. All rights reserved.</p>
    </footer>
</body>
</html>
