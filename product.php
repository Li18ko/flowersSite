<?php
class Product
{
    private static $instance; 
    private $mysqli;

    private function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public static function getInstance($mysqli)
    {
        if (!isset(self::$instance)) {
            self::$instance = new Product($mysqli);
        }
        return self::$instance;
    }
    public function searchProducts($limit, $offset, $searchTerm, $sortOrder, $availability, $minPrice, $maxPrice)
    {
        $bindParams = array();
        $bindTypes = '';

        $sql = "SELECT products.name, products.imageURL, products.description, products.price, stocks.quantity 
                FROM products 
                JOIN stocks ON products.productID = stocks.productID";

        $conditions = array();

        if (!empty($searchTerm)) {
            $conditions[] = "(LOWER(products.name) LIKE ? OR LOWER(products.description) LIKE ?)";
            $searchTerm = strtolower($searchTerm);
            $bindParams[] = "%$searchTerm%";
            $bindParams[] = "%$searchTerm%";
            $bindTypes .= "ss";
        }

        if (!empty($minPrice)) {
            $conditions[] = "products.price >= ?";
            $bindParams[] = $minPrice;
            $bindTypes .= "d";
        }

        if (!empty($maxPrice)) {
            $conditions[] = "products.price <= ?";
            $bindParams[] = $maxPrice;
            $bindTypes .= "d";
        }

        if ($availability === 'in-stock') {
            $conditions[] = "stocks.quantity > 0";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($sortOrder === 'asc') {
            $sql .= " ORDER BY products.price ASC";
        } elseif ($sortOrder === 'desc') {
            $sql .= " ORDER BY products.price DESC";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $bindParams[] = $limit;
        $bindParams[] = $offset;
        $bindTypes .= "ii";

        $stmt = $this->mysqli->prepare($sql);

        if (!empty($bindParams)) {
            $stmt->bind_param($bindTypes, ...$bindParams);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    public function countProducts($searchTerm, $availability, $minPrice, $maxPrice)
    {
        $bindParams = array();
        $bindTypes = '';

        $sql = "SELECT COUNT(*) as total FROM products JOIN stocks ON products.productID = stocks.productID";

        $conditions = array();

        if (!empty($searchTerm)) {
            $conditions[] = "(LOWER(products.name) LIKE ? OR LOWER(products.description) LIKE ?)";
            $searchTerm = strtolower($searchTerm);
            $bindParams[] = "%$searchTerm%";
            $bindParams[] = "%$searchTerm%";
            $bindTypes .= "ss";
        }

        if (!empty($minPrice)) {
            $conditions[] = "products.price >= ?";
            $bindParams[] = $minPrice;
            $bindTypes .= "d";
        }

        if (!empty($maxPrice)) {
            $conditions[] = "products.price <= ?";
            $bindParams[] = $maxPrice;
            $bindTypes .= "d";
        }

        if ($availability === 'in-stock') {
            $conditions[] = "stocks.quantity > 0";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->mysqli->prepare($sql);

        if (!empty($bindParams)) {
            $stmt->bind_param($bindTypes, ...$bindParams);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'];
    }

    public function getMinPrice()
    {
        $result = $this->mysqli->query("SELECT MIN(price) as min_price FROM products");
        $row = $result->fetch_assoc();

        return $row['min_price'];
    }

    public function getMaxPrice()
    {
        $result = $this->mysqli->query("SELECT MAX(price) as max_price FROM products");
        $row = $result->fetch_assoc();

        return $row['max_price'];
    }

    public function get_available_products(){
        $result = $this->mysqli->query("SELECT products.productID, products.name, products.price, stocks.quantity 
        FROM products JOIN stocks ON products.productID = stocks.productID WHERE stocks.quantity > 0");

        return $result;
    }

    public function get_quantity(){
        $productQuantities = array();
        $result = $this->mysqli->query("SELECT products.productID, stocks.quantity FROM products JOIN stocks ON products.productID = stocks.productID WHERE stocks.quantity > 0");
        while ($row = mysqli_fetch_assoc($result)) {
            $productQuantities[$row['productID']] = $row['quantity'];
        }
        return $productQuantities;
    }

}
?>
