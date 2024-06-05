<?php
include 'db_connection.php';
include 'product.php';

$database = Database::getInstance();
$productModel = new Product($database->getConnection()); 

$products = $productModel->get_available_products();
$productQuantities = $productModel->get_quantity();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказать цветы - Фиалка</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="images/icon.svg" type="image/svg+xml">
</head>
<body>
    <header>
        <div class="headerLeft">
            <img class="logo" src="images/icon.svg" alt="Логотип">
        </div>

        <div class="headerRight">
            <h1>Фиалка</h1>
            <h3>Уютный магазинчик цветов города Перми</h3>
            <p>Адрес: ул. Ленина, 50, Телефон: +7 (342) 123-45-67</p>
            <nav>
                <a href="index.php">Главная</a>
            </nav>
        </div>
    
    </header>
    
    <section id="order-form">
        <h2>Оформить заявку на заказ цветов</h2>
        <form action="process_order.php" method="POST">
            <label for="customerName">Имя:</label>
            <input type="text" id="customerName" name="customerName" required><br>

            <label for="phoneNumber">Телефон:</label>
            <input type="text" id="phoneNumber" name="phoneNumber" required><br>

            <label for="email">Почта:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="address">Адрес:</label>
            <input type="text" id="address" name="address" required><br>

            <div id="productFields">
                <div class="productField">
                    <label for='productID'>Выберите продукт:</label>
                    <select id='productID1' name='productID1' required>
                        <?php
                        foreach ($products as $product) {
                            print_r($product['productID'], $product['quantity']);
                            echo "<option value='" . $product['productID'] . "'>" . $product['name'] . " - " . $product['price'] . " руб. (Доступно к заказу: " . $product['quantity'] . " шт.)</option>";
                        }
                        ?>
                    </select><br>
                    
                    <label for='quantity'>Количество:</label>
                    <input type='number' id='quantity1' name='quantity1' required><br>
                </div>
            </div>

            <button onclick="addProductField()">Еще</button>

            <button type="submit" id="send-zack-button">Отправить заказ</button>
        </form>
    </section>


    <footer>
        <p>Фиалка - Уютный магазинчик цветов города Перми</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sendOrderButton = document.getElementById('send-zack-button');
            var productQuantities = <?php echo json_encode($productQuantities); ?>;

            sendOrderButton.addEventListener('click', function(event) {
                var selectedProducts = {};

                var productFields = document.querySelectorAll('.productField');

                for (var i = 1; i <= productFields.length; i++) {
                    var selectElement = document.getElementById('productID' + i);
                    var selectedProductID = selectElement.options[selectElement.selectedIndex].value;
                    var selectedQuantity = parseInt(document.getElementById('quantity' + i).value);

                    if (selectedProducts[selectedProductID]) {
                        selectedProducts[selectedProductID] += selectedQuantity;
                    } else {
                        selectedProducts[selectedProductID] = selectedQuantity;
                    }
                }


                for (var productId in selectedProducts) {
                    if (selectedProducts[productId]) {
                        if (selectedProducts[productId] > productQuantities[productId]) {
                            alert('Количество продукта превышает доступное количество.');
                            event.preventDefault();
                            return;
                        }
                    }
                }
            });
        });



        let counter = 1;
        function addProductField() {
            let productFieldsDiv = document.getElementById('productFields');
            let productField = document.querySelector('.productField').cloneNode(true);

            counter++;
            
            let newSelect = productField.querySelector('select');
            let newInput = productField.querySelector('input[type="number"]');
            
            newSelect.id = 'productID' + counter;
            newSelect.name = 'productID' + counter;
            
            newInput.id = 'quantity' + counter;
            newInput.name = 'quantity' + counter;
            
            newInput.value = '';
            newSelect.selectedIndex = 0;

            productFieldsDiv.appendChild(productField);
        }
    </script>

</body>
</html>
