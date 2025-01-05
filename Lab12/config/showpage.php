<?php
/**
 * Page Display Module
 * 
 * This file contains functions responsible for displaying page content from the database.
 * Used by the main index.php to show dynamic content based on page ID.
 */

/**
 * Displays page content based on its identifier
 * 
 * @param string $id Page identifier (title)
 * @param mysqli $link Database connection
 * @return string Page content or error message
 */
function show_page($id, $link) {
    // Protect against SQL Injection
    $id_clear = htmlspecialchars($id);
    
    // Fetch page content from database with LIMIT 1 for optimization
    if ($id_clear === 'cart') {
        ob_start();
        include('shop/cart.php'); // Include the cart page
        return ob_get_clean();
    }

    $query = "SELECT * FROM page_list WHERE page_title='$id_clear' LIMIT 1;";
    $result = mysqli_query($link, $query);

    $row = mysqli_fetch_array($result);

    if ($id_clear === 'shop') {
        ob_start();
        include('shop/shop.php');
        return ob_get_clean();
    }

    if (empty($row)) {
        $web = '<h1>Page not found</h1>';
    } else {
        $web = $row['page_content'];
    }
    
    return $web;
}