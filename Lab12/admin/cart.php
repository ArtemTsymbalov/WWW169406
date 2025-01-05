<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Add product to cart
 * 
 * @param int $productId
 * @param float $price
 * @param int $quantity
 * @param string $title
 * @param string $imageUrl
 */
function addToCart($productId, $price, $quantity, $title, $imageUrl) {
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'price' => $price,
            'quantity' => $quantity,
            'title' => $title,
            'image_url' => $imageUrl
        ];
    }
}

/**
 * Remove product from cart
 * 
 * @param int $productId
 */
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

/**
 * Update product quantity in cart
 * 
 * @param int $productId
 * @param int $quantity
 */
function updateCart($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity <= 0) {
            removeFromCart($productId);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }
}

/**
 * Show cart contents
 * 
 * @return array
 */
function showCart() {
    return $_SESSION['cart'];
}

/**
 * Calculate total value of the cart
 * 
 * @return float
 */
function calculateTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}
?> 