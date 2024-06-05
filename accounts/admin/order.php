<?php
class Order
{
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function showOrder() {
        $sql = "SELECT customers.name, customers.phoneNumber, customers.email, customers.address, orders.orderID, orders.orderDate FROM customers
                JOIN orders ON customers.customerID = orders.customerID WHERE orders.status='Подана' ORDER BY orders.orderDate DESC";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function showOrderProducts($orderID){
        $sql = "SELECT products.productID, products.name, products.imageURL, products.description, products.price, order_items.quantity 
                FROM products 
                JOIN order_items ON order_items.productID = products.productID
                WHERE order_items.orderID = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function approveOrder($orderID) {
        $sql = "UPDATE orders SET status='Подтверждена' WHERE orderID=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();

        $sql = "UPDATE stocks s
                JOIN order_items oi ON s.productID = oi.productID
                SET s.quantity = s.quantity - oi.quantity
                WHERE oi.orderID = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
    }

    public function deleteOrder($orderID) {
        $sql = "DELETE FROM orders WHERE orderID=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
    }

    public function deleteOrderProduct($orderID, $productID) {
        $sql = "DELETE FROM order_items WHERE orderID=? AND productID=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $orderID, $productID);
        $stmt->execute();
    }

    public function updateOrderProductQuantity($orderID, $productID, $quantity) {
        $sql = "UPDATE order_items SET quantity=? WHERE orderID=? AND productID=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("iii", $quantity, $orderID, $productID);
        $stmt->execute();
    }

    public function getProductAvailability($productID) {
        $sql = "SELECT quantity FROM stocks WHERE productID = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['quantity'];
        } else {
            return 0;
        }
    }
    
}
?>
