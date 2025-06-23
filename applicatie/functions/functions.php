<?php
include_once 'db_connectie.php';
//eenmalige verbinding met database
$conn = maakVerbinding();

//query die menudata ophaald
function getMenuData($conn)
{

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

function loginUser($conn, $username, $password)
{
    $queryUsers = "SELECT username, password, role FROM [User] WHERE username = :username";
    $stmt = $conn->prepare($queryUsers);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($user);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    return false;

}
function logoutUser()
{
    session_start(); // nodig om sessie te kunnen vernietigen
    session_unset(); // verwijdert alle sessievariabelen
    session_destroy();
    header('Location: index.php');
}

function usernameExists($conn, $username)
{
    $sql = "SELECT COUNT(*) FROM [User] WHERE username = :username";
    $query = $conn->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchColumn() > 0;
}


function registerUser($conn, $username, $password, $firstName, $lastName, $address, $role = 'Client')
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $queryRegister = "INSERT INTO [User] ([username], [password], [first_name], [last_name], [address], [role])
            VALUES (:username, :password, :first_name, :last_name, :address, :role)";
    $stmt = $conn->prepare($queryRegister);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    return $stmt->execute();


}


?>