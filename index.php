<?php
include 'db_connection.php';
include 'Product.php';

$products_per_page = 12;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

$productModel = new Product($mysqli);

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$minPrice = isset($_GET['price-min']) ? trim($_GET['price-min']) : null;
$maxPrice = isset($_GET['price-max']) ? trim($_GET['price-max']) : null;
$sortOrder = isset($_GET['sort-order']) ? $_GET['sort-order'] : 'asc';
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';

$total_products = $productModel->countProducts($searchTerm, $availability, $minPrice, $maxPrice);
$products = $productModel->searchProducts($products_per_page, $offset, $searchTerm, $sortOrder, $availability, $minPrice, $maxPrice);

$min_price = $productModel->getMinPrice();
$max_price = $productModel->getMaxPrice();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фиалка - уютный магазинчик цветов</title>
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
                <a href="order.html">Заказать цветы</a>
            </nav>
        </div>
    
    </header>
    
    <section class="search-section">
        <form action="index.php" method="get">
            <div class="form-group">
                <input type="text" name="search" id="search" placeholder="Поиск цветов..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="price-min">Цена от:</label>
                <input type="number" name="price-min" id="price-min" placeholder="<?php echo $min_price ?>" value="<?php echo isset($_GET['price-min']) ? htmlspecialchars($_GET['price-min']) : ''; ?>">
                
                <label for="price-max">Цена до:</label>
                <input type="number" name="price-max" id="price-max" placeholder="<?php echo $max_price; ?>" value="<?php echo isset($_GET['price-max']) ? htmlspecialchars($_GET['price-max']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="sort-order">Сортировать по:</label>
                <select name="sort-order" id="sort-order">
                    <option value="asc" <?php echo (isset($_GET['sort-order']) && $_GET['sort-order'] === 'asc') ? 'selected' : ''; ?>>Возрастанию цены</option>
                    <option value="desc" <?php echo (isset($_GET['sort-order']) && $_GET['sort-order'] === 'desc') ? 'selected' : ''; ?>>Убыванию цены</option>
                </select>

                
                <label for="availability">Наличие:</label>
                <select name="availability" id="availability">
                    <option value="all" <?php echo (isset($_GET['availability']) && $_GET['availability'] === 'all') ? 'selected' : ''; ?>>Все</option>
                    <option value="in-stock" <?php echo (isset($_GET['availability']) && $_GET['availability'] === 'in-stock') ? 'selected' : ''; ?>>В наличии</option>
                </select>

            </div>
            
            <div class="form-group buttons">
                <button type="submit" id="apply-button">Применить</button>
                <button type="button" id="reset-button" onclick="window.location.href='index.php'">Сбросить</button>
            </div>
        </form>
    </section>



    
    <section class="products-section">
        <h2>Товары</h2>
        <div id="product-list" class="product-list">
            <?php
            if ($products->num_rows > 0) {
                while ($row = $products->fetch_assoc()) {
                    echo "<div class='product-card'>";
                    echo "<img src='" . $row['imageURL'] . "' alt='" . $row['name'] . "'>";
                    echo "<h3>" . $row['name'] . "</h3>";
                    echo "<p>" . $row['description'] . "</p>";
                    echo "<p class='price'>Цена: " . $row['price'] . " руб.</p>";
                    echo "<p>Количество в наличии: " . $row['quantity'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='not-found'>Товары не найдены</p>";
            }
            ?>
        </div>
    </section>


    <section class="pagination">
        <?php
        $total_pages = ceil($total_products / $products_per_page);

        if ($total_pages > 1) {
            $base_url = $_SERVER['PHP_SELF'] . "?";

            $queryParams = array(
                'search' => $searchTerm,
                'price-min' => $minPrice,
                'price-max' => $maxPrice,
                'sort-order' => $sortOrder,
                'availability' => $availability
            );

            foreach ($queryParams as $key => $value) {
                if (!empty($value)) {
                    $base_url .= "$key=$value&";
                }
            }

            $base_url .= "page=";

            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='{$base_url}$i'" . ($i == $page ? " class='active'" : "") . ">$i</a> ";
            }
        }
        ?>
    </section>

    

    <footer>
        <p>Фиалка - Уютный магазинчик цветов города Перми</p>
    </footer>

    <script src="script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var applyButton = document.getElementById('apply-button');
        var priceMinInput = document.getElementById('price-min');
        var priceMaxInput = document.getElementById('price-max');
        var minPrice = <?php echo $min_price; ?>;
        var maxPrice = <?php echo $max_price; ?>;

        applyButton.addEventListener('click', function(event) {
            if (parseInt(priceMinInput.value) < minPrice) {
                alert('Нельзя ввести значение меньше минимальной цены!');
                priceMinInput.value = minPrice;
                event.preventDefault(); 
            }

            if (parseInt(priceMaxInput.value) > maxPrice) {
                alert('Нельзя ввести значение больше максимальной цены!');
                priceMaxInput.value = maxPrice;
                event.preventDefault(); 
            }
            });
        });
    </script>

</body>
</html>
