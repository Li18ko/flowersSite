<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin.html");
    exit();
}

include '../../db_connection.php';
include 'order.php';

$database = Database::getInstance();
$orderModel = new Order($database->getConnection()); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $orderID = $_POST['orderID'];

    switch ($action) {
        case 'approve':
            $orderModel->approveOrder($orderID);
            break;
        
        case 'delete':
            $orderModel->deleteOrder($orderID);
            break;

        case 'update_quantity':
            $productID = $_POST['productID'];
            $quantity = $_POST['quantity'];
            $orderModel->updateOrderProductQuantity($orderID, $productID, $quantity);
            break;

        case 'delete_product':
            $productID = $_POST['productID'];
            $orderModel->deleteOrderProduct($orderID, $productID);
            break;

        default:
            break;
    }
}

header("Location: admin.php");
exit();
?>
