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
$orders = $orderModel->showOrder();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Панель администратора</h1>
            <nav>
                <a href="logout.php">Выйти</a>
            </nav>
        </div>
    </header>

    <section id="requests">
        <div class="request-list">
            <h2>Новые заявки</h2>
            <?php
            if ($orders->num_rows > 0) {
                while ($row = $orders->fetch_assoc()) {
                    echo "<div class='request'>";
                    echo "<h3>Имя: " . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>Телефон: " . htmlspecialchars($row['phoneNumber']) . "</p>";
                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                    echo "<p>Адрес: " . htmlspecialchars($row['address']) . "</p>";
                    echo "<p>Дата заказа: " . htmlspecialchars($row['orderDate']) . "</p>";

                    echo "<form action='order_actions.php' method='POST'>";
                    echo "<input type='hidden' name='orderID' value='" . $row['orderID'] . "'>";
                    echo "<button type='submit' name='action' value='approve'>Утвердить</button>";
                    echo "<button type='submit' name='action' value='delete'>Удалить заявку</button>";
                    echo "</form>";

                    $orderProducts = $orderModel->showOrderProducts($row['orderID']);
                    if ($orderProducts->num_rows > 0) {
                        echo "<h4>Товары в заказе:</h4>";
                        while ($product = $orderProducts->fetch_assoc()) {
                            echo "<div class='product'>";
                            echo "<p>Наименование: " . htmlspecialchars($product['name']) . "</p>";
                            echo "<p>Описание: " . htmlspecialchars($product['description']) . "</p>";
                            echo "<p>Цена: " . htmlspecialchars($product['price']) . " руб.</p>";
                            echo "<p>Количество: " . htmlspecialchars($product['quantity']) . "</p>";
                            $availability = $orderModel->getProductAvailability($product['productID']);
                            echo "<p>Количество в наличии: " . htmlspecialchars($availability) . "</p>";

                            echo "<form action='order_actions.php' method='POST'>";
                            echo "<input type='hidden' name='orderID' value='" . $row['orderID'] . "'>";
                            echo "<input type='hidden' name='productID' value='" . $product['productID'] . "'>";
                            echo "<input type='number' name='quantity' value='" . $product['quantity'] . "' min='1'>";
                            echo "<button type='submit' name='action' value='update_quantity'>Обновить количество</button>";
                            echo "<button type='submit' name='action' value='delete_product'>Удалить товар</button>";
                            echo "</form>";

                            echo "</div>";
                        }
                    } else {
                        echo "<p>Нет товаров в заказе.</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p class='not-found'>Новых заявок нет</p>";
            }
            ?>
        </div>
    </section>



    <footer>
        <p>Фиалка - Уютный магазинчик цветов города Перми</p>
    </footer>
</body>
</html>
