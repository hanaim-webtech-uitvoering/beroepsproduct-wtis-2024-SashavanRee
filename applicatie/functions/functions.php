<?php
include_once 'db_connectie.php';
//eenmalige verbinding met database
$conn = maakVerbinding();

//query die menudata ophaald
function getMenuData($conn) {

    // Producten en ingrediënten ophalen voor dit type
    $queryProducts = "SELECT pt.name AS categorie, p.name AS naam, p.price AS prijs, STRING_AGG(i.name, ', ') AS ingrediënten
FROM Product p 
LEFT JOIN ProductType pt ON pt.name = p.type_id
LEFT JOIN Product_Ingredient pi ON p.name = pi.product_name
LEFT JOIN Ingredient i ON pi.ingredient_name = i.name
GROUP BY pt.name, p.name, p.price";

    $stmt = $conn->prepare($queryProducts);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $products;
}
?>
