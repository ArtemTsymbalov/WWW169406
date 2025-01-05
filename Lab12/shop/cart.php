<?php
include('admin/cart.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_cart'])) {
        // Clear the cart
        $_SESSION['cart'] = [];
        echo "<p>Koszyk został opróżniony.</p>";
    }

    if (isset($_POST['order'])) {
        echo "<p>Dziękujemy za zamówienie! Twoje zamówienie zostało przyjęte.</p>";
        $_SESSION['cart'] = [];
    }

    if (isset($_POST['update_cart'])) {
        // Update the quantity of the product in the cart
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        updateCart($productId, $quantity);
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="Content-Language" content="pl">
    <meta name="Author" content="Artem Tsymbalov">
    <title>Koszyk</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart-style.css">
    <script>
        function updateTotal() {
            let total = 0;
            const items = document.querySelectorAll('.cart-item');
            items.forEach(item => {
                const price = parseFloat(item.querySelector('.cart-item-price').dataset.price.replace(',', ''));
                const quantity = parseInt(item.querySelector('input[name="quantity"]').value);
                total += price * quantity;
            });
            document.getElementById('total-price').innerText = total.toFixed(2) + ' PLN';
        }
    </script>
</head>
<body>
    <div class="cart-container">
        <?php
        if (empty($_SESSION['cart'])) {
            echo "<p>Koszyk jest pusty.</p>";
        } else {
            echo "<h2>Zawartość koszyka:</h2>";
            foreach ($_SESSION['cart'] as $productId => $item) {
                echo '<div class="cart-item">';
                $imageUrl = isset($item['image_url']) ? htmlspecialchars($item['image_url']) : 'default-image.png';
                echo '<img src="' . $imageUrl . '" alt="Product Image">';
                
                echo '<div class="cart-item-details">';
                $title = isset($item['title']) ? htmlspecialchars($item['title']) : 'Unknown Product';
                echo '<h3>' . $title . '</h3>';
                
                echo '<p class="cart-item-price" data-price="' . number_format($item['price'], 2) . '">Cena: ' . number_format($item['price'], 2) . ' PLN</p>';
                echo '<form method="post" action="" oninput="updateTotal()">
                        <input type="hidden" name="product_id" value="' . $productId . '">
                        <input type="number" name="quantity" value="' . $item['quantity'] . '" min="1"> <!-- Quantity selector -->
                      </form>';
                echo '</div>';
                echo '</div>';
            }
            echo "<p class='cart-total'>Łączna wartość: <span id='total-price'>" . number_format(calculateTotal(), 2) . " PLN</span></p>";
        }
        ?>
        
        <!-- Buttons for ordering and clearing the cart -->
        <form method="post" action="">
            <button type="submit" name="order">Zamów</button>
            <button type="submit" name="clear_cart">Opróżnij koszyk</button>
        </form>
    </div>
    <script>
        // Initial total calculation
        updateTotal();
    </script>
</body>
</html>