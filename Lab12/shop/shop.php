<?php
/**
 * Shop Page
 * 
 * This file displays the shop content.
 */

include('cfg.php');
include('admin/cart.php'); // Include the cart functions

// Fetch products from the database (assuming you have a products table)
$query = "SELECT * FROM products";
$result = mysqli_query($link, $query);

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="Content-Language" content="pl">
    <meta name="Author" content="Artem Tsymbalov">
    <title>Sklep</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/shop-style.css">
</head>
<body>
    <h1>Witamy w naszym sklepie!</h1>
    <div class="products">
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($product = mysqli_fetch_assoc($result)) {
                echo '<div class="product-item">';
                echo '<h2>' . htmlspecialchars($product['title']) . '</h2>';
                echo '<img src="' . htmlspecialchars($product['image_url']) . '" alt="' . htmlspecialchars($product['title']) . '">';
                echo '<p>' . htmlspecialchars($product['description']) . '</p>';
                echo '<p>Cena: ' . number_format($product['net_price'], 2) . ' PLN</p>';
                echo '<form method="post" action="">
                        <input type="hidden" name="product_id" value="' . $product['id'] . '">
                        <input type="hidden" name="product_price" value="' . $product['net_price'] . '">
                        <input type="hidden" name="product_title" value="' . htmlspecialchars($product['title']) . '">
                        <input type="hidden" name="product_image" value="' . htmlspecialchars($product['image_url']) . '">
                        <input type="hidden" name="quantity" value="1">
                        <input type="submit" name="add_to_cart" value="Dodaj do koszyka">
                      </form>';
                echo '</div>';
            }
        } else {
            echo '<p>Brak produktów w sklepie.</p>';
        }
        ?>
    </div>

    <!-- Handle adding to cart -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $productId = (int)$_POST['product_id'];
        $price = (float)$_POST['product_price'];
        $quantity = 1;
        $title = $_POST['product_title'];
        $imageUrl = $_POST['product_image'];
        addToCart($productId, $price, $quantity, $title, $imageUrl);
        echo "<p>Produkt dodany do koszyka!</p>";
    }
    ?>
</body>
</html> 