<?php
include 'db_connection.php';

$database = Database::getInstance();
$mysqli = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $mysqli->prepare("INSERT INTO customers (name, phoneNumber, email, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $customerName, $phoneNumber, $email, $address);
    $stmt->execute();
    $customerID = $stmt->insert_id;
    $stmt->close();

    date_default_timezone_set('Europe/Moscow');
    $orderDate = date('Y-m-d H:i:s');
    $stmt = $mysqli->prepare("INSERT INTO orders (customerID, orderDate, status) VALUES (?, ?, 'Подана')");
    $stmt->bind_param("is", $customerID, $orderDate);
    $stmt->execute();
    $orderID = $stmt->insert_id;
    $stmt->close();

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'productID') !== false) {
            $productID = $value;
            $quantityKey = 'quantity' . substr($key, -1);
            $quantity = $_POST[$quantityKey];

            $stmt = $mysqli->prepare("INSERT INTO order_items (orderID, productID, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $orderID, $productID, $quantity);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: order_success.html');
    exit();
}
?>