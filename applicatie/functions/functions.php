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

function getOrderData($conn, $username)
{
    $queryOrders = "SELECT o.order_id, o.datetime, o.status, o.address
                   FROM Pizza_Order o
                   WHERE o.client_name = :username 
                   ORDER BY o.datetime DESC";

    $stmt = $conn->prepare($queryOrders);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetails(PDO $conn, int $orderId, string $username): array
{
    $query = "SELECT o.order_id, o.datetime, o.status, o.address,
                     p.name AS product_name, p.price, op.quantity
              FROM Pizza_Order o
              JOIN Pizza_Order_Product op ON o.order_id = op.order_id
              JOIN Product p ON op.product_name = p.name
              WHERE o.order_id = :order_id AND o.client_name = :username";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderStatusText(int $status): string
{
    switch ($status) {
        case 1:
            return 'In behandeling';
        case 2:
            return 'In de oven';
        case 3:
            return 'onderweg';
        case 4:
            return 'Afgeleverd';
        default:
            return 'Onbekende status';
    }
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    header('Location: index.php');
    exit;
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

function getShoppingcartData($conn)
{
    $queryGetItems = "SELECT p.name AS naam, p.price AS prijs, op.quantity AS aantal
                      FROM Pizza_Order_Product op
                      JOIN Product p ON op.product_name = p.name";

    $stmt = $conn->prepare($queryGetItems);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $items;

}

function addToCart($conn, $productName, $quantity)
{
    $queryAddToCart = "INSERT INTO Pizza_Order_Product (order_id, product_name, quantity)
                        VALUES ((SELECT MAX(order_id) FROM Pizza_Order), :product_name, :quantity)";
    $stmt = $conn->prepare($queryAddToCart);
    $stmt->bindParam(':product_name', $productName, PDO::PARAM_STR);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    return $stmt->execute();
}

function removeFromCart(string $productName): void
{
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['naam'] === $productName) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Herindexeer array
            return;
        }
    }
}

function updateCartQuantity(string $productName, int $newQuantity): void
{
    foreach ($_SESSION['cart'] as $key => &$item) {
        if ($item['naam'] === $productName) {
            if ($newQuantity > 0) {
                $item['aantal'] = $newQuantity;
            } else {
                unset($_SESSION['cart'][$key]);
            }
            break;
        }
    }
    $_SESSION['cart'] = array_values(array_filter($_SESSION['cart']));
}

function placeOrder($conn, $personnelUsername, $clientUsername, $orderDate, $status, $address)
{
    $queryPlaceOrder = "INSERT INTO Pizza_Order (client_name, personnel_username, datetime, status, address)
                        VALUES (:client_name, :personnel_username, :datetime, :status, :address)";

    $stmt = $conn->prepare($queryPlaceOrder);
    $stmt->bindParam(':client_name', $clientUsername, PDO::PARAM_STR);
    $stmt->bindParam(':personnel_username', $personnelUsername, PDO::PARAM_STR);
    $stmt->bindParam(':datetime', $orderDate, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    return $stmt->execute();
}

function getUserAddress($conn, $username)
{
    $queryGetAddress = "SELECT address FROM [User] WHERE username = :username";

    $stmt = $conn->prepare($queryGetAddress);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user['address'] ?? '';
}

function getAllActiveOrders($conn)
{
    $queryGetActiveOrders = "SELECT o.order_id, o.datetime, o.status, o.address, o.client_name, o.personnel_username
                     FROM Pizza_Order o
                     WHERE o.status < 4
                     ORDER BY o.datetime DESC";

    $stmt = $conn->prepare($queryGetActiveOrders);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetailsPersonnel($conn, $orderId)
{
    $queryGetOrderDetails = "SELECT o.order_id, o.datetime, o.status, o.address,
                     p.name AS product_name, p.price, op.quantity
              FROM Pizza_Order o
              JOIN Pizza_Order_Product op ON o.order_id = op.order_id
              JOIN Product p ON op.product_name = p.name
              WHERE o.order_id = :order_id";

    $stmt = $conn->prepare($queryGetOrderDetails);
    $stmt->bindParam(':order_id', $orderId, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateStatusOrder($conn, $orderId, $status)
{
    $queryUpdateOrderStatus = "UPDATE Pizza_Order
                               SET status = :status
                               WHERE order_id = :order_id";

    $stmt = $conn->prepare($queryUpdateOrderStatus);
    $stmt->bindparam(':status', $status, PDO::PARAM_INT);
    $stmt->bindparam(':order_id', $orderId, PDO::PARAM_INT);
    $stmt->execute();
}

?>